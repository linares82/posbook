<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\StCashBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StCashBoxesCreateRequest;
use App\Http\Requests\StCashBoxesUpdateRequest;

class StCashBoxesController extends Controller
{
    public function getPermissions(){
        $permissions['stCashBoxesIndex']=Auth::user()->hasPermissionTo('stCashBoxes.index');
        $permissions['stCashBoxesCreate']=Auth::user()->hasPermissionTo('stCashBoxes.create');
        $permissions['stCashBoxesStore']=Auth::user()->hasPermissionTo('stCashBoxes.store');
        $permissions['stCashBoxesEdit']=Auth::user()->hasPermissionTo('stCashBoxes.edit');
        $permissions['stCashBoxesUpdate']=Auth::user()->hasPermissionTo('stCashBoxes.update');
        $permissions['stCashBoxesShow']=Auth::user()->hasPermissionTo('stCashBoxes.show');
        $permissions['stCashBoxesDestroy']=Auth::user()->hasPermissionTo('stCashBoxes.destroy');
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
        $stCashBoxes=StCashBox::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($stCashBoxe)=>[
            'id'=>$stCashBoxe->id,
            'name'=>$stCashBoxe->name
        ]);
        //dd($users);

        return Inertia::render('StCashBoxes/Index',[
            'stCashBoxes'=>$stCashBoxes,
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
        return Inertia::render('StCashBoxes/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StCashBoxesCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stCashBoxe=StCashBox::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stCashBoxes.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stCashBoxe=StCashBox::findOrfail($id);
        
        return Inertia::render('StCashBoxes/Show', ['stCashBoxe'=>$stCashBoxe]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stCashBoxe=StCashBox::findOrfail($id);
        return Inertia::render('StCashBoxes/Edit', ['stCashBoxe'=>$stCashBoxe]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StCashBoxesUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stCashBoxe=StCashBox::findOrFail($id);
            $stCashBoxe->name=$datos['name'];
            $stCashBoxe->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stCashBoxes.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stCashBoxe=StCashBox::findOrFail($id);
        //dd($user);
        try{
            $stCashBoxe->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('stCashBoxes.index')->with('sysMessage', 'Registro Borrado.');
    }

}
