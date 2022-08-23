<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Movement;
use Illuminate\Http\Request;
use App\Models\OrderSalesLine;
use App\Http\Requests\OrderSalesLinesUpdateRequest;

class OrderSalesLinesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OrderSalesLine  $orderSalesLine
     * @return \Illuminate\Http\Response
     */
    public function show(OrderSalesLine $orderSalesLine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OrderSalesLine  $orderSalesLine
     * @return \Illuminate\Http\Response
     */
    public function edit(OrderSalesLine $orderSalesLine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OrderSalesLine  $orderSalesLine
     * @return \Illuminate\Http\Response
     */
    public function update(OrderSalesLinesUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $period=OrderSalesLine::findOrFail($id);
            $period->plantel_id=$datos['plantel_id'];
            $period->product_id=$datos['product_id'];
            $period->cantidad=$datos['cantidad'];
            $period->contacto=$datos['contacto'];
            $period->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderSales.edit',$id)->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\OrderSalesLine  $orderSalesLine
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orderSalesLine=OrderSalesLine::findOrFail($id);
        $cabecera=$orderSalesLine->order_sale_id;
        //dd($user);
        try{
            $orderSalesLine->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderSales.edit', $cabecera)->with('sysMessage', 'Registro Borrado.');
    }

    public function receiveOCPlantel($id){
        $linea=OrderSalesLine::findOrFail($id);
        $linea->bnd_entrada_registrada=1;
        $linea->save();

        $input['plantel_id']=$linea->plantel_id;
        $input['reason_id']=2;
        $input['type_movement_id']=1;
        $input['product_id']=$linea->product_id;
        $input['costo']=$linea->product->costo;
        $input['precio']=$linea->product->precio;
        $input['cantidad_entrada']=$linea->cantidad;
        $input['order_sales_line_id']=$linea->id;

        $movement=Movement::create($input);

    }
}
