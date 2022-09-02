<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\CashBox;
use App\Models\Plantel;
use App\Models\Product;
use App\Models\StCashBox;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CashBoxsCreateRequest;
use App\Http\Requests\CashBoxsUpdateRequest;

class CashBoxesController extends Controller
{
    public function getPermissions(){
        $permissions['cashBoxesIndex']=Auth::user()->hasPermissionTo('cashBoxes.index');
        $permissions['cashBoxesCreate']=Auth::user()->hasPermissionTo('cashBoxes.create');
        $permissions['cashBoxesStore']=Auth::user()->hasPermissionTo('cashBoxes.store');
        $permissions['cashBoxesEdit']=Auth::user()->hasPermissionTo('cashBoxes.edit');
        $permissions['cashBoxesUpdate']=Auth::user()->hasPermissionTo('cashBoxes.update');
        $permissions['cashBoxesShow']=Auth::user()->hasPermissionTo('cashBoxes.show');
        $permissions['cashBoxesDestroy']=Auth::user()->hasPermissionTo('cashBoxes.destroy');
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
        $cashBoxes=CashBox::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($cashBox)=>[
            'id'=>$cashBox->id,
            'cliente'=>$cashBox->customer,
            'fecha'=>$cashBox->fecha,
            'estatus'=>$cashBox->stCashBox->name,
            'total'=>$cashBox->total,
        ]);
        //dd($users);

        return Inertia::render('CashBoxes/Index',[
            'cashBoxes'=>$cashBoxes,
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
        $planteles=Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value"=>null,'label'=>"Selecionar Opción"]);

        $estatus=StCashBox::get()->map(fn ($stCashBox) => [
            'value' => $stCashBox->id,
            'label' => $stCashBox->name,
        ]);
        $estatus->prepend(["value"=>null,'label'=>"Selecionar Opción"]);

        $productos=Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);
        $productos->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        //dd();
        $plantel=Auth::user()->plantels->first()->id;

        $paymentMethods=PaymentMethod::get()->map(fn ($paymentMethod) => [
            'value' => $paymentMethod->id,
            'label' => $paymentMethod->name,
        ]);
        $paymentMethods->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        
        $ruta_productos_findById=route('products.findById');
        //dd($ruta_productos_findById);

        return Inertia::render('CashBoxes/Create', 
        ['planteles'=>$planteles, 'estatus'=>$estatus, 'productos'=>$productos, 'plantel'=>$plantel,'paymentMethods'=>$paymentMethods,'ruta_productos_findById'=>$ruta_productos_findById]);
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
        $datos=$request->all();
        //dd($datos);
        try{
            $cashBoxe=CashBox::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cashBoxes.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cashBoxe=CashBox::findOrfail($id);
        
        return Inertia::render('CashBoxs/Show', ['cashBoxe'=>$cashBoxe]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cashBoxe=CashBox::findOrfail($id);
        return Inertia::render('CashBoxes/Edit', ['cashBoxe'=>$cashBoxe]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CashBoxsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $cashBoxe=CashBox::findOrFail($id);
            $cashBoxe->name=$datos['name'];
            $cashBoxe->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cashBoxes.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $cashBoxe=CashBox::findOrFail($id);
        //dd($user);
        try{
            $cashBoxe->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('cashBoxes.index')->with('sysMessage', 'Registro Borrado.');
    }

}
