<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\Expense;
use App\Models\AccountPlantel;
use App\Models\DetalleAccountPlantel;

class ExpenseObserver
{
    public function created(Expense $expense)
    {

        $cuenta = AccountPlantel::where('account_id',$expense->account_id)
        ->where('plantel_id', $expense->plantel_id)
        ->first();
        $fecha_inicio=Carbon::createFromFormat("Y-m-d", $cuenta->fecha_inicio);
        $fecha_expense=Carbon::createFromFormat("Y-m-d", $expense->fecha);
        if($fecha_expense->greaterThanOrEqualTo($fecha_inicio)){

            $cuenta->saldo_egresos = $cuenta->saldo_egresos + $expense->monto;
            $cuenta->save();

            //detalle para seguimiento
            $input['account_plantel_id']=$cuenta->id;
            $input['expense_id']=$expense->id;
            $input['monto_egreso']=$expense->monto;
            $input['saldo_egresos']=$cuenta->saldo_egresos;
            //dd($input);
            DetalleAccountPlantel::create($input);
            //Fin detalle
        }

    }

    public function deleted(Expense $expense)
    {
        $cuenta = AccountPlantel::where('account_id',$expense->account_id)
        ->where('plantel_id', $expense->plantel_id)
        ->first();

        $fecha_inicio=Carbon::createFromFormat("Y-m-d", $cuenta->fecha_inicio);
        $fecha_expense=Carbon::createFromFormat("Y-m-d", $expense->fecha);
        if($fecha_expense->greaterThanOrEqualTo($fecha_inicio)){
            $cuenta->saldo_egresos = $cuenta->saldo_egresos - $expense->monto;
            $cuenta->save();

            //detalle para seguimiento
            $input['account_plantel_id']=$cuenta->id;
            $input['expense_id']=$expense->id;
            $input['monto_egreso']=$expense->monto*-1;
            $input['saldo_egresos']=$cuenta->saldo_egresos;
            DetalleAccountPlantel::create($input);
            //Fin detalle
        }

    }
}
