<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\Expense;
use App\Models\Plantel;
use Illuminate\Http\Request;
use App\Models\AccountPlantel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\DetalleAccountPlantel;
use App\Http\Requests\AccountsCreateRequest;
use App\Http\Requests\AccountsUpdateRequest;

class AccountsController extends Controller
{
    public function getPermissions(){
        $permissions['accountsIndex']=Auth::user()->hasPermissionTo('accounts.index');
        $permissions['accountsCreate']=Auth::user()->hasPermissionTo('accounts.create');
        $permissions['accountsStore']=Auth::user()->hasPermissionTo('accounts.store');
        $permissions['accountsEdit']=Auth::user()->hasPermissionTo('accounts.edit');
        $permissions['accountsUpdate']=Auth::user()->hasPermissionTo('accounts.update');
        $permissions['accountsShow']=Auth::user()->hasPermissionTo('accounts.show');
        $permissions['accountsDestroy']=Auth::user()->hasPermissionTo('accounts.destroy');
        $permissions['accountShowSaldos'] = Auth::user()->hasPermissionTo('accounts.showSaldos');
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
        $accounts=Account::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($account)=>[
            'id'=>$account->id,
            'code'=>$account->code,
            'name'=>$account->name,
            'bnd_ingreso'=>$account->bnd_ingreso ? "Si" : "No",
            'bnd_egreso'=>$account->bnd_egreso ? "Si" : "No",
            'fecha_inicio'=>$account->fecha_inicio,
            'saldo_ingresos'=>$account->saldo_ingresos,
            'saldo_egresos'=>$account->saldo_egresos,
        ]);
        //dd($users);

        return Inertia::render('Accounts/Index',[
            'accounts'=>$accounts,
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
        $planteles=Plantel::plantelsCmb();$planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        return Inertia::render('Accounts/Create', ['planteles' => $planteles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $account=Account::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('accounts.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $account=Account::findOrfail($id);
        //dd($account);
        $diferencia=$account->saldo_ingresos-$account->saldo_egresos;
        $cash_boxes=CashBox::select('cash_boxes.id as cash_box_id', 'cash_boxes.fecha',
        'p.name as producto', 'ln.total as monto')
        ->join('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
        ->join('products as p','p.id', 'ln.product_id')
        ->whereNull('cash_boxes.deleted_at')
        ->whereNull('ln.deleted_at')
        ->whereNull('p.deleted_at')
        ->where('p.account_id', $account->id)
        ->where('fecha','>=', $account->fecha_inicio)
        ->where('st_cash_box_id',2)
        ->orderBy('cash_boxes.fecha', 'asc')
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
        ->where('expenses.account_id', $account->id)
        ->where('expenses.fecha','>=', $account->fecha_inicio)
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
        //dd($cash_boxes->toArray());
        return Inertia::render('Accounts/Show', ['account'=>$account, 'diferencia'=>$diferencia,
        "cash_boxes"=>$cash_boxes,'expenses'=>$expenses, 'suma_egresos'=>$suma_egresos,
        'suma_ingresos'=>$suma_ingresos
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
        $account=Account::findOrfail($id);
        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        return Inertia::render('Accounts/Edit', ['account'=>$account, 'planteles'=>$planteles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $account=Account::findOrFail($id);
            //$account->name=$datos['name'];
            $account->update($datos);

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('accounts.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $account=Account::findOrFail($id);
        //dd($user);
        try{
            $account->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('accounts.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function showSaldos($id)
    {
        $account=Account::findOrfail($id);

        $account_plantel=AccountPlantel::select('account_plantel.*','p.name as plantel','a.name as account')
        ->where('account_id', $id)
        ->join('plantels as p','p.id','account_plantel.plantel_id')
        ->join('accounts as a','a.id','account_plantel.account_id')
        ->whereNull('p.deleted_at')
        ->whereNull('a.deleted_at')
        ->orderBy('account_plantel.plantel_id', 'asc')
        ->get();
        //dd($account_plantel->toArray());

        return Inertia::render('Accounts/ShowSaldos', ['account'=>$account,
        'accountPlantels'=>$account_plantel,
    ]);
    }

}
