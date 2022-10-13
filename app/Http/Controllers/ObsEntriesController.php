<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\ObsEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ObsEntrysCreateRequest;
use App\Http\Requests\ObsEntrysUpdateRequest;

class ObsEntriesController extends Controller
{
    public function getPermissions(){
        $permissions['obsEntrysIndex']=Auth::user()->hasPermissionTo('obsEntrys.index');
        $permissions['obsEntrysCreate']=Auth::user()->hasPermissionTo('obsEntrys.create');
        $permissions['obsEntrysStore']=Auth::user()->hasPermissionTo('obsEntrys.store');
        $permissions['obsEntrysEdit']=Auth::user()->hasPermissionTo('obsEntrys.edit');
        $permissions['obsEntrysUpdate']=Auth::user()->hasPermissionTo('obsEntrys.update');
        $permissions['obsEntrysShow']=Auth::user()->hasPermissionTo('obsEntrys.show');
        $permissions['obsEntrysDestroy']=Auth::user()->hasPermissionTo('obsEntrys.destroy');
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
        $obsEntrys=ObsEntry::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($obsEntry)=>[
            'id'=>$obsEntry->id,
            'name'=>$obsEntry->name
        ]);
        //dd($users);

        return Inertia::render('ObsEntrys/Index',[
            'obsEntrys'=>$obsEntrys,
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
        return Inertia::render('ObsEntrys/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ObsEntrysCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $obsEntry=ObsEntry::create($datos);
        }catch(Exception $e){
            dd($e);
        }
        //dd($obsEntry->orderSalesLine->toArray());

        return redirect()->route('orderSales.show', $obsEntry->orderSalesLine->order_sale_id)->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $obsEntry=ObsEntry::findOrfail($id);
        
        return Inertia::render('ObsEntrys/Show', ['obsEntry'=>$obsEntry]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $obsEntry=ObsEntry::findOrfail($id);
        return Inertia::render('ObsEntrys/Edit', ['obsEntry'=>$obsEntry]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ObsEntrysUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $obsEntry=ObsEntry::findOrFail($id);
            $obsEntry->name=$datos['name'];
            $obsEntry->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('obsEntrys.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $obsEntry=ObsEntry::findOrFail($id);
        //dd($user);
        $id=$obsEntry->orderSalesLine->order_sale_id;
        try{
            $obsEntry->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderSales.show', $id)->with('sysMessage', 'Registro Borrado.');
    }

    public function verObservaciones(Request $request){
        $datos=$request->all();
        $obs=ObsEntry::select('obs_entries.*','u.name as user_alta')->join('users as u','u.id','obs_entries.usu_alta_id')
        ->where('order_sales_line_id', $datos['order_sales_line_id'])->get();
        //dd($obs);
        return $obs;
    }
}

