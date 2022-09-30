<?php

namespace App\Observers;

use App\Models\Movement;
use App\Models\OrderDevolutionLine;

class OrderDevolutionLineObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(OrderDevolutionLine $orderDevolutionLine)
    {
        $movement=Movement::find($orderDevolutionLine->movement_id);
        $movement->cantidad_salida=$movement->cantidad_salida+$orderDevolutionLine->cantidad; 
        $movement->save();   
        
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(OrderDevolutionLine $orderDevolutionLine)
    {
        $movement=Movement::find($orderDevolutionLine->movement_id);
        $movement->cantidad_salida=$movement->cantidad_salida-$orderDevolutionLine->cantidad; 
        $movement->save();
    }
}
