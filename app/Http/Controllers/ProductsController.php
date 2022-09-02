<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Stock;
use App\Models\Period;
use App\Models\Plantel;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductsCreateRequest;
use App\Http\Requests\ProductsUpdateRequest;

class ProductsController extends Controller
{
    public function getPermissions(){
        $permissions['productsIndex']=Auth::user()->hasPermissionTo('products.index');
        $permissions['productsCreate']=Auth::user()->hasPermissionTo('products.create');
        $permissions['productsStore']=Auth::user()->hasPermissionTo('products.store');
        $permissions['productsEdit']=Auth::user()->hasPermissionTo('products.edit');
        $permissions['productsUpdate']=Auth::user()->hasPermissionTo('products.update');
        $permissions['productsShow']=Auth::user()->hasPermissionTo('products.show');
        $permissions['productsDestroy']=Auth::user()->hasPermissionTo('products.destroy');
        //dd($permissions);
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
        $products=Product::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($product)=>[
            'id'=>$product->id,
            'name'=>$product->name
        ]);
        //dd($users);

        return Inertia::render('Products/Index',[
            'products'=>$products,
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
        $books=Product::where('bnd_ofertable',0)->get()->map(fn ($product) => [
            'value' => $product->id,
            'label' => $product->name,
        ]);
        $books->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        $periods=Period::all()->map(fn ($period) => [
            'value' => $period->id,
            'label' => $period->name,
        ]);
        $periods->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        ///dd($products);
        return Inertia::render('Products/Create',['books'=>$books,'periods'=>$periods]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $product=Product::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('products.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product=Product::select('products.id','products.name','products.costo','products.precio','products.bnd_activo',
        'products.bnd_ofertable','book.name as libro','p.name as periodo')
        ->leftJoin('products as book','book.id','products.product_id')
        ->leftJoin('periods as p','p.id','products.period_id')
        ->findOrfail($id);
        $this->crearStockInicial($id);
        $stock=Stock::select('stocks.id','pla.name as plantel','stocks.current_stock')
        ->where('product_id',$id)
        ->join('plantels as pla','pla.id','plantel_id')
        ->get();
        //dd($product);
        
        return Inertia::render('Products/Show', ['product'=>$product,'stocks'=>$stock]);
    }

    public function crearStockInicial($product_id){
        $plantels=Plantel::all();
        foreach($plantels as $plantel){
            $buscar=Stock::where('plantel_id',$plantel->id)->where('product_id',$product_id)->first();
            if(is_null($buscar)){
                $input['product_id']=$product_id;
                $input['plantel_id']=$plantel->id;
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
        $product=Product::findOrfail($id);
        $books=Product::where('bnd_ofertable',0)->get()->map(fn ($product) => [
            'value' => $product->id,
            'label' => $product->name,
        ]);
        $books->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        $periods=Period::all()->map(fn ($period) => [
            'value' => $period->id,
            'label' => $period->name,
        ]);
        $periods->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        return Inertia::render('Products/Edit', ['product'=>$product, 'books'=>$books,'periods'=>$periods]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $product=Product::findOrFail($id);
            $product->name=$datos['name'];
            $product->costo=$datos['costo'];
            $product->precio=$datos['precio'];
            if(!isset($datos['bnd_activo'])){
                $datos['bnd_activo']=0;
            }
            $product->bnd_activo=$datos['bnd_activo'];
            
            if(!isset($datos['bnd_ofertable'])){
                $datos['bnd_ofertable']=0;
            }
            $product->bnd_ofertable=$datos['bnd_ofertable'];
            $product->product_id=$datos['product_id'];
            $product->period_id=$datos['period_id'];
            $product->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('products.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product=Product::findOrFail($id);
        //dd($user);
        try{
            $product->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('products.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function findById(Request $request){
        //dd($request->all());
        $producto=Product::find($request->input('producto'));
        return response($producto, 200);
    }

}
