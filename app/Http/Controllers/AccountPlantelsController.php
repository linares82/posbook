<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Models\AccountPlantel;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AccountPlantelsCreateRequest;
use App\Http\Requests\AccountPlantelsUpdateRequest;

class AccountPlantelsController extends Controller
{
    public function getPermissions()
    {
        $permissions['accountPlantelsIndex'] = Auth::user()->hasPermissionTo('accountPlantels.index');
        $permissions['accountPlantelsCreate'] = Auth::user()->hasPermissionTo('accountPlantels.create');
        $permissions['accountPlantelsStore'] = Auth::user()->hasPermissionTo('accountPlantels.store');
        $permissions['accountPlantelsEdit'] = Auth::user()->hasPermissionTo('accountPlantels.edit');
        $permissions['accountPlantelsUpdate'] = Auth::user()->hasPermissionTo('accountPlantels.update');
        $permissions['accountPlantelsShow'] = Auth::user()->hasPermissionTo('accountPlantels.show');
        $permissions['accountPlantelsDestroy'] = Auth::user()->hasPermissionTo('accountPlantels.destroy');

        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $plantel)
    {
        //dd($plantel);
        $sysMessage = $request->session()->get('sysMessage');
        $accountPlantels = AccountPlantel::query()->where('plantel_id', $plantel)
            ->when($request->input('name'), function ($query, $item) {
                $query->where('name', 'like', '%' . $item . '%');
            })->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString()
            ->through(fn ($accountPlantel) => [
                'id' => $accountPlantel->id,
                'plantel_id' => $accountPlantel->plantel_id,
                'plantel' => $accountPlantel->plantel->name,
                'account_id' => $accountPlantel->account_id,
                'account' => $accountPlantel->account->name,
                'fecha_inicio' => $accountPlantel->fecha_inicio,
                'saldo_ingresos' => $accountPlantel->saldo_ingresos,
                'saldo_egresos' => $accountPlantel->saldo_egresos
            ]);
        //dd($users);

        return Inertia::render('AccountPlantels/Index', [
            'accountPlantels' => $accountPlantels,
            'filters' => $request->only(['name']),
            'sysMessage' => $sysMessage,
            'permissions' => $this->getPermissions(),
            'plantel' => $plantel,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('AccountPlantels/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountPlantelsCreateRequest $request, $plantel)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $accounts = Account::get();
            foreach ($accounts as $account) {
                $revisar_existencia = AccountPlantel::where('plantel_id', $plantel)->where('account_id', $account->id)->first();
                if (is_null($revisar_existencia)) {
                    $input['account_id'] = $account->id;
                    $input['plantel_id'] = $plantel;
                    $input['fecha_inicio'] = Date('Y-m-d');
                    $input['saldo_ingresos'] = 0;
                    $input['saldo_egresos'] = 0;
                    $input['diferencia'] = 0;
                    $accountPlantel = AccountPlantel::create($input);
                }
            }
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('accountPlantels.index', $plantel)->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $accountPlantel = AccountPlantel::with('plantel')->with('account')->findOrfail($id);
        //dd($accountPlantel->toArray());

        $diferencia=$accountPlantel->saldo_ingresos-$accountPlantel->saldo_egresos;
        $cash_boxes=CashBox::select('cash_boxes.id as cash_box_id', 'cash_boxes.fecha',
        'p.name as producto', 'ln.total as monto')
        ->join('ln_cash_boxes as ln', 'ln.cash_box_id', 'cash_boxes.id')
        ->join('products as p','p.id', 'ln.product_id')
        ->whereNull('cash_boxes.deleted_at')
        ->whereNull('ln.deleted_at')
        ->whereNull('p.deleted_at')
        ->where('p.account_id', $accountPlantel->account_id)
        ->where('cash_boxes.plantel_id', $accountPlantel->plantel_id)
        ->where('fecha','>=', $accountPlantel->fecha_inicio)
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
        ->where('expenses.account_id', $accountPlantel->account_id)
        ->where('expenses.plantel_id', $accountPlantel->plantel_id)
        ->where('expenses.fecha','>=', $accountPlantel->fecha_inicio)
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

        return Inertia::render('AccountPlantels/Show', ['accountPlantel' => $accountPlantel,
        'diferencia'=>$diferencia,
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
        $accountPlantel = AccountPlantel::findOrfail($id);
        return Inertia::render('AccountPlantels/Edit', ['accountPlantel' => $accountPlantel]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AccountPlantelsUpdateRequest $request, $id)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $accountPlantel = AccountPlantel::findOrFail($id);
            $accountPlantel->name = $datos['name'];
            $accountPlantel->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('accountPlantels.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $accountPlantel = AccountPlantel::findOrFail($id);
        //dd($user);
        try {
            $accountPlantel->delete();
        } catch (Exception $e) {
            dd($e);
        }
        return redirect()->route('accountPlantels.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function consultaSaldo(Request $request){
        $datos=$request->all();
        //dd($datos);
        $resultado=AccountPlantel::where('plantel_id', $datos['plantel'])
        ->where('account_id',$datos['account'])
        ->first();
        //dd($resultado);
        return $resultado->saldo_ingresos-$resultado->saldo_egresos;
    }
}
