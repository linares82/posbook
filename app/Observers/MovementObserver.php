<?php

namespace App\Observers;

use App\Models\CashBox;
use App\Models\Movement;
use App\Models\LnCashBox;
use App\Models\OutputProduct;

class MovementObserver
{
    public function created(Movement $movement)
    {
        $lnCashBoxes = CashBox::select('ln.*')
            ->join('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
            ->join('output_products as op', 'op.ln_cash_box_id', 'ln.id')
            ->where('cash_boxes.plantel_id', $movement->plantel_id)
            ->where('ln.product_id', $movement->product_id)
            ->whereNull('op.movement_id')
            ->whereNull('ln.deleted_at')
            ->distinct()
            ->get();

        /*$lnCashBoxes =CashBox::select('ln.*')
            ->join('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
            ->where('ln.product_id', $movement->product_id)
            ->whereNull('ln.movement_i')
            ->whereNull('ln.deleted_at')
            ->get();*/
        //dd($lnCashBoxes->toArray());
        if (count($lnCashBoxes) > 0) {
            foreach ($lnCashBoxes as $ln) {
                if (($movement->cantidad_entrada-$movement->cantidad_salida) >= $ln->quantity) {
                    $linea = LnCashBox::find($ln->id);
                    $linea->movement_id = $movement->id;
                    $linea->save();

                    $outputs_product = OutputProduct::where('ln_cash_box_id', $ln->id)->get();
                    foreach($outputs_product  as $output_product){
                        $output_product->movement_id = $movement->id;

                        $output_product->cantidad_descontada = 1;

                        $suma=$outputs_product = OutputProduct::where('ln_cash_box_id', $ln->id)
                        ->where('movement_id', $movement->id)->sum('cantidad_descontada');
                        if(is_null($suma)){
                            $suma=0;
                        }
                        $output_product->cantidad_restante=$movement->cantidad_salida;
                        //$output_product->cantidad_antes_descuento = $movement->cantidad_entrada-$suma;
                        $output_product->save();
                    }

                    $movement->cantidad_salida = $movement->cantidad_salida + $ln->quantity;
                    $movement->save();

                }else{
                    $maximo_entrega=($movement->cantidad_entrada-$movement->cantidad_salida);
                    $linea = LnCashBox::find($ln->id);
                    //dd($linea);
                    $linea->movement_id = $movement->id;
                    $linea->save();

                    $outputs_product = OutputProduct::where('ln_cash_box_id', $ln->id)
                    ->whereNull('movement_id')->get();
                    $i=0;
                    //dd($outputs_product->toArray());
                    foreach($outputs_product  as $output_product){
                        $output_product->movement_id = $movement->id;

                        //$output_product->cantidad_descontada = 1;
                        $suma=$outputs_product = OutputProduct::where('ln_cash_box_id', $ln->id)
                        ->where('movement_id', $movement->id)->sum('cantidad_descontada');
                        if(is_null($suma)){
                            $suma=0;
                        }

                        //$output_product->cantidad_antes_descuento = $suma;
                        $output_product->cantidad_restante=$suma+1;
                        $output_product->cantidad_antes_descuento = $movement->cantidad_entrada-$output_product->cantidad_restante;
                        $output_product->save();
                        $i++;
                        if($i==$maximo_entrega){
                            break;
                        }
                    }

                    $movement->cantidad_salida = $movement->cantidad_entrada;
                    $movement->save();

                }

            }
        }
    }

    public function deleting(Movement $movement)
    {
    }
}
