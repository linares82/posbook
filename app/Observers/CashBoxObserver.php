<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\AccountPlantel;
use App\Models\DetalleAccountPlantel;

class CashBoxObserver
{
    protected CashBox $caja_anterior;
    public function updating(CashBox $cashBox)
    {
        $this->caja_anterior = $cashBox->find($cashBox->id);

        if ($cashBox->st_cash_box_id == 2 and $this->caja_anterior->st_cash_box_id<>2) {
            $lineas = $cashBox->lnCashBoxes;
            foreach ($lineas as $linea) {
                $cuenta = AccountPlantel::where('account_id',$linea->product->account_id)
                ->where('plantel_id',$cashBox->plantel_id)
                ->first();


                $fecha_inicio = Carbon::createFromFormat("Y-m-d", $cuenta->fecha_inicio);
                $fecha_caja = Carbon::createFromFormat("Y-m-d",$cashBox->fecha);
                if ($fecha_caja->greaterThanOrEqualTo($fecha_inicio)) {
                    $cuenta->saldo_ingresos = $cuenta->saldo_ingresos + $linea->total;
                    $cuenta->save();

                    //detalle para seguimiento
                    $input['account_plantel_id']=$cuenta->id;
                    $input['ln_cash_box_id']=$linea->id;
                    $input['monto_ingreso']=$linea->total;
                    $input['saldo_ingresos']=$cuenta->saldo_ingresos;
                    DetalleAccountPlantel::create($input);
                    //Fin detalle

                }
            }
        }elseif($cashBox->st_cash_box_id <> 2 and $this->caja_anterior->st_cash_box_id==2){
            $lineas = $cashBox->lnCashBoxes;
            foreach ($lineas as $linea) {
                $cuenta = AccountPlantel::where('account_id',$linea->product->account_id)
                ->where('plantel_id',$cashBox->plantel_id)
                ->first();
                $fecha_inicio = Carbon::createFromFormat("Y-m-d", $cuenta->fecha_inicio);
                $fecha_caja = Carbon::createFromFormat("Y-m-d",$cashBox->fecha);
                if ($fecha_caja->greaterThanOrEqualTo($fecha_inicio)) {
                    $cuenta->saldo_ingresos = $cuenta->saldo_ingresos - $linea->total;
                    $cuenta->save();

                    //detalle para seguimiento
                    $input['account_plantel_id']=$cuenta->id;
                    $input['ln_cash_box_id']=$linea->id;
                    $input['monto_ingreso']=$linea->total*-1;
                    $input['saldo_ingresos']=$cuenta->saldo_ingresos;
                    DetalleAccountPlantel::create($input);
                    //Fin detalle
                }
            }
        }
    }
}
