<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PeriodsCreateRequest;
use App\Http\Requests\PeriodsUpdateRequest;

class PeriodsController extends Controller
{
    public function getPermissions(){
        $permissions['periodsIndex']=Auth::user()->hasPermissionTo('periods.index');
        $permissions['periodsCreate']=Auth::user()->hasPermissionTo('periods.create');
        $permissions['periodsStore']=Auth::user()->hasPermissionTo('periods.store');
        $permissions['periodsEdit']=Auth::user()->hasPermissionTo('periods.edit');
        $permissions['periodsUpdate']=Auth::user()->hasPermissionTo('periods.update');
        $permissions['periodsShow']=Auth::user()->hasPermissionTo('periods.show');
        $permissions['periodsDestroy']=Auth::user()->hasPermissionTo('periods.destroy');
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
        $periods=Period::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($period)=>[
            'id'=>$period->id,
            'name'=>$period->name
        ]);
        //dd($users);

        return Inertia::render('Periods/Index',[
            'periods'=>$periods,
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
        return Inertia::render('Periods/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PeriodsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $period=Period::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('periods.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $period=Period::findOrfail($id);
        
        return Inertia::render('Periods/Show', ['period'=>$period]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $period=Period::findOrfail($id);
        return Inertia::render('Periods/Edit', ['period'=>$period]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PeriodsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $period=Period::findOrFail($id);
            $period->name=$datos['name'];
            $period->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('periods.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $period=Period::findOrFail($id);
        //dd($user);
        try{
            $period->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('periods.index')->with('sysMessage', 'Registro Borrado.');
    }

}
