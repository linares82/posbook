<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\CashBox;
use App\Models\Payment;
use App\Models\Plantel;
use App\Models\Product;
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
        $permissions['cashBoxesCancelCashBox'] = Auth::user()->hasPermissionTo('cashBoxes.cancelCashBox');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sysMessage = $request->session()->get('sysMessage');
        $cashBoxes = CashBox::query()
            ->when($request->input('name'), function ($query, $item) {
                $query->where('name', 'like', '%' . $item . '%');
            })->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString()
            ->through(fn ($cashBox) => [
                'id' => $cashBox->id,
                'plantel'=>$cashBox->plantel->name,
                'cliente' => $cashBox->customer,
                'fecha' => $cashBox->fecha,
                'estatus' => $cashBox->stCashBox->name,
                'total' => $cashBox->total,
            ]);
        //dd($users);

        return Inertia::render('CashBoxes/Index', [
            'cashBoxes' => $cashBoxes,
            'filters' => $request->only(['name']),
            'sysMessage' => $sysMessage,
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
        $planteles = Plantel::get()->map(fn ($plantel) => [
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
        //dd($ruta_productos_findById);



        return Inertia::render(
            'CashBoxes/Create',
            ['planteles' => $planteles, 'estatus' => $estatus, 'productos' => $productos, 'plantel' => $plantel, 'paymentMethods' => $paymentMethods, 'ruta_productos_findById' => $ruta_productos_findById]
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
            'bnd_entregado'=>$caja->bnd_entregado
        );
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
                'deleted' => 0
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
        $planteles = Plantel::get()->map(fn ($plantel) => [
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
        //dd($ruta_productos_findById);


        return Inertia::render(
            'CashBoxes/Edit',
            [
                'planteles' => $planteles, 'estatus' => $estatus, 'productos' => $productos, 'plantel' => $plantel,
                'paymentMethods' => $paymentMethods, 'ruta_productos_findById' => $ruta_productos_findById,
                'cashBox' => $cashBox, 'ruta_update_ln' => $ruta_update_ln, 'ruta_destroy_ln' => $ruta_destroy_ln,
                'ruta_update_payment' => $ruta_update_payment, 'ruta_destroy_payment' => $ruta_destroy_payment,
                'ruta_update_cashBox' => $ruta_update_cashBox
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
            if(!isset($datos['bnd_entregado'])){
                $cashBox->bnd_entregado=0;
            }else{
                $cashBox->bnd_entregado=$datos['bnd_entregado'];
            }
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
        $cashBox->st_cash_box_id=4;
        $cashBox->save();

        //dd($cashBox);
        foreach($cashBox->lnCashBoxes as $linea){
            //dd($linea);
            $linea->delete();
        }

        foreach($cashBox->payments as $payment){
            //dd($payment);
            $payment->delete();
        }
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
}
