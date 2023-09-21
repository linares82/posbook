<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\LnCashBox;
use App\Models\OutputProduct;

class LnCashBoxObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(LnCashBox $lnCashBox)
    {
        $producto=$lnCashBox->product_id;
        $plantel=$lnCashBox->cashBox->plantel_id;

        for($i=1; $i<=$lnCashBox->quantity; $i++){
            $movement=Movement::where('plantel_id', $plantel)
            ->where('product_id', $producto)
            ->whereColumn('cantidad_entrada','>','cantidad_salida')
            ->first();
            //dd($movement->toArray());

            if(!is_null($movement)){
                $input_output_products['movement_id']=$movement->id;
                $input_output_products['ln_cash_box_id']=$lnCashBox->id;
                $input_output_products['cantidad_antes_descuento']=$movement->cantidad_entrada-$movement->cantidad_salida;
                $movement->cantidad_salida=$movement->cantidad_salida+1;
                $movement->save();

                $lnCashBox->movement_id=$movement->id;
                $lnCashBox->save();

                $input_output_products['cantidad_descontada']=1;
                $input_output_products['cantidad_restante']=$movement->cantidad_entrada-$movement->cantidad_salida;
                //dd($input_output_products);
                OutputProduct::create($input_output_products);
            }else{
                $input_output_products['ln_cash_box_id']=$lnCashBox->id;
                $input_output_products['cantidad_descontada']=1;
                //dd($input_output_products);
                OutputProduct::create($input_output_products);
            }
        }

    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(LnCashBox $lnCashBox)
    {
        /*
        if(!is_null($lnCashBox->movement_id)){
            $movement=Movement::where('id', $lnCashBox->movement_id)
            ->first();
            if(!is_null($movement)){
                $movement->cantidad_salida=$movement->cantidad_salida-$lnCashBox->quantity;
                $movement->save();
            }
        }*/
        for($i=1; $i<=$lnCashBox->quantity; $i++){

            $output_product=OutputProduct::where('ln_cash_box_id', $lnCashBox->id)
                ->orderBy('id', 'desc')
                ->first();
            //dd($output_product);
            if(!is_null($output_product) and !is_null($output_product->movement_id)){
                $movement=Movement::where('id', $output_product->movement_id)->first();
                $movement->cantidad_salida=$movement->cantidad_salida-1;
                $movement->save();

            }
            $output_product->delete();

        }



    }
}
