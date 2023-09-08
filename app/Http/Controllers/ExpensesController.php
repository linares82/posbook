<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExpensesCreateRequest;
use App\Http\Requests\ExpensesUpdateRequest;

class ExpensesController extends Controller
{
    public function getPermissions(){
        $permissions['expensesIndex']=Auth::user()->hasPermissionTo('expenses.index');
        $permissions['expensesCreate']=Auth::user()->hasPermissionTo('expenses.create');
        $permissions['expensesStore']=Auth::user()->hasPermissionTo('expenses.store');
        $permissions['expensesEdit']=Auth::user()->hasPermissionTo('expenses.edit');
        $permissions['expensesUpdate']=Auth::user()->hasPermissionTo('expenses.update');
        $permissions['expensesShow']=Auth::user()->hasPermissionTo('expenses.show');
        $permissions['expensesDestroy']=Auth::user()->hasPermissionTo('expenses.destroy');
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
        $expenses=Expense::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($expense)=>[
            'id'=>$expense->id,
            'name'=>$expense->name
        ]);
        //dd($users);

        return Inertia::render('Expenses/Index',[
            'expenses'=>$expenses,
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
        return Inertia::render('Expenses/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpensesCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $expense=Expense::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('expenses.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $expense=Expense::findOrfail($id);

        return Inertia::render('Expenses/Show', ['expense'=>$expense]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense=Expense::findOrfail($id);
        return Inertia::render('Expenses/Edit', ['expense'=>$expense]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExpensesUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $expense=Expense::findOrFail($id);
            $expense->name=$datos['name'];
            $expense->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('expenses.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense=Expense::findOrFail($id);
        //dd($user);
        try{
            $expense->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('expenses.index')->with('sysMessage', 'Registro Borrado.');
    }

}
