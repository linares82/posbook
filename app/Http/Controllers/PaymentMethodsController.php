<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PaymentMethodsCreateRequest;
use App\Http\Requests\PaymentMethodsUpdateRequest;

class PaymentMethodsController extends Controller
{
    public function getPermissions(){
        $permissions['paymentMethodsIndex']=Auth::user()->hasPermissionTo('paymentMethods.index');
        $permissions['paymentMethodsCreate']=Auth::user()->hasPermissionTo('paymentMethods.create');
        $permissions['paymentMethodsStore']=Auth::user()->hasPermissionTo('paymentMethods.store');
        $permissions['paymentMethodsEdit']=Auth::user()->hasPermissionTo('paymentMethods.edit');
        $permissions['paymentMethodsUpdate']=Auth::user()->hasPermissionTo('paymentMethods.update');
        $permissions['paymentMethodsShow']=Auth::user()->hasPermissionTo('paymentMethods.show');
        $permissions['paymentMethodsDestroy']=Auth::user()->hasPermissionTo('paymentMethods.destroy');
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
        $paymentMethods=PaymentMethod::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($paymentMethod)=>[
            'id'=>$paymentMethod->id,
            'name'=>$paymentMethod->name
        ]);
        //dd($users);

        return Inertia::render('PaymentMethods/Index',[
            'paymentMethods'=>$paymentMethods,
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
        return Inertia::render('PaymentMethods/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PaymentMethodsCreateRequest $request)
    {
        $datos=$request->all();
        $datos['bnd_exempt'] = (($datos['bnd_exempt']==true) ? 1 : 0);
        //dd($datos);
        try{
            $paymentMethod=PaymentMethod::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('paymentMethods.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $paymentMethod=PaymentMethod::findOrfail($id);
        
        return Inertia::render('PaymentMethods/Show', ['paymentMethod'=>$paymentMethod]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $paymentMethod=PaymentMethod::findOrfail($id);
        return Inertia::render('PaymentMethods/Edit', ['paymentMethod'=>$paymentMethod]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PaymentMethodsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $paymentMethod=PaymentMethod::findOrFail($id);
            $paymentMethod->name=$datos['name'];
            $paymentMethod->porcentaje_descuento=$datos['porcentaje_descuento'];
            $paymentMethod->bnd_exempt=$datos['bnd_exempt']==true ? 1 : 0;
            $paymentMethod->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('paymentMethods.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paymentMethod=PaymentMethod::findOrFail($id);
        //dd($user);
        try{
            $paymentMethod->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('paymentMethods.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function consultaPorcentajeDescuento(Request $request){
        //dd($request->all());
        $registro=PaymentMethod::findOrFail($request->input('id'));
        return $registro->toJson();
    }
}
