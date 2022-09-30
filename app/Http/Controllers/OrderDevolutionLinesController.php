<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Movement;
use Illuminate\Http\Request;
use App\Models\OrderDevolutionLine;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderDevolutionLinesCreateRequest;
use App\Http\Requests\OrderDevolutionLinesUpdateRequest;

class OrderDevolutionLinesController extends Controller
{
    public function getPermissions()
    {
        $permissions['orderDevolutionLinesIndex'] = Auth::user()->hasPermissionTo('orderDevolutionLines.index');
        $permissions['orderDevolutionLinesCreate'] = Auth::user()->hasPermissionTo('orderDevolutionLines.create');
        $permissions['orderDevolutionLinesStore'] = Auth::user()->hasPermissionTo('orderDevolutionLines.store');
        $permissions['orderDevolutionLinesEdit'] = Auth::user()->hasPermissionTo('orderDevolutionLines.edit');
        $permissions['orderDevolutionLinesUpdate'] = Auth::user()->hasPermissionTo('orderDevolutionLines.update');
        $permissions['orderDevolutionLinesShow'] = Auth::user()->hasPermissionTo('orderDevolutionLines.show');
        $permissions['orderDevolutionLinesDestroy'] = Auth::user()->hasPermissionTo('orderDevolutionLines.destroy');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $sysMessage = $request->session()->get('sysMessage');
        $orderDevolutionLines = OrderDevolutionLine::query()
            ->when($request->input('name'), function ($query, $item) {
                $query->where('name', 'like', '%' . $item . '%');
            })->orderBy('id', 'desc')
            ->paginate(100)
            ->withQueryString()
            ->through(fn ($orderDevolutionLine) => [
                'id' => $orderDevolutionLine->id,
                'name' => $orderDevolutionLine->name
            ]);
        //dd($users);

        return Inertia::render('OrderDevolutionLines/Index', [
            'orderDevolutionLines' => $orderDevolutionLines,
            'filters' => $request->only(['name']),
            'sysMessage' => $sysMessage,
            'permissions' => $this->getPermissions()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('OrderDevolutionLines/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderDevolutionLinesCreateRequest $request)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $orderDevolutionLine = OrderDevolutionLine::create($datos);
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('orderDevolutionLines.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDevolutionLine = OrderDevolutionLine::findOrfail($id);

        return Inertia::render('OrderDevolutionLines/Show', ['orderDevolutionLine' => $orderDevolutionLine]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderDevolutionLine = OrderDevolutionLine::findOrfail($id);
        return Inertia::render('OrderDevolutionLines/Edit', ['orderDevolutionLine' => $orderDevolutionLine]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderDevolutionLinesUpdateRequest $request, $id)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $orderDevolutionLine = OrderDevolutionLine::findOrFail($id);
            $orderDevolutionLine->name = $datos['name'];
            $orderDevolutionLine->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('orderDevolutionLines.index')->with('sysMessage', 'Registro Actualizado.');
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
        $orderDevolutionLine = OrderDevolutionLine::findOrFail($datos['id']);
        $id=$orderDevolutionLine->order_devolution_id;
        try {
            $orderDevolutionLine->delete();
        } catch (Exception $e) {
            dd($e);
        }

        //return redirect()->route('orderDevolutions.show',$id)->with('sysMessage', 'Registro Borrado.');
    }

    public function storeLines(Request $request)
    {
        $registros = $request->all();
        $id = 0;
        foreach ($registros as $registro) {
            $id = $registro['order_devolution_id'];
            if ($registro['cantidad_devolucion'] > 0) {
                //dd($registro);
                $input['order_devolution_id'] = $registro['order_devolution_id'];
                $input['plantel_id'] = $registro['plantel_id'];
                $input['product_id'] = $registro['producto_id'];
                $input['cantidad'] = $registro['cantidad_devolucion'];
                $input['usu_alta_id'] = Auth::user()->id;
                $input['usu_mod_id'] = Auth::user()->id;
                $input['movement_id']=$registro['id'];
                OrderDevolutionLine::create($input);
                /*$movement=Movement::find($registro['id']);
                $movement->salida=$movement->salida+$registro['cantidad_devolucion'];
                $movement->save();
                */
            }
        }
        return redirect()->route('orderDevolutions.show', $id);
    }

    public function registrarDevolucion($id) {
        
        try {
            $orderDevolutionLine = OrderDevolutionLine::find($id);
            $orderDevolutionLine->bnd_salida_registrada=1;
            $orderDevolutionLine->save();
        } catch (Exception $e) {
            dd($e);
        }

    }

    
}
