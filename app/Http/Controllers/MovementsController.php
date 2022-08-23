<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MovementsCreateRequest;
use App\Http\Requests\MovementsUpdateRequest;

class MovementsController extends Controller
{
    public function getPermissions(){
        $permissions['movementsIndex']=Auth::user()->hasPermissionTo('movements.index');
        $permissions['movementsCreate']=Auth::user()->hasPermissionTo('movements.create');
        $permissions['movementsStore']=Auth::user()->hasPermissionTo('movements.store');
        $permissions['movementsEdit']=Auth::user()->hasPermissionTo('movements.edit');
        $permissions['movementsUpdate']=Auth::user()->hasPermissionTo('movements.update');
        $permissions['movementsShow']=Auth::user()->hasPermissionTo('movements.show');
        $permissions['movementsDestroy']=Auth::user()->hasPermissionTo('movements.destroy');
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
        $movements=Movement::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($movement)=>[
            'id'=>$movement->id,
            'name'=>$movement->name
        ]);
        //dd($users);

        return Inertia::render('Movements/Index',[
            'movements'=>$movements,
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
        return Inertia::render('Movements/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovementsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $movement=Movement::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('movements.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movement=Movement::findOrfail($id);
        
        return Inertia::render('Movements/Show', ['movement'=>$movement]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $movement=Movement::findOrfail($id);
        return Inertia::render('Movements/Edit', ['movement'=>$movement]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MovementsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $movement=Movement::findOrFail($id);
            $movement->name=$datos['name'];
            $movement->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('movements.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movement=Movement::findOrFail($id);
        //dd($user);
        try{
            $movement->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('movements.index')->with('sysMessage', 'Registro Borrado.');
    }

}
