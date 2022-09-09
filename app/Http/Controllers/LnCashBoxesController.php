<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\LnCashBox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LnCashBoxsCreateRequest;
use App\Http\Requests\LnCashBoxsUpdateRequest;

class LnCashBoxesController extends Controller
{
    public function getPermissions(){
        $permissions['lnCashBoxesIndex']=Auth::user()->hasPermissionTo('lnCashBoxes.index');
        $permissions['lnCashBoxesCreate']=Auth::user()->hasPermissionTo('lnCashBoxes.create');
        $permissions['lnCashBoxesStore']=Auth::user()->hasPermissionTo('lnCashBoxes.store');
        $permissions['lnCashBoxesEdit']=Auth::user()->hasPermissionTo('lnCashBoxes.edit');
        $permissions['lnCashBoxesUpdate']=Auth::user()->hasPermissionTo('lnCashBoxes.update');
        $permissions['lnCashBoxesShow']=Auth::user()->hasPermissionTo('lnCashBoxes.show');
        $permissions['lnCashBoxesDestroy']=Auth::user()->hasPermissionTo('lnCashBoxes.destroy');
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
        $lnCashBoxes=LnCashBox::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($period)=>[
            'id'=>$period->id,
            'name'=>$period->name
        ]);
        //dd($users);

        return Inertia::render('LnCashBoxs/Index',[
            'lnCashBoxes'=>$lnCashBoxes,
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
        return Inertia::render('LnCashBoxs/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LnCashBoxsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $period=LnCashBox::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('lnCashBoxes.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $period=LnCashBox::findOrfail($id);
        
        return Inertia::render('LnCashBoxs/Show', ['period'=>$period]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $period=LnCashBox::findOrfail($id);
        return Inertia::render('LnCashBoxs/Edit', ['period'=>$period]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LnCashBoxsUpdateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $lnCashBox=LnCashBox::findOrFail($datos['linea_id']);
            //dd($lnCashBox);
            $lnCashBox->product_id=$datos['product_id'];
            $lnCashBox->precio=$datos['precio'];
            $lnCashBox->quantity=$datos['quantity'];
            $lnCashBox->total=$lnCashBox->precio*$lnCashBox->quantity;
            $lnCashBox->save();

            $cashBox=$lnCashBox->cashBox;
            //dd($cashBox);
            $cashBox->total=$lnCashBox->total;
            $cashBox->save();
            
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('cashBoxes.edit',$cashBox->id)->with('sysMessage', 'Registro Actualizado.');
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
        
        $lnCashBox=LnCashBox::findOrFail($datos['id']);
        $cashBox=$lnCashBox->cash_box_id;
        //dd($user);
        try{
            $lnCashBox->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('cashBoxes.edit', $cashBox)->with('sysMessage', 'Registro Borrado.');
    }

}
