<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\LnCashBox;

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
                $movement->cantidad_salida=$movement->cantidad_salida+1;
                $movement->save();
                $lnCashBox->movement_id=$movement->id;
                $lnCashBox->save();
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
            $movement=Movement::where('id', $lnCashBox->movement_id)
            ->first();
            if(!is_null($movement)){
                $movement->cantidad_salida=$movement->cantidad_salida-$lnCashBox->quantity;
                $movement->save();
            }
    }
}
