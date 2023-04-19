<?php

namespace App\Http\Controllers;

use DateTime;
use Exception;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Reason;
use App\Models\CashBox;
use App\Models\Payment;
use App\Models\Plantel;
use App\Models\Product;
use App\Models\Movement;
use App\Models\LnCashBox;
use App\Models\OrderSale;
use App\Models\TypeMovement;
use Illuminate\Http\Request;
use App\Models\OrderSalesLine;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MovementsCreateRequest;
use App\Http\Requests\MovementsUpdateRequest;

class MovementsController extends Controller
{
    public function getPermissions()
    {
        $permissions['movementsIndex'] = Auth::user()->hasPermissionTo('movements.index');
        $permissions['movementsCreate'] = Auth::user()->hasPermissionTo('movements.create');
        $permissions['movementsStore'] = Auth::user()->hasPermissionTo('movements.store');
        $permissions['movementsEdit'] = Auth::user()->hasPermissionTo('movements.edit');
        $permissions['movementsUpdate'] = Auth::user()->hasPermissionTo('movements.update');
        $permissions['movementsShow'] = Auth::user()->hasPermissionTo('movements.show');
        $permissions['movementsDestroy'] = Auth::user()->hasPermissionTo('movements.destroy');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($request->all());
        $sysMessage = $request->session()->get('sysMessage');
        $filtros = $request->input('search');

        $planteles=Auth::user()->plantels->pluck('id');
        //dd($planteles);

        //dd($filtros);
        $m = Movement::query();
        if (isset($filtros['plantel_id'])) {
            $m->when($filtros['plantel_id'], function ($query, $plantel_id) {
                $query->where('movements.plantel_id', $plantel_id);
            });
        }
        if (isset($filtros['reason_id'])) {
            $m->when($filtros['reason_id'], function ($query, $reason_id) {
                $query->where('reason_id', $reason_id);
            });
        }
        if (isset($filtros['type_movement_id'])) {
            $m->when($filtros['type_movement_id'], function ($query, $type_movement_id) {
                $query->where('type_movement_id', $type_movement_id);
            });
        }
        if (isset($filtros['product_id'])) {
            $m->when($filtros['product_id'], function ($query, $product_id) {
                $query->where('product_id', $product_id);
            });
        }

        if (isset($filtros['column']) and isset($filtros['direction'])) {
        $m->when($filtros, function ($query, $filtros) {
                $query->orderBy($filtros['column'], $filtros['direction']);

        });
        }
        $movements = $m->orderBy('movements.id', 'desc')
            ->join('users as u','u.id','movements.usu_alta_id')
            ->join('plantel_user as pu','pu.user_id','u.id')
            ->with('plantel','typeMovement','reason','plantel')
            ->whereIn('pu.plantel_id',$planteles)
            ->paginate(100)
            ->withQueryString()
            ->through(fn ($movement) => [
                'id' => $movement->id,
                'plantel' => $movement->plantel->name,
                'motivo' => $movement->reason->name,
                'tipo_movimiento' => $movement->typeMovement->name,
                'producto' => $movement->product->name,
                'costo' => $movement->costo,
                'precio' => $movement->precio,
                'entrada' => $movement->cantidad_entrada,
                'salida' => $movement->cantidad_salida
            ]);
        //dd($movements->plantel);

        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos = Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos = TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);

        return Inertia::render('Movements/Index', [
            'movements' => $movements,
            'planteles' => $planteles,
            'motivos' => $motivos,
            'tipo_movimientos' => $tipo_movimientos,
            'productos' => $productos,
            'filters' => $request->only(['name','column','direction']),
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
        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos = Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos = TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);

        return Inertia::render(
            'Movements/Create',
            ['planteles' => $planteles, 'motivos' => $motivos, 'tipo_movimientos' => $tipo_movimientos, 'productos' => $productos]
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MovementsCreateRequest $request)
    {
        $datos = $request->all();
        //dd($datos);
        $producto = Product::find($datos['product_id']);
        $datos['precio'] = $producto->precio;
        $datos['costo'] = $producto->costo;
        $datos['reason_id'] = 2;
        $datos['type_movement_id'] = 1;
        $orderSalesLine = OrderSalesLine::find($datos['order_sales_line_id']);
        try {
            $movement = Movement::create($datos);
            $sumaEntradas = Movement::where('order_sales_line_id', $movement->order_sale_id)->sum('cantidad_entrada');
            if ($orderSalesLine->cantidad == $sumaEntradas) {
                $orderSalesLine->bnd_entrada_registrada = 1;
                $orderSalesLine->save();
            }
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('orderSales.show', $orderSalesLine->order_sale_id)->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $movement = Movement::findOrfail($id);

        return Inertia::render('Movements/Show', ['movement' => $movement]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $movement = Movement::findOrfail($id);
        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $motivos = Reason::get()->map(fn ($reason) => [
            'value' => $reason->id,
            'label' => $reason->name,
        ]);
        $tipo_movimientos = TypeMovement::get()->map(fn ($typeMovement) => [
            'value' => $typeMovement->id,
            'label' => $typeMovement->name,
        ]);
        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);

        return Inertia::render('Movements/Edit', ['movement' => $movement]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MovementsUpdateRequest $request, $id)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $movement = Movement::findOrFail($id);
            $movement->name = $datos['name'];
            $movement->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('movements.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $movement = Movement::findOrFail($id);
        $orderSalesId = $movement->orderSalesLine->order_sale_id;


        //dd($user);
        try {
            if (!is_null($movement->cantidad_salida) and $movement->cantidad_salida != 0) {
                foreach ($movement->lnCashBoxes as $lnCashBox) {
                    $lnCashBox->movement_id = null;
                    $lnCashBox->save();
                }
            }
            $movement->delete();
        } catch (Exception $e) {
            dd($e);
        }
        return redirect()->route('orderSales.show', $orderSalesId)->with('sysMessage', 'Registro Borrado.');
    }

    public function consultaExistencias(Request $request)
    {
        $datos = $request->all();
        $existencias = Movement::select(
            'movements.id',
            'p.name as plantel',
            'p.id as plantel_id',
            'pro.name as producto',
            'pro.id as producto_id',
            'cantidad_entrada',
            'cantidad_salida'
        )
            ->join('plantels as p', 'p.id', 'movements.plantel_id')
            ->join('products as pro', 'pro.id', 'movements.product_id')
            ->join('order_sales_lines as osl', 'osl.id', 'movements.order_sales_line_id')
            ->join('order_sales as os', 'os.id', 'osl.order_sale_id')
            ->whereIn('movements.plantel_id', Auth::user()->plantels->pluck('id'))
            ->where('movements.type_movement_id', 1)
            ->where('os.id', $datos['id'])
            ->whereColumn('movements.cantidad_entrada', '>', 'movements.cantidad_salida')
            ->get();

        return response(json_encode(array('existencias' => $existencias)), 200);
    }

    public function verEntradas(Request $request)
    {
        $datos = $request->all();
        $movements = Movement::select('movements.*', 'p.name as producto', 'u.name as user_alta')
            ->join('products as p', 'p.id', 'movements.product_id')
            ->join('users as u', 'u.id', 'movements.usu_alta_id')
            ->where('order_sales_line_id', $datos['order_sales_line_id'])
            ->get();
        return $movements;
    }

    public function verEntradasSalidasF()
    {
        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $productos = Product::get()->map(fn ($producto) => [
            'value' => $producto->id,
            'label' => $producto->name,
        ]);


        return Inertia::render('Movements/reportes/RptEntradasSalidasF', [
            'planteles' => $planteles,
            'productos' => $productos
        ]);
    }

    public function verEntradasSalidasR(Request $request)
    {
        $datos = $request->all();

        $resumen_ventas = Movement::select(
            'p.name as plantel',
            'pro.name as producto',
            'lcb.costo',
            'os.fecha_orden',
            'movements.cantidad_entrada',
            'movements.cantidad_salida',
            'lcb.quantity',
            'lcb.precio',
            'cb.fecha_caja',
            'lcb.bnd_entregado'
        )
            ->join('plantels as p', 'p.id', 'movements.plantel_id')
            ->join('products as pro', 'pro.id', 'movements.product_id')
            ->join('order_sales_lines as osl', 'osl.id', 'movements.order_line_id')
            ->join('order_sales as os', 'os.id', 'osl.order_sales_id')
            ->join('ln_cash_boxes as lcb', 'lcb.movement_id', 'movements.id')
            ->join('cash_boxes as cb', 'cb.id', 'lcb.cash_box_id')
            ->whereNull('movements.deleted_at')
            ->get();

        $resumen_devoluciones = Movement::select(
            'p.name as plantel',
            'pro.name as producto',
            'os.fecha_orden',
            'movements.cantidad_entrada',
            'movements.cantidad_salida',
            'odl.cantidad',
            'movements.precio',
            'od.fecha_devolucion'
        )
            ->join('plantels as p', 'p.id', 'movements.plantel_id')
            ->join('products as pro', 'pro.id', 'movements.product_id')
            ->join('order_sales_lines as osl', 'osl.id', 'movements.order_line_id')
            ->join('order_sales as os', 'os.id', 'osl.order_sales_id')
            ->join('order_devolution_lines as odl', 'odl.movement_id', 'movements.id')
            ->join('order_devolution as od', 'od.id', 'odl.order_devolution_id')
            ->whereNull('movements.deleted_at')
            ->get();
    }

    public function cortePlantel()
    {
        $planteles = $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

        return Inertia::render('Movements/reportes/RptCortePlantel', [
            'planteles' => $planteles
        ]);
    }

    public function cortePlantelR(Request $request)
    {
        $datos = $request->all();
        //dd($datos);
        //$fechaf=Carbon::createFromFormat('', $datos['fecha_f']);
        $plantel = Plantel::find($datos['plantel_id']);
        $lineas = Movement::select(
            'plantel_id',
            'p.name as product',
            'cantidad_entrada',
            'cantidad_salida',
            'movements.precio',
            'movements.costo',
            'movements.id as movement_id'
        )
            ->join('products as p', 'p.id', 'movements.product_id')
            ->where('plantel_id', $datos['plantel_id'])
            ->whereDate('movements.created_at', '>=', $datos['fecha_f'])
            ->whereDate('movements.created_at', '<=', $datos['fecha_t'])
            ->orderBy('movements.product_id')
            ->get();

        $grouped = $lineas->groupBy('product');
        //dd($grouped->toArray());
        $resumen = array();
        $totales = array();
        $totales['cantidad'] = 0;
        $totales['vendidos'] = 0;
        $totales['existencia'] = 0;
        $totales['precio'] = 0;
        $totales['efectivo_caja'] = 0;
        $totales['vales'] = 0;

        foreach ($grouped as $llave => $group) {
            $linea = array();
            $linea['producto'] = $llave;
            $linea['cantidad'] = 0;
            $linea['vendidos'] = 0;
            $linea['existencia'] = 0;
            $linea['precio'] = 0;
            $linea['efectivo_caja'] = 0;
            foreach ($group as $line) {
                $linea['movement_id'] = $line->movement_id;
                $linea['cantidad'] = $linea['cantidad'] + $line->cantidad_entrada;
                $vendidos = LnCashBox::where('movement_id', $line->movement_id)
                    //->wereNull('deleted_at')
                    ->count();
                $linea['vendidos'] = $vendidos;
                $ventas = LnCashBox::where('movement_id', $line->movement_id)
                    //->wereNull('deleted_at')
                    ->get();
                $pagos_vale = 0;
                foreach ($ventas as $venta) {
                    $pagos_vale = $pagos_vale + Payment::where('cash_box_id', $venta->cash_box_id)
                        ->where('porcentaje_descuento', '>', 0)
                        //->wereNull('deleted_at')
                        ->count();
                }
                $linea['vales'] = $pagos_vale;
                $linea['existencia'] = $linea['cantidad'] - $linea['vendidos'];
                $linea['precio'] = $line->precio;
                $linea['efectivo_caja'] = $line->precio * $linea['vendidos'];
            }
            $totales['cantidad'] = $totales['cantidad'] + $linea['cantidad'];
            $totales['vendidos'] = $totales['vendidos'] + $linea['vendidos'];
            $totales['existencia'] = $totales['existencia'] + $linea['existencia'];
            $totales['precio'] = $totales['precio'] + $linea['precio'];
            $totales['efectivo_caja'] = $totales['efectivo_caja'] + $linea['efectivo_caja'];
            $totales['vales']=$totales['vales']+$linea['vales'];
            array_push($resumen, $linea);
            //dd($linea);
        }
        $datos['fecha_f'] = new DateTime($datos['fecha_f']);
        $datos['fecha_t'] = new DateTime($datos['fecha_t']);

        return Inertia::render('Movements/reportes/RptCortePlantelR', [
            'plantel' => $plantel,
            'fecha1' => $datos['fecha_f']->format('Y-m-d'),
            'fecha2' => $datos['fecha_t']->format('Y-m-d'),
            'resumen' => $resumen,
            'totales' => $totales
        ]);
    }

    public function corteGeneral()
    {
        $planteles = $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

        return Inertia::render('Movements/reportes/RptCorteGeneral', [
            'planteles' => $planteles
        ]);
    }

    public function corteGeneralR(Request $request)
    {
        $datos = $request->all();
        //dd($datos);
        //$fechaf=Carbon::createFromFormat('', $datos['fecha_f']);
        $planteles = Plantel::all();
        $detalle = array();
        $totales = array();
        $totales['dinero_cantidad_vendida'] = 0;
        $totales['dinero_existencia_por_devolver'] = 0;
        $totales['dinero_vendidos_costo'] = 0;
        $resumen = array();
        foreach ($planteles as $plantel) {
            $lineas = Movement::select(
                'plantel_id',
                'pla.name as plantel',
                'p.name as product',
                'cantidad_entrada',
                'cantidad_salida',
                'movements.precio',
                'movements.costo',
                'movements.id as movement_id'
            )
                ->join('products as p', 'p.id', 'movements.product_id')
                ->join('plantels as pla', 'pla.id', 'movements.plantel_id')
                ->where('plantel_id', $plantel->id)
                ->whereDate('movements.created_at', '>=', $datos['fecha_f'])
                ->whereDate('movements.created_at', '<=', $datos['fecha_t'])
                ->orderBy('movements.product_id')
                ->get();
            //dd($lineas);
            //$groupedPlantel = $lineas->groupBy('plantel_id');

            //foreach($groupedPlantel as $llave => $group){

            $lin_gral['dinero_cantidad_vendida'] = 0;
            $lin_gral['dinero_existencia_por_devolver'] = 0;
            $lin_gral['dinero_vendidos_costo'] = 0;

            $groupedProducto = $lineas->groupBy('product');
            //dd($groupedProducto->toArray());
            foreach ($groupedProducto as $llave => $group) {
                //dd($group);
                $linea = array();
                $linea['plantel'] = "";
                $linea['cantidad_pedida'] = 0;
                $linea['vendidos'] = 0;
                $linea['existencia_por_vender'] = 0;
                $linea['dinero_cantidad_vendida'] = 0;
                $linea['dinero_existencia_por_devolver'] = 0;
                $linea['dinero_vendidos_costo'] = 0;
                $linea['costo'] = 0;
                $linea['precio'] = 0;
                $linea['movement_id'] = 0;

                $linea['producto'] = $llave;
                //dd($group->toArray());
                foreach ($group as $line) {
                    $linea['plantel'] = $line->plantel;
                    //dd($line->toArray());
                    $linea['costo'] = $line->costo;
                    $linea['precio'] = $line->precio;
                    $linea['movement_id'] = $line->movement_id;
                    $linea['cantidad_pedida'] = $linea['cantidad_pedida'] + $line->cantidad_entrada;
                    $vendidos = LnCashBox::where('movement_id', $line->movement_id)
                        //->wereNull('deleted_at')
                        ->count();
                    $linea['vendidos'] = $vendidos;
                    $ventas = LnCashBox::where('movement_id', $line->movement_id)
                        //->wereNull('deleted_at')
                        ->get();
                    $linea['existencia_por_vender'] = $linea['cantidad_pedida'] - $linea['vendidos'];
                    $linea['dinero_cantidad_vendida'] = $line->precio * $linea['vendidos'];
                    $linea['dinero_existencia_por_devolver'] = $linea['existencia_por_vender'] * $line->costo;
                    $linea['dinero_vendidos_costo'] = $line->costo * $linea['vendidos'];

                    $lin_gral['plantel'] = $linea['plantel'];
                }
                $lin_gral['control'] = $linea['movement_id'];
                $lin_gral['dinero_cantidad_vendida'] = $lin_gral['dinero_cantidad_vendida'] + $linea['dinero_cantidad_vendida'];
                $lin_gral['dinero_existencia_por_devolver'] = $lin_gral['dinero_existencia_por_devolver'] + $linea['dinero_existencia_por_devolver'];
                $lin_gral['dinero_vendidos_costo'] = $lin_gral['dinero_vendidos_costo'] + $linea['dinero_vendidos_costo'];
                array_push($detalle, $linea);
            }
            //dd($detalle);
            if (
                $lin_gral['dinero_cantidad_vendida'] != 0 and
                $lin_gral['dinero_existencia_por_devolver'] != 0 and
                $lin_gral['dinero_vendidos_costo']
            ) {
                //dd($lin_gral);
                $totales['dinero_cantidad_vendida'] = $totales['dinero_cantidad_vendida'] + $lin_gral['dinero_cantidad_vendida'];
                $totales['dinero_existencia_por_devolver'] = $totales['dinero_existencia_por_devolver'] + $lin_gral['dinero_existencia_por_devolver'];
                $totales['dinero_vendidos_costo'] = $totales['dinero_vendidos_costo'] + $lin_gral['dinero_vendidos_costo'];
                array_push($resumen, $lin_gral);
            }

            //dd($totales);
            //}
            //dd($resumen);
        }
        //dd($totales);

        //dd($resumen);

        //dd($grouped->toArray());

        $datos['fecha_f'] = new DateTime($datos['fecha_f']);
        $datos['fecha_t'] = new DateTime($datos['fecha_t']);

        return Inertia::render('Movements/reportes/RptCorteGeneralR', [
            'fecha1' => $datos['fecha_f']->format('Y-m-d'),
            'fecha2' => $datos['fecha_t']->format('Y-m-d'),
            'resumen' => $resumen,
            'detalle' => $detalle,
            'totales' => $totales
        ]);
    }

    public function corteToeic()
    {
        $planteles = $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

        return Inertia::render('Movements/reportes/RptCorteToeic', [
            'planteles' => $planteles
        ]);
    }

    public function corteToeicR(Request $request)
    {
        $datos = $request->all();
        //dd($datos);
        //$fechaf=Carbon::createFromFormat('', $datos['fecha_f']);
        $planteles = Plantel::all();
        $resumen = array();
        foreach ($planteles as $plantel) {
            $lineas = CashBox::select('pla.name as plantel', 'cash_box_id')
                ->join('ln_cash_boxes as lcb', 'lcb.cash_box_id', 'cash_boxes.id')
                ->join('products as p', 'p.id', 'lcb.product_id')
                ->join('plantels as pla', 'pla.id', 'cash_boxes.plantel_id')
                ->whereDate('cash_boxes.created_at', '>=', $datos['fecha_f'])
                ->whereDate('cash_boxes.created_at', '<=', $datos['fecha_t'])
                ->where('plantel_id', $plantel->id)
                ->where('p.id', 19)
                ->whereNull('lcb.deleted_at')
                ->orderBy('plantel_id')
                ->orderBy('lcb.product_id')
                ->get();
            if ($lineas->count() > 0) {
                $pagos = array();
                $pagos['plantel'] = "";
                $pagos['cash_box_id'] = "";
                $pagos['cuenta_pagos1'] = 0;
                $pagos['suma_pagos2'] = 0;
                $pagos['cuenta_pagos2'] = 0;
                $pagos['suma_pagos1'] = 0;
                foreach ($lineas as $caja) {
                    $pagos['plantel'] = $caja->plantel;
                    $pagos['cash_box_id'] = $caja->cash_box_id;
                    $count_pagos = Payment::where('cash_box_id', $caja->cash_box_id)
                        ->count();
                    //dd($count_pagos);
                    if ($count_pagos == 1) {
                        $pagos['cuenta_pagos1'] = $pagos['cuenta_pagos1'] + 1;
                        $p = Payment::where('cash_box_id', $caja->cash_box_id)->orderBy('id', 'asc')->first();
                        $pagos['suma_pagos1'] = $pagos['suma_pagos1'] + $p->monto;
                    } elseif (count($count_pagos) == 2) {
                        $pagos['cuenta_pagos1'] = $pagos['cuenta_pagos1'] + 1;
                        $p = Payment::where('cash_box_id', $caja->cash_box_id)->orderBy('id', 'asc')->first();
                        $pagos['suma_pagos1'] = $pagos['suma_pagos1'] + $p->monto;

                        $pagos['cuenta_pagos2'] = $pagos['cuenta_pagos2'] + 1;
                        $p = Payment::where('cash_box_id', $caja->cash_box_id)->orderBy('id', 'desc')->first();
                        $pagos['suma_pagos2'] = $pagos['suma_pagos2'] + $p->monto;
                    }
                }
                array_push($resumen, $pagos);
            }
        }
        //dd($resumen);


        $datos['fecha_f'] = new DateTime($datos['fecha_f']);
        $datos['fecha_t'] = new DateTime($datos['fecha_t']);

        return Inertia::render('Movements/reportes/RptCorteToeicR', [
            'fecha1' => $datos['fecha_f']->format('Y-m-d'),
            'fecha2' => $datos['fecha_t']->format('Y-m-d'),
            'resumen' => $resumen
        ]);
    }
}
