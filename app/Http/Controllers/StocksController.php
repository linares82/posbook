<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StocksCreateRequest;
use App\Http\Requests\StocksUpdateRequest;

class StocksController extends Controller
{
    public function getPermissions(){
        $permissions['stocksIndex']=Auth::user()->hasPermissionTo('stocks.index');
        $permissions['stocksCreate']=Auth::user()->hasPermissionTo('stocks.create');
        $permissions['stocksStore']=Auth::user()->hasPermissionTo('stocks.store');
        $permissions['stocksEdit']=Auth::user()->hasPermissionTo('stocks.edit');
        $permissions['stocksUpdate']=Auth::user()->hasPermissionTo('stocks.update');
        $permissions['stocksShow']=Auth::user()->hasPermissionTo('stocks.show');
        $permissions['stocksDestroy']=Auth::user()->hasPermissionTo('stocks.destroy');
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
        $stocks=Stock::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($stock)=>[
            'id'=>$stock->id,
            'name'=>$stock->name
        ]);
        //dd($users);

        return Inertia::render('Stocks/Index',[
            'stocks'=>$stocks,
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
        return Inertia::render('Stocks/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StocksCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stock=Stock::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stocks.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stock=Stock::findOrfail($id);
        
        return Inertia::render('Stocks/Show', ['stock'=>$stock]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stock=Stock::findOrfail($id);
        return Inertia::render('Stocks/Edit', ['stock'=>$stock]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StocksUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stock=Stock::findOrFail($id);
            $stock->current_stock=$datos['current_stock'];
            $stock->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stocks.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stock=Stock::findOrFail($id);
        //dd($user);
        try{
            $stock->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('stocks.index')->with('sysMessage', 'Registro Borrado.');
    }

}
