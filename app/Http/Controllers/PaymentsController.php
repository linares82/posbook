<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\CashBox;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaymentsCreateRequest;
use App\Http\Requests\PaymentsUpdateRequest;

class PaymentsController extends Controller
{
    public function getPermissions(){
        $permissions['paymentsIndex']=Auth::user()->hasPermissionTo('payments.index');
        $permissions['paymentsCreate']=Auth::user()->hasPermissionTo('payments.create');
        $permissions['paymentsStore']=Auth::user()->hasPermissionTo('payments.store');
        $permissions['paymentsEdit']=Auth::user()->hasPermissionTo('payments.edit');
        $permissions['paymentsUpdate']=Auth::user()->hasPermissionTo('payments.update');
        $permissions['paymentsShow']=Auth::user()->hasPermissionTo('payments.show');
        $permissions['paymentsDestroy']=Auth::user()->hasPermissionTo('payments.destroy');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sysMessage=$request->session()->get('sysMessage');
        $payments=Payment::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($payment)=>[
            'id'=>$payment->id,
            'name'=>$payment->name
        ]);
        //dd($users);

        return Inertia::render('Payments/Index',[
            'payments'=>$payments,
            'filters'=>$request->only(['name']),
            'sysMessage'=>$sysMessage,
            'permissions'=>$this->getPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('Payments/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $payment=Payment::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('payments.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $payment=Payment::findOrfail($id);

        return Inertia::render('Payments/Show', ['payment'=>$payment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment=Payment::findOrfail($id);
        return Inertia::render('Payments/Edit', ['payment'=>$payment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentsUpdateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $payment=Payment::findOrFail($datos['payment_id']);
            $payment->monto=$datos['monto'];
            $payment->fecha=$datos['fechaPago'];
            $payment->save();

            $cashBox=$payment->cashBox;
            $sumaPagos=Payment::where('cash_box_id', $cashBox->id)->whereNull('deleted_at')->sum('monto');
            //dd($cashBox->total."-".$sumaPagos);
            if($sumaPagos<$cashBox->total){
                $cashBox->st_cash_box_id=3;
            }else{
                $cashBox->st_cash_box_id=2;
            }
            //dd($cashBox->ToArray());
            $cashBox->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cashBoxes.edit', $cashBox->id)->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $datos=$request->all();
        $payment=Payment::findOrFail($datos['id']);
        $cash_box=CashBox::findOrFail($payment->cash_box_id);


        //dd($user);
        try{
            $payment->delete();
            $sumaPagos=Payment::where('cash_box_id', $payment->cash_box_id)->whereNull('deleted_at')->sum('monto');
            if($sumaPagos<$cash_box->total and $sumaPagos>0){
                $cash_box->st_cash_box_id=3;
            }else if($sumaPagos==0){
                $cash_box->st_cash_box_id=1;
            }else{
                $cash_box->st_cash_box_id=2;
            }
            //dd($cashBox->ToArray());
            $cash_box->save();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('payments.index')->with('sysMessage', 'Registro Borrado.');
    }

}
