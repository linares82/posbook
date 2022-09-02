<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\StPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StPaymentsCreateRequest;
use App\Http\Requests\StPaymentsUpdateRequest;

class StPaymentsController extends Controller
{
    public function getPermissions(){
        $permissions['stPaymentsIndex']=Auth::user()->hasPermissionTo('stPayments.index');
        $permissions['stPaymentsCreate']=Auth::user()->hasPermissionTo('stPayments.create');
        $permissions['stPaymentsStore']=Auth::user()->hasPermissionTo('stPayments.store');
        $permissions['stPaymentsEdit']=Auth::user()->hasPermissionTo('stPayments.edit');
        $permissions['stPaymentsUpdate']=Auth::user()->hasPermissionTo('stPayments.update');
        $permissions['stPaymentsShow']=Auth::user()->hasPermissionTo('stPayments.show');
        $permissions['stPaymentsDestroy']=Auth::user()->hasPermissionTo('stPayments.destroy');
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
        $stPayments=StPayment::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($stPayment)=>[
            'id'=>$stPayment->id,
            'name'=>$stPayment->name
        ]);
        //dd($users);

        return Inertia::render('StPayments/Index',[
            'stPayments'=>$stPayments,
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
        return Inertia::render('StPayments/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StPaymentsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stPayment=StPayment::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stPayments.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $stPayment=StPayment::findOrfail($id);
        
        return Inertia::render('StPayments/Show', ['stPayment'=>$stPayment]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stPayment=StPayment::findOrfail($id);
        return Inertia::render('StPayments/Edit', ['stPayment'=>$stPayment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StPaymentsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $stPayment=StPayment::findOrFail($id);
            $stPayment->name=$datos['name'];
            $stPayment->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('stPayments.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stPayment=StPayment::findOrFail($id);
        //dd($user);
        try{
            $stPayment->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('stPayments.index')->with('sysMessage', 'Registro Borrado.');
    }

}
