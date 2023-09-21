<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Stock;
use App\Models\CashBox;
use App\Models\Movement;
use App\Models\LnCashBox;
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

        return redirect()->route('orderSales.edit',$period->order_sale_id)->with('sysMessage', 'Registro Actualizado.');
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

        $movements=Movement::where('order_sales_line_id', $orderSalesLine->id)->where('cantidad_salida', '>', 0)->get();

        if(count($movements)>0){
            return redirect()->route('orderSales.edit', $cabecera)->with('sysMessage', 'Registro con movimientos, no puede ser borrar.');
        }
        //dd($user);
        try{
            $orderSalesLine->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderSales.edit', $cabecera)->with('sysMessage', 'Registro Borrado.');
    }

    public function receiveOCPlantel($id){
        try{
            $linea=OrderSalesLine::findOrFail($id);
            $linea->bnd_entrada_registrada=1;
            $linea->save();

            /*$movement_apartados=Movement::where('plantel_id',$linea->plantel_id)
            ->where('product_id',$linea->product_id)
            ->where('reason_id',4)
            ->whereColumn('cantidad_entrada','<','cantidad_salida')
            ->first();
*/


            $input['plantel_id']=$linea->plantel_id;
            $input['reason_id']=2;
            $input['type_movement_id']=1;
            $input['product_id']=$linea->product_id;
            $input['costo']=$linea->product->costo;
            $input['precio']=$linea->product->precio;
            $input['cantidad_entrada']=$linea->cantidad;
            $input['cantidad_salida']=0;
            $input['order_sales_line_id']=$linea->id;

            $movement=Movement::create($input);

            $lnCashBoxes=CashBox::select('ln.*')->join('ln_cash_boxes as ln','ln.cash_box_id','cash_boxes.id')
            ->where('cash_boxes.plantel_id', $linea->plantel_id)
            ->where('ln.product_id', $linea->product_id)
            ->whereNull('ln.movement_id')
            ->get();
            if(count($lnCashBoxes)>0){
                foreach($lnCashBoxes as $ln){
                    $linea=LnCashBox::find($ln->id);
                    $linea->movement_id=$movement->id;
                    $linea->save();
                    $movement->cantidad_salida=$movement->cantidad_salida+$ln->quantity;
                    $movement->save();
                }
            }

        }catch(Exception $e){
            dd($e);
        }


    }
}
