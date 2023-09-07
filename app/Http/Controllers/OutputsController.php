<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Output;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OutputsCreateRequest;
use App\Http\Requests\OutputsUpdateRequest;

class OutputsController extends Controller
{
    public function getPermissions(){
        $permissions['outputsIndex']=Auth::user()->hasPermissionTo('outputs.index');
        $permissions['outputsCreate']=Auth::user()->hasPermissionTo('outputs.create');
        $permissions['outputsStore']=Auth::user()->hasPermissionTo('outputs.store');
        $permissions['outputsEdit']=Auth::user()->hasPermissionTo('outputs.edit');
        $permissions['outputsUpdate']=Auth::user()->hasPermissionTo('outputs.update');
        $permissions['outputsShow']=Auth::user()->hasPermissionTo('outputs.show');
        $permissions['outputsDestroy']=Auth::user()->hasPermissionTo('outputs.destroy');
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
        $outputs=Output::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($output)=>[
            'id'=>$output->id,
            'name'=>$output->name
        ]);
        //dd($users);

        return Inertia::render('Outputs/Index',[
            'outputs'=>$outputs,
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
        return Inertia::render('Outputs/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutputsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $output=Output::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('outputs.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $output=Output::findOrfail($id);

        return Inertia::render('Outputs/Show', ['output'=>$output]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $output=Output::findOrfail($id);
        return Inertia::render('Outputs/Edit', ['output'=>$output]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OutputsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $output=Output::findOrFail($id);
            $output->name=$datos['name'];
            $output->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('outputs.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $output=Output::findOrFail($id);
        //dd($user);
        try{
            $output->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('outputs.index')->with('sysMessage', 'Registro Borrado.');
    }

}
