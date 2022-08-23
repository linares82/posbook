<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\TypeMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TypeMovementsCreateRequest;
use App\Http\Requests\TypeMovementsUpdateRequest;

class TypeMovementsController extends Controller
{
    public function getPermissions(){
        $permissions['typeMovementsIndex']=Auth::user()->hasPermissionTo('typeMovements.index');
        $permissions['typeMovementsCreate']=Auth::user()->hasPermissionTo('typeMovements.create');
        $permissions['typeMovementsStore']=Auth::user()->hasPermissionTo('typeMovements.store');
        $permissions['typeMovementsEdit']=Auth::user()->hasPermissionTo('typeMovements.edit');
        $permissions['typeMovementsUpdate']=Auth::user()->hasPermissionTo('typeMovements.update');
        $permissions['typeMovementsShow']=Auth::user()->hasPermissionTo('typeMovements.show');
        $permissions['typeMovementsDestroy']=Auth::user()->hasPermissionTo('typeMovements.destroy');
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
        $typeMovements=TypeMovement::query()
        ->when($request->input('name'), function($query, $name){
            $query->where('name','like','%'.$name.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($typeMovement)=>[
            'id'=>$typeMovement->id,
            'name'=>$typeMovement->name,
        ]);
        //dd($users);

        return Inertia::render('TypeMovements/Index',[
            'typeMovements'=>$typeMovements,
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
        return Inertia::render('TypeMovements/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeMovementsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $typeMovement=TypeMovement::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('typeMovements.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $typeMovement=TypeMovement::findOrfail($id);
        
        return Inertia::render('TypeMovements/Show', ['typeMovement'=>$typeMovement]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $typeMovement=TypeMovement::findOrfail($id);
        return Inertia::render('TypeMovements/Edit', ['typeMovement'=>$typeMovement]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TypeMovementsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $typeMovement=TypeMovement::findOrFail($id);
            $typeMovement->name=$datos['name'];
            $typeMovement->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('typeMovements.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $typeMovement=TypeMovement::findOrFail($id);
        //dd($user);
        try{
            $typeMovement->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('typeMovements.index')->with('sysMessage', 'Registro Borrado.');
    }
}
