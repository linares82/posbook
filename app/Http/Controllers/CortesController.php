<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Corte;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\AccountPlantel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CortesCreateRequest;
use App\Http\Requests\CortesUpdateRequest;

class CortesController extends Controller
{
    public function getPermissions(){
        $permissions['cortesIndex']=Auth::user()->hasPermissionTo('cortes.index');
        $permissions['cortesCreate']=Auth::user()->hasPermissionTo('cortes.create');
        $permissions['cortesStore']=Auth::user()->hasPermissionTo('cortes.store');
        $permissions['cortesEdit']=Auth::user()->hasPermissionTo('cortes.edit');
        $permissions['cortesUpdate']=Auth::user()->hasPermissionTo('cortes.update');
        $permissions['cortesShow']=Auth::user()->hasPermissionTo('cortes.show');
        $permissions['cortesDestroy']=Auth::user()->hasPermissionTo('cortes.destroy');
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
        $cortes=Corte::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($corte)=>[
            'id'=>$corte->id,
            'account_plantel_id'=>$corte->account_id,
            'account'=>$corte->accountPlantel->account->name,
            'plantel'=>$corte->accountPlantel->plantel->name,
            'fecha_inicio'=>$corte->fecha_inicio,
            'fecha_fin'=>$corte->fecha_fin,
            'saldo_egresos'=>$corte->saldo_egresos,
            'saldo_ingresos'=>$corte->saldo_ingresos
        ]);
        //dd($users);

        return Inertia::render('Cortes/Index',[
            'cortes'=>$cortes,
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
        return Inertia::render('Cortes/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CortesCreateRequest $request)
    {

        $datos=$request->except(['cash_boxes', 'expenses']);

        $cajas=$request->only('cash_boxes');
        $egresos=$request->only('expenses');
        //dd($egresos['expenses']);
        try{
            $corte=Corte::create($datos);
            //dd($corte->account_plantel_id);
            $accountPlantel=AccountPlantel::find($corte->account_plantel_id);
            //dd($corte->account_plantel_id);
            if($corte->saldo_ingresos>=$corte->saldo_egresos){
                $accountPlantel->saldo_ingresos=$corte->saldo_ingresos-$corte->saldo_egresos;
                $accountPlantel->saldo_egresos=0;
            }else{
                $accountPlantel->saldo_egresos=$corte->saldo_egresos-$corte->saldo_ingresos;
                $accountPlantel->saldo_ingresos=0;
            }
            //dd(Carbon::createFromFormat('Y-m-d', Date('Y-m-d'))->addDay()->format('Y-m-d\TH:i:s.000\Z'));
            //dd($datos['fecha_fin']);
            $accountPlantel->fecha_inicio=Carbon::createFromFormat('Y-m-d', Date('Y-m-d'))->addDay()->format('Y-m-d');
            //dd($account);
            $accountPlantel->save();
            foreach($cajas['cash_boxes'] as $caja){
                //dd($caja['cash_box_id']);
                $payments=Payment::where('cash_box_id', $caja['cash_box_id'])->get();
                foreach($payments as $payment){
                    $payment->corte_id=$corte->id;
                    $payment->save();
                }
            }
            foreach($egresos['expenses'] as $egreso){
                $expense=Expense::find($egreso['expense_id']);
                $expense->corte_id=$corte->id;
                $expense->save();
            }

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cortes.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $corte=Corte::with('accountPlantel')->findOrfail($id);
        $plantel=$corte->accountPlantel->plantel;
        $account=$corte->accountPlantel->account;
        //dd($account);

        $cash_boxes=CashBox::select('cash_boxes.id as cash_box_id', 'cash_boxes.fecha',
        'p.name as producto', 'ln.total as monto')
        ->join('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
        ->join('products as p','p.id', 'ln.product_id')
        ->join('payments as pay','pay.cash_box_id', 'cash_boxes.id')
        ->whereNull('cash_boxes.deleted_at')
        ->whereNull('ln.deleted_at')
        ->whereNull('p.deleted_at')
        ->where('pay.corte_id', $corte->id)
        ->where('st_cash_box_id',2)
        ->orderBy('cash_boxes.fecha', 'asc')
        ->distinct()
        ->get()
        ->map(fn($box)=>[
            'cash_box_id'=>$box->cash_box_id,
            'fecha'=>$box->fecha,
            'monto'=>$box->monto,
            'producto'=>$box->producto,
            'url_consultar_ingreso'=>route('cashBoxes.edit', $box->cash_box_id)
        ]);
        //dd($cash_boxes->toArray());

        $suma_ingresos=0;
        foreach($cash_boxes as $box){
            $suma_ingresos=$suma_ingresos+$box['monto'];
        }

        $expenses=Expense::select('expenses.id as expense_id', 'expenses.fecha',
        'o.name as egreso', 'expenses.monto')
        ->join('outputs as o','o.id', 'expenses.output_id')
        ->where('expenses.corte_id', $corte->id)
        ->orderBy('expenses.fecha', 'asc')
        ->get()
        ->map(fn($box)=>[
            'expense_id'=>$box->expense_id,
            'fecha'=>$box->fecha,
            'monto'=>$box->monto,
            'egreso'=>$box->egreso,
            'url_consultar_egreso'=>route('expenses.edit', $box->expense_id)
        ]);
        $suma_egresos=0;
        foreach($expenses as $expense){
            $suma_egresos=$suma_egresos+$expense['monto'];
        }

        return Inertia::render('Cortes/Show', ['corte'=>$corte, 'suma_egresos'=>$suma_egresos,
        'account'=>$account, 'plantel'=>$plantel,
        'suma_ingresos'=>$suma_ingresos, 'expenses'=>$expenses, 'cash_boxes'=>$cash_boxes
    ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $corte=Corte::findOrfail($id);
        return Inertia::render('Cortes/Edit', ['corte'=>$corte]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CortesUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $corte=Corte::findOrFail($id);
            $corte->name=$datos['name'];
            $corte->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cortes.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $corte=Corte::findOrFail($id);
        //dd($user);
        try{
            $corte->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('cortes.index')->with('sysMessage', 'Registro Borrado.');
    }

}
