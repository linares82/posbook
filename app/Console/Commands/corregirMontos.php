<?php

namespace App\Console\Commands;

use App\Models\CashBox;
use App\Models\Payment;
use Illuminate\Console\Command;

class corregirMontos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ian:corregirMontos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //return 0;
        $cajas = CashBox::whereNull('deleted_at')->get();
        foreach ($cajas as $caja) {
            $totalPagos = Payment::where('cash_box_id', $caja->id)
                ->where('st_payment_id', 2)
                ->whereNull('deleted_at')
                ->sum('total');
            $totalDescuentos = Payment::where('cash_box_id', $caja->id)
                ->where('st_payment_id', 2)
                ->whereNull('deleted_at')
                ->sum('discount');

            $cantidadPagos = $caja->payments()->whereNull('deleted_at')->count();

            if ($cantidadPagos == 1) {
                $pagosArray = $caja->payments()->get()->toArray();
                $pago = Payment::find($pagosArray[0]['id']);
                if (is_null($pago->deleted_at)) {
                    if ($pago->porcentaje_descuento == 0 or is_null($pago->porcentaje_descuento)) {
                        $pago->subtotal = $pago->monto;
                        $pago->discount = 0;
                        $pago->total = $pago->monto;
                    } elseif ($pago->porcentaje_descuento > 0) {
                        $caja = CashBox::find($pago->cash_box_id);
                        $pago->subtotal = $caja->total;
                        $pago->discount = $pago->subtotal * $pago->porcentaje_descuento;
                        $pago->total = $pago->subtotal - $pago->discount;
                    }
                    $pago->save();
                    /*if ($caja->total == $totalPagos and $pago->porcentaje_descuento==0) {
                        $caja->st_cash_box_id = 2;
                    }elseif($caja->total == ($totalPagos+$totalDescuentos) and $pago->porcentaje_descuento>0){
                        $caja->st_cash_box_id = 2;
                    }
                    $caja->save();*/
                }
            }elseif($cantidadPagos>1){
                $pagos = $caja->payments()->get();
                foreach($pagos as $pago){
                    if($pago->porcentaje_descuento==0){
                        $pago->subtotal = $pago->monto;
                        $pago->discount = 0;
                        $pago->total = $pago->monto;
                    }elseif($pago->porcentaje_descuento>0){
                        $caja = CashBox::find($pago->cash_box_id);
                        $pago->subtotal = $caja->total;
                        $pago->discount = $pago->subtotal * $pago->porcentaje_descuento;
                        $pago->total = $pago->subtotal - $pago->discount;
                    }
                    $pago->save();
                }

                /*if ($caja->total == $totalPagos) {
                    $caja->st_cash_box_id = 2;
                }
                $caja->save();*/
            }
        }
    }
}
