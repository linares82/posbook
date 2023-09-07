<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'bnd_egreso'=>$account->bnd_egreso ? "Si" : "No"
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
        return Inertia::render('Accounts/Create');
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

        return Inertia::render('Accounts/Show', ['account'=>$account]);
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
        return Inertia::render('Accounts/Edit', ['account'=>$account]);
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

}
