<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Reason;
use App\Models\Plantel;
use App\Models\Product;
use App\Models\Movement;
use App\Models\TypeMovement;
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
        //dd($request->all());
        $sysMessage=$request->session()->get('sysMessage');
        $filtros=$request->input('search');
        //dd($filtros);
        $m=Movement::query();
        if(isset($filtros['plantel_id'])){
            $m->when($filtros['plantel_id'], function($query, $plantel_id){
                $query->where('plantel_id',$plantel_id);
            });
        }
        if(isset($filtros['reason_id'])){
            $m->when($filtros['reason_id'], function($query, $reason_id){
                $query->where('reason_id',$reason_id);
            });
        }
        if(isset($filtros['type_movement_id'])){
            $m->when($filtros['type_movement_id'], function($query, $type_movement_id){
                $query->where('type_movement_id',$type_movement_id);
            });
        }
        if(isset($filtros['product_id'])){
            $m->when($filtros['product_id'], function($query, $product_id){
                $query->where('product_id',$product_id);
            });
        }
        $movements=$m->orderBy('id','desc')
        ->with('plantel')
        //->where('id',1)->first();
        ->paginate(100)
        ->withQueryString()
        ->through(fn($movement)=>[
            'id'=>$movement->id,
            'plantel'=>$movement->plantel->name,
            'motivo'=>$movement->reason->name,
            'tipo_movimiento'=>$movement->typeMovement->name,
            'producto'=>$movement->product->name,
            'costo'=>$movement->costo,
            'precio'=>$movement->precio,
            'entrada'=>$movement->cantidad_entrada,
            'salida'=>$movement->cantidad_salida
        ]);
        //dd($movements->plantel);

        $planteles=Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos=Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos=TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos=Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        
        return Inertia::render('Movements/Index',[
            'movements'=>$movements,
            'planteles'=>$planteles,
            'motivos'=>$motivos,
            'tipo_movimientos'=>$tipo_movimientos,
            'productos'=>$productos,
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
        $planteles=Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos=Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos=TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos=Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        
        return Inertia::render('Movements/Create', 
        ['planteles'=>$planteles,'motivos'=>$motivos,'tipo_movimientos'=>$tipo_movimientos,'productos'=>$productos]);
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
        $planteles=Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos=Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos=TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos=Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        
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

    public function consultaExistencias(){
        $existencias=Movement::select('movements.id','p.name as plantel','p.id as plantel_id','pro.name as producto', 
        'pro.id as producto_id','cantidad_entrada','cantidad_salida')
        ->join('plantels as p', 'p.id','movements.plantel_id')
        ->join('products as pro', 'pro.id','movements.product_id')
        ->where('movements.type_movement_id',1)
        ->whereColumn('movements.cantidad_entrada','>','movements.cantidad_salida')
        ->get();

        return response(json_encode(array('existencias'=>$existencias)), 200);
    }
}
