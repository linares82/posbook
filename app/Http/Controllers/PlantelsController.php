<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Stock;
use App\Models\Plantel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PlantelsCreateRequest;
use App\Http\Requests\PlantelsUpdateRequest;

class PlantelsController extends Controller
{
    public function getPermissions(){
        $permissions['plantelsIndex']=Auth::user()->hasPermissionTo('plantels.index');
        $permissions['plantelsCreate']=Auth::user()->hasPermissionTo('plantels.create');
        $permissions['plantelsStore']=Auth::user()->hasPermissionTo('plantels.store');
        $permissions['plantelsEdit']=Auth::user()->hasPermissionTo('plantels.edit');
        $permissions['plantelsUpdate']=Auth::user()->hasPermissionTo('plantels.update');
        $permissions['plantelsShow']=Auth::user()->hasPermissionTo('plantels.show');
        $permissions['plantelsDestroy']=Auth::user()->hasPermissionTo('plantels.destroy');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $sysMessage=$request->session()->get('sysMessage');
        $plantels=Plantel::query()
        ->when($request->input('name'), function($query, $name){
            $query->where('name','like','%'.$name.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($plantel)=>[
            'id'=>$plantel->id,
            'name'=>$plantel->name,
            'address'=>$plantel->address,
            'phone'=>$plantel->phone,
            'director'=>$plantel->director
        ]);
        //dd($plantels);

        return Inertia::render('Plantels/Index',[
            'plantels'=>$plantels,
            'filters'=>$request->only(['name']),
            'sysMessage'=>$sysMessage,
            'permissions'=>$this->getPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Plantels/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlantelsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $plantel=Plantel::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('plantels.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $plantel=Plantel::findOrfail($id);
        $this->crearStockInicial($id);

        $stocks=Stock::select('stocks.id','p.name as producto','stocks.current_stock')
        ->join('products as p','p.id','stocks.product_id')
        ->where('plantel_id',$id)
        ->get();
        //dd($stocks);
        return Inertia::render('Plantels/Show', ['plantel'=>$plantel,'stocks'=>$stocks]);
    }

    public function crearStockInicial($plantel_id){
        $products=Product::all();
        foreach($products as $product){
            $buscar=Stock::where('plantel_id',$plantel_id)->where('product_id',$product->id)->first();
            if(is_null($buscar)){
                $input['product_id']=$product->id;
                $input['plantel_id']=$plantel_id;
                $input['current_stock']=0;
                Stock::create($input);
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $plantel=Plantel::findOrfail($id);
        return Inertia::render('Plantels/Edit', ['plantel'=>$plantel]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PlantelsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $plantel=Plantel::findOrFail($id);
            $plantel->name=$datos['name'];
            $plantel->address=$datos['address'];
            $plantel->phone=$datos['phone'];
            $plantel->director=$datos['director'];
            $plantel->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('plantels.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $plantel=Plantel::findOrFail($id);
        //dd($user);
        try{
            $plantel->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('plantels.index')->with('sysMessage', 'Registro Borrado.');
    }

}
