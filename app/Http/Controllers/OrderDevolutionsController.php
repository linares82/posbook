<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\OrderDevolution;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderDevolutionsCreateRequest;
use App\Http\Requests\OrderDevolutionsUpdateRequest;
use App\Models\OrderDevolutionLine;

class OrderDevolutionsController extends Controller
{
    public function getPermissions(){
        $permissions['orderDevolutionsIndex']=Auth::user()->hasPermissionTo('orderDevolutions.index');
        $permissions['orderDevolutionsCreate']=Auth::user()->hasPermissionTo('orderDevolutions.create');
        $permissions['orderDevolutionsStore']=Auth::user()->hasPermissionTo('orderDevolutions.store');
        $permissions['orderDevolutionsEdit']=Auth::user()->hasPermissionTo('orderDevolutions.edit');
        $permissions['orderDevolutionsUpdate']=Auth::user()->hasPermissionTo('orderDevolutions.update');
        $permissions['orderDevolutionsShow']=Auth::user()->hasPermissionTo('orderDevolutions.show');
        $permissions['orderDevolutionsDestroy']=Auth::user()->hasPermissionTo('orderDevolutions.destroy');
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
        $orderDevolutions=OrderDevolution::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($orderDevolution)=>[
            'id'=>$orderDevolution->id,
            'name'=>$orderDevolution->name,
            'fecha'=>$orderDevolution->fecha,
            'motivo'=>$orderDevolution->motivo
        ]);
        //dd($orderDevolutions);

        return Inertia::render('OrderDevolutions/Index',[
            'orderDevolutions'=>$orderDevolutions,
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
        return Inertia::render('OrderDevolutions/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderDevolutionsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $orderDevolution=OrderDevolution::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        $orderDevolutionLines=OrderDevolutionLine::where('order_devolution_id',$id)
        ->get()
        ->map(fn ($linea) => [
            'id' => $linea->id,
            'order_devolution_id' => $linea->order_devolution_id,
            'plantel_id' => $linea->plantel_id,
            'plantel'=> $linea->plantel->name,
            'product_id' => $linea->product_id,
            'product'=>$linea->product->name,
            'cantidad' => $linea->cantidad,
            'contacto' => $linea->contacto,
            'bnd_salida_registrada'=> $linea->bnd_salida_registrada
        ]);
        //dd($orderDevolutionLines);
        $route_consultaExistencias=route('movements.consultaExistencias');
        $route_storeLines=route('orderDevolutionLines.storeLines');
        $route_destroy_ln=route('orderDevolutionLines.destroy');
        //dd($route_consultaExistencias);
        
        return Inertia::render('OrderDevolutions/Show', 
        ['orderDevolution'=>$orderDevolution,
         'orderDevolutionLines'=>$orderDevolutionLines,
         'route_consultaExistencias'=>$route_consultaExistencias,
        'route_storeLines'=>$route_storeLines,
        'route_destroy_ln'=>$route_destroy_ln]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        return Inertia::render('OrderDevolutions/Edit', ['orderDevolution'=>$orderDevolution]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderDevolutionsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $orderDevolution=OrderDevolution::findOrFail($id);
            $orderDevolution->name=$datos['name'];
            $orderDevolution->fecha=$datos['fecha'];
            $orderDevolution->motivo=$datos['motivo'];
            $orderDevolution->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orderDevolution=OrderDevolution::findOrFail($id);
        //dd($user);
        try{
            $orderDevolution->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function registrarDevolucion($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        $orderDevolutionLines=OrderDevolutionLine::where('order_devolution_id',$id)
        ->whereIn('plantel_id', Auth::user()->plantels->pluck('id'))
        ->get()
        ->map(fn ($linea) => [
            'id' => $linea->id,
            'order_devolution_id' => $linea->order_devolution_id,
            'plantel_id' => $linea->plantel_id,
            'plantel'=> $linea->plantel->name,
            'product_id' => $linea->product_id,
            'product'=>$linea->product->name,
            'cantidad' => $linea->cantidad,
            'contacto' => $linea->contacto,
            'bnd_salida_registrada'=> $linea->bnd_salida_registrada
        ]);
        //dd($orderDevolutionLines);
        $route_consultaExistencias=route('movements.consultaExistencias');
        $route_storeLines=route('orderDevolutionLines.storeLines');
        $route_destroy_ln=route('orderDevolutionLines.destroy');
        //dd($route_consultaExistencias);
        
        return Inertia::render('OrderDevolutions/RegistrarDevolucion', 
        ['orderDevolution'=>$orderDevolution,
         'orderDevolutionLines'=>$orderDevolutionLines,
         'route_consultaExistencias'=>$route_consultaExistencias,
        'route_storeLines'=>$route_storeLines,
        'route_destroy_ln'=>$route_destroy_ln]);
    }
    

}
