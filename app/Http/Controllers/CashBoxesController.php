<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Plantel;
use App\Models\Product;
use App\Models\Movement;
use App\Models\LnCashBox;
use App\Models\StCashBox;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CashBoxsCreateRequest;
use App\Http\Requests\CashBoxsUpdateRequest;

class CashBoxesController extends Controller
{
    public function getPermissions()
    {
        $permissions['cashBoxesIndex'] = Auth::user()->hasPermissionTo('cashBoxes.index');
        $permissions['cashBoxesCreate'] = Auth::user()->hasPermissionTo('cashBoxes.create');
        $permissions['cashBoxesStore'] = Auth::user()->hasPermissionTo('cashBoxes.store');
        $permissions['cashBoxesEdit'] = Auth::user()->hasPermissionTo('cashBoxes.edit');
        $permissions['cashBoxesUpdate'] = Auth::user()->hasPermissionTo('cashBoxes.update');
        $permissions['cashBoxesShow'] = Auth::user()->hasPermissionTo('cashBoxes.show');
        $permissions['cashBoxesDestroy'] = Auth::user()->hasPermissionTo('cashBoxes.destroy');
        $permissions['cashBoxesManyLines'] = Auth::user()->hasPermissionTo('cashBoxes.manyLines');
        $permissions['cashBoxesCancelCashBox'] = Auth::user()->hasPermissionTo('cashBoxes.cancelCashBox');
        $permissions['cashBoxesPayments'] = Auth::user()->hasPermissionTo('cashBoxes.payments');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filtros = $request->input('search');
        $sysMessage = $request->session()->get('sysMessage');
        $m = CashBox::query();
            $m->whereIn('plantel_id', Auth::user()->plantels->pluck('id'));
            if (isset($filtros['name'])) {
                $m->when($filtros['name'], function ($query, $name) {
                    $query->where('movements.name', $name);
                });
            }
            if (isset($filtros['plantel_id'])) {
                $m->when($filtros['plantel_id'], function ($query, $plantel_id) {
                    $query->where('cash_boxes.plantel_id', $plantel_id);
                });
            }
            $cashBoxes=$m->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString()
            ->through(fn ($cashBox) => [
                'id' => $cashBox->id,
                'plantel'=>$cashBox->plantel->name,
                'cliente' => $cashBox->customer,
                'fecha' => $cashBox->fecha,
                'estatus' => optional($cashBox->stCashBox)->name,
                'total' => $cashBox->total,
            ]);
        //dd($users);

        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

        return Inertia::render('CashBoxes/Index', [
            'cashBoxes' => $cashBoxes,
            'filters' => $request->only(['name', 'plantel_id']),
            'sysMessage' => $sysMessage,
            'planteles'=>$planteles,
            'permissions' => $this->getPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $estatus = StCashBox::get()->map(fn ($stCashBox) => [
            'value' => $stCashBox->id,
            'label' => $stCashBox->name,
        ]);
        $estatus->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        $productos->prepend(["value" => null, 'label' => "Selecionar Opción"]);
        //dd();
        $plantel = Auth::user()->plantels->first()->id;

        $paymentMethods = PaymentMethod::get()->map(fn ($paymentMethod) => [
            'value' => $paymentMethod->id,
            'label' => $paymentMethod->name,
        ]);
        $paymentMethods->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $ruta_productos_findById = route('products.findById');
        $ruta_consulta_porcentaje_descuento = route('paymentMethods.consultaPorcentajeDescuento'); //
        //dd($ruta_productos_findById);



        return Inertia::render(
            'CashBoxes/Create',
            ['planteles' => $planteles, 'estatus' => $estatus, 'productos' => $productos,
            'plantel' => $plantel, 'paymentMethods' => $paymentMethods,
            'ruta_productos_findById' => $ruta_productos_findById,
            'ruta_consulta_porcentaje_descuento'=>$ruta_consulta_porcentaje_descuento]
        );
        //->withViewData(['ruta_productos_findById'=>$ruta_productos_findById]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CashBoxsCreateRequest $request)
    {
        $datos = $request->all();
        // dd($datos);
        $inputCaja['plantel_id'] = $datos['plantel_id'];
        $inputCaja['fecha'] = $datos['fecha'];
        $inputCaja['customer'] = $datos['customer'];
        $inputCaja['matricula'] = $datos['matricula'];
        $inputCaja['total'] = $datos['total'];
        if(!isset($datos['bnd_entregado'])){
            $inputCaja['bnd_entregado']=0;
        }else{
            $inputCaja['bnd_entregado'] = $datos['bnd_entregado'];
        }

        $inputCaja['st_cash_box_id'] = 1;

        try {
            $cashBox = CashBox::create($inputCaja);
            foreach ($datos['lineas'] as $linea) {
                $inputLinea['cash_box_id'] = $cashBox->id;
                $inputLinea['product_id'] = $linea['product_id'];
                $inputLinea['quantity'] = $linea['quantity'];
                $inputLinea['precio'] = $linea['precio'];
                $inputLinea['total'] = $linea['totalLinea'];
                LnCashBox::create($inputLinea);
            }
            foreach ($datos['payments'] as $payment) {
                $inputPayment['cash_box_id'] = $cashBox->id;
                $inputPayment['payment_method_id'] = $payment['payment_method_id'];
                $inputPayment['porcentaje_descuento'] = $payment['porcentaje_descuento'];
                $inputPayment['monto'] = $payment['monto'];
                $inputPayment['fecha'] = $payment['fecha'];
                $inputPayment['st_payment_id'] = 2;
                $payment = Payment::create($inputPayment);
            }
            $totalPagos = Payment::where('cash_box_id', $cashBox->id)
                ->where('st_payment_id', 2)
                ->whereNull('deleted_at')
                ->sum('monto');
            //dd(($totalPagos));
            if ($cashBox->total == $totalPagos) {
                $cashBox->st_cash_box_id = 2;
            } elseif ($cashBox->total > $totalPagos) {
                $cashBox->st_cash_box_id = 3;
            }
            $cashBox->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('cashBoxes.edit', $cashBox->id)->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cashBoxe = CashBox::findOrfail($id);

        return Inertia::render('CashBoxs/Show', ['cashBoxe' => $cashBoxe]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $caja = CashBox::findOrFail($id);
        $lnCaja = LnCashBox::where('cash_box_id', $id)
            ->get();

        $cashBox = array(
            'id' => $caja->id,
            'customer' => $caja->customer,
            'matricula'=> $caja->matricula,
            'fecha' => $caja->fecha,
            'st_cash_box_id' => $caja->st_cash_box_id,
            'st_cash_box' => $caja->stCashBox->name,
            'total' => $caja->total,
            'plantel' => $caja->plantel->name,
            'plantel_id' => $caja->plantel_id,
            'bnd_entregado'=>$caja->bnd_entregado,
            'reference'=>$caja->reference,
            'bnd_referencia_revisada' => $caja->bnd_referencia_revisada
        );
        //dd($cashBox);
        $cashBox['lineas'] = array();
        foreach ($lnCaja as $ln) {
            array_push($cashBox['lineas'], array(
                'id' => $ln->id,
                'cash_box_id' => $ln->cash_box_id,
                'product_id' => $ln->product_id,
                'product' => $ln->product->name,
                'quantity' => $ln->quantity,
                'precio' => $ln->precio,
                'totalLinea' => $ln->total,
                'deleted' => 0,
                'movement_id'  => $ln->movement_id,
            ));
        }
        $pagos = Payment::where('cash_box_id', $id)->get();
        $cashBox['payments'] = array();
        $posicion = 0;
        foreach ($pagos as $pago) {
            //dd($pago->paymentMethod);
            $cashBox['payments'][$posicion] = array(
                'id' => $pago->id,
                'cash_box_id' => $pago->cash_box_id,
                'payment_method_id' => $pago->payment_method_id,
                'payment_method' => $pago->paymentMethod->name,
                'porcentaje_descuento' => $pago->porcentaje_descuento,
                'monto' => $pago->monto,
                'fecha' => $pago->fecha,
                'st_payment_id' => $pago->st_payment_id,
                'st_payment' => $pago->stPayment->name,
                'deleted' => 0
            );
            $posicion++;
        }
        //dd(json_encode($cashBox));
        $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $estatus = StCashBox::get()->map(fn ($stCashBox) => [
            'value' => $stCashBox->id,
            'label' => $stCashBox->name,
        ]);
        $estatus->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        $productos->prepend(["value" => null, 'label' => "Selecionar Opción"]);
        //dd();
        $plantel = Auth::user()->plantels->first()->id;

        $paymentMethods = PaymentMethod::get()->map(fn ($paymentMethod) => [
            'value' => $paymentMethod->id,
            'label' => $paymentMethod->name,
        ]);
        $paymentMethods->prepend(["value" => null, 'label' => "Selecionar Opción"]);

        $ruta_productos_findById = route('products.findById');
        $ruta_update_ln = route('lnCashBoxes.update');
        $ruta_destroy_ln = route('lnCashBoxes.destroy');
        $ruta_update_payment = route('payments.update');
        $ruta_destroy_payment = route('payments.destroy');
        $ruta_update_cashBox = route('cashBoxes.update');
        $ruta_consulta_porcentaje_descuento = route('paymentMethods.consultaPorcentajeDescuento'); //
        //dd($ruta_consulta_porcentaje_descuento);


        return Inertia::render(
            'CashBoxes/Edit',
            [
                'planteles' => $planteles, 'estatus' => $estatus, 'productos' => $productos, 'plantel' => $plantel,
                'paymentMethods' => $paymentMethods, 'ruta_productos_findById' => $ruta_productos_findById,
                'cashBox' => $cashBox, 'ruta_update_ln' => $ruta_update_ln, 'ruta_destroy_ln' => $ruta_destroy_ln,
                'ruta_update_payment' => $ruta_update_payment, 'ruta_destroy_payment' => $ruta_destroy_payment,
                'ruta_update_cashBox' => $ruta_update_cashBox,
                'ruta_consulta_porcentaje_descuento' => $ruta_consulta_porcentaje_descuento,
                'permissions'=>$this->getPermissions()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CashBoxsUpdateRequest $request)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $cashBox = CashBox::findOrFail($datos['id']);
            $cashBox->plantel_id = $datos['plantel_id'];
            $cashBox->fecha = $datos['fecha'];
            $cashBox->customer = $datos['customer'];
            $cashBox->matricula = $datos['matricula'];
            $cashBox->total = $datos['total'];
            $cashBox->reference = $datos['reference'];
            if(!isset($datos['bnd_entregado'])){
                $cashBox->bnd_entregado=0;
            }else{
                $cashBox->bnd_entregado=$datos['bnd_entregado'];
            }
            if(!isset($datos['bnd_referencia_revisada'])){
                $cashBox->bnd_referencia_revisada=0;
            }else{
                $cashBox->bnd_referencia_revisada=$datos['bnd_referencia_revisada'];
            }
            //dd($cashBox);
            $cashBox->save();

            foreach ($datos['lineas'] as $linea) {

                if (!isset($linea['id'])) {
                    $inputLinea['cash_box_id'] = $cashBox->id;
                    $inputLinea['product_id'] = $linea['product_id'];
                    $inputLinea['quantity'] = $linea['quantity'];
                    $inputLinea['precio'] = $linea['precio'];
                    $inputLinea['total'] = $linea['totalLinea'];
                    LnCashBox::create($inputLinea);
                }
            }
            foreach ($datos['payments'] as $payment) {
                if (!isset($payment['id'])) {
                    $inputPayment['cash_box_id'] = $cashBox['id'];
                    $inputPayment['payment_method_id'] = $payment['payment_method_id'];
                    $inputPayment['porcentaje_descuento'] = $payment['porcentaje_descuento'];
                    $inputPayment['monto'] = $payment['monto'];
                    $inputPayment['fecha'] = $payment['fecha'];
                    $inputPayment['st_payment_id'] = 2;
                    $payment = Payment::create($inputPayment);
                }
            }

            $totalPagos = Payment::where('cash_box_id', $cashBox->id)
                ->where('st_payment_id', 2)
                ->whereNull('deleted_at')
                ->sum('monto');
            //dd(($totalPagos));
            if ($cashBox->total == $totalPagos) {
                $cashBox->st_cash_box_id = 2;
            } elseif ($cashBox->total > $totalPagos) {
                $cashBox->st_cash_box_id = 3;
            }
            $cashBox->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('cashBoxes.edit', $datos['id'])->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cashBox = CashBox::findOrFail($id);
        //dd($user);
        try {
            $cashBox->delete();
        } catch (Exception $e) {
            dd($e);
        }
        return redirect()->route('cashBoxes.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function cancelCashBox($id)
    {
        $cashBox=CashBox::with('payments')->with('lnCashBoxes')->findOrFail($id);

        //dd($cashBox);
        foreach($cashBox->lnCashBoxes as $linea){
            //dd($linea);
            /*if($cashBox->st_cash_box_id==2 and $cashBox->bnd_entrgado==1){
                $movimiento=Movement::find($linea->movement_id);
                $movimiento->cantidad_salida=$movimiento->cantidad_salida-$linea->quantity;
                $movimiento->save();
            }*/
            $linea->delete();
        }

        foreach($cashBox->payments as $payment){
            //dd($payment);
            $payment->delete();
        }

        $cashBox->st_cash_box_id=4;
        $cashBox->save();
    }

    public function ticket($id){

        $cashBox =cashBox::select('cash_boxes.*', 'p.name as plantel','st.name as estatus','p.address','p.director','phone')
        ->join('plantels as p','p.id','cash_boxes.plantel_id')
        ->join('st_cash_boxes as st','st.id','cash_boxes.st_cash_box_id')->where('cash_boxes.id',$id)->first();

        $cashBoxLns=LnCashBox::where('cash_box_id',$cashBox->id)->get()->map(fn ($ln) => [
            'id' => $ln->id,
            'cash_box_id' => $ln->cash_box_id,
            'product_id' => $ln->product_id,
            'product' => $ln->product->name,
            'quantity' => $ln->quantity,
            'precio' => $ln->precio,
            'total' => $ln->total,
            'movement_id' => $ln->movement_id
        ]);

        $payments= Payment::select('payments.*','pm.name as payment_method')
        ->join('payment_methods as pm','pm.id','payments.payment_method_id')
        ->where('cash_box_id', $cashBox->id)
        ->get();

        return Inertia::render(
            'CashBoxes/Ticket',
            [
                'cashBox' => $cashBox,
                'cashBoxLns'=>$cashBoxLns,
                'payments'=>$payments
            ]
        );
    }

    public function rptCajasApartadasF(Request $request){

        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value" => null, 'label' => "Selecionar Opción"]);
        return Inertia::render(
            'CashBoxes/reportes/RptCajasApartadasF',
            [
                'planteles' => $planteles
            ]
        );
    }

    public function rptCajasApartadasR(Request $request){
        $datos=$request->all();
        //dd($datos);
        $cajasApartadas=CashBox::select('cash_boxes.id','cash_boxes.customer','st_cash_box_id','stcb.name as stcb',
        'cash_boxes.total as total_caja', 'cash_boxes.matricula', 'cash_boxes.bnd_entregado','cash_boxes.fecha as fecha_caja',
        'cash_boxes.bnd_referencia_revisada', 'cash_boxes.reference',
        'p.name as producto', 'ln.quantity', 'ln.precio', 'ln.total as total_ln', 'ln.movement_id')
        /*'pm.name as payment_method',
        'pay.monto', 'pay.porcentaje_descuento', 'pay.fecha as fecha_pago')*/
        ->join('ln_cash_boxes as ln','ln.cash_box_id','cash_boxes.id')
        ->join('st_cash_boxes as stcb', 'stcb.id','cash_boxes.st_cash_box_id')
        ->join('products as p','p.id','ln.product_id')
        //->join('payments as pay', 'pay.cash_box_id', 'cash_boxes.id')
        //->join('payment_methods as pm','pm.id','pay.payment_method_id')
        ->whereBetween('cash_boxes.fecha', array($datos['fecha_f'], $datos['fecha_t']))
        ->whereIn('cash_boxes.plantel_id', $datos['plantel_id'])
        ->where('st_cash_box_id', 2)
        //->whereNull('pay.deleted_at')
        ->whereNull('ln.deleted_at')
        ->whereRaw('(bnd_entregado is null or bnd_entregado=0)')
        ->get();
        //dd($cajasApartadas->toArray());
        return Inertia::render(
            'CashBoxes/reportes/RptCajasApartadasR',
            [
                'cajasApartadas' => $cajasApartadas
            ]
        );
    }

    public function rptIngresosEgresos(){
        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value" => null, 'label' => "Selecionar Opción"]);
        $plantelesPluck=Plantel::pluck('id');
        $cuentas = Account::get()->map(fn ($account) => [
            'value' => $account->id,
            'label' => $account->code." ".$account->name,
        ]);
        $cuentas->prepend(["value" => null, 'label' => "Selecionar Opción"]);
        $cuentasPluck=Account::pluck('id');
        //dd($plantelesPluck);
        return Inertia::render('CashBoxes/reportes/RptIngresosEgresos',['planteles'=>$planteles,
        "cuentas"=>$cuentas, 'cuentasPluck'=>$cuentasPluck, 'plantelesPluck'=>$plantelesPluck
    ]);
    }

    public function rptIngresosEgresosR(Request $request){
        $datos=$request->all();
        //dd($datos);
        $ingresos=CashBox::select('cash_boxes.id as cash_box_id','cash_boxes.customer','pla.name as plantel',
        'cash_boxes.matricula', 'cash_boxes.bnd_entregado', 'stp.name as estatus_pago', 'p.fecha', 'ln.total as monto',
        'pro.name as producto',
        'c.name as cuenta', 'c.code as codigo')
        ->join ('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
        ->join('products as pro', 'pro.id', 'ln.product_id')
        ->join('accounts as c','c.id', 'pro.account_id')
        ->join('payments as p', 'p.cash_box_id','cash_boxes.id')
        ->join('plantels as pla', 'pla.id', 'cash_boxes.plantel_id')
        ->join('st_payments as stp', 'stp.id', 'p.st_payment_id')
        ->join('payment_methods as pm', 'pm.id', 'p.payment_method_id')
        ->where('st_cash_box_id', 2)
        ->where('st_payment_id', 2)
        ->whereBetween('p.fecha', array($datos['fecha_f'], $datos['fecha_t']))
        ->whereIn('cash_boxes.plantel_id', $datos['plantel_id'])
        ->whereIn('pro.account_id', $datos['account_id'])
        ->orderBy('c.id')
        ->orderBy('pla.id')
        ->get();

        //dd($ingresos->toArray());

        $egresos=Expense::select('expenses.id','pla.name as plantel', 'o.name as concepto',
        'c.name as cuenta','c.code as codigo','monto', 'fecha')
        ->whereBetween('expenses.fecha', array($datos['fecha_f'], $datos['fecha_t']))
        ->whereIn('expenses.plantel_id', $datos['plantel_id'])
        ->whereIn('expenses.account_id', $datos['account_id'])
        ->join('plantels as pla', 'pla.id', 'expenses.plantel_id')
        ->join('outputs as o', 'o.id', 'expenses.output_id')
        ->join('accounts as c', 'c.id', 'expenses.account_id')
        ->orderBy('c.id')
        ->orderBy('pla.id')
        ->get();

        //dd($egresos->toArray());

        return Inertia::render('CashBoxes/reportes/RptIngresosEgresosR', ['ingresos' => $ingresos,
        'egresos' => $egresos,
        'fecha1' => date_format(date_create($datos['fecha_f']),'Y-m-d'),
        'fecha2' => date_format(date_create($datos['fecha_t']), 'Y-m-d'),
    ]);
    }
}
