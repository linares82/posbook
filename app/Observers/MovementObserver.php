<?php

namespace App\Observers;

use App\Models\CashBox;
use App\Models\Movement;
use App\Models\LnCashBox;

class MovementObserver
{
    public function created(Movement $movement){
        $lnCashBoxes=CashBox::select('ln.*')->join('ln_cash_boxes as ln','ln.cash_box_id','cash_boxes.id')
            ->where('cash_boxes.plantel_id', $movement->plantel_id)
            ->where('ln.product_id', $movement->product_id)
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
    }

    public function deleting(Movement $movement) {

    }
}
