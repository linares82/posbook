<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Reason;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ReasonsCreateRequest;
use App\Http\Requests\ReasonsUpdateRequest;

class ReasonsController extends Controller
{
    public function getPermissions(){
        $permissions['reasonsIndex']=Auth::user()->hasPermissionTo('reasons.index');
        $permissions['reasonsCreate']=Auth::user()->hasPermissionTo('reasons.create');
        $permissions['reasonsStore']=Auth::user()->hasPermissionTo('reasons.store');
        $permissions['reasonsEdit']=Auth::user()->hasPermissionTo('reasons.edit');
        $permissions['reasonsUpdate']=Auth::user()->hasPermissionTo('reasons.update');
        $permissions['reasonsShow']=Auth::user()->hasPermissionTo('reasons.show');
        $permissions['reasonsDestroy']=Auth::user()->hasPermissionTo('reasons.destroy');
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
        $reasons=Reason::query()
        ->when($request->input('name'), function($query, $name){
            $query->where('name','like','%'.$name.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($reason)=>[
            'id'=>$reason->id,
            'name'=>$reason->name
        ]);
        //dd($reasons);

        return Inertia::render('Reasons/Index',[
            'reasons'=>$reasons,
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
        return Inertia::render('Reasons/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReasonsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $reason=Reason::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('reasons.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reason=Reason::findOrfail($id);
        
        return Inertia::render('Reasons/Show', ['reason'=>$reason]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reason=Reason::findOrfail($id);
        return Inertia::render('Reasons/Edit', ['reason'=>$reason]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReasonsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $reason=Reason::findOrFail($id);
            $reason->name=$datos['name'];
            $reason->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('reasons.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reason=Reason::findOrFail($id);
        //dd($user);
        try{
            $reason->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('reasons.index')->with('sysMessage', 'Registro Borrado.');
    }

}
