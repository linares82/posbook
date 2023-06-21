<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Plantel;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Models\OrderDevolution;
use Illuminate\Support\Facades\DB;
use App\Models\OrderDevolutionLine;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderDevolutionsCreateRequest;
use App\Http\Requests\OrderDevolutionsUpdateRequest;

class OrderDevolutionsController extends Controller
{
    public function getPermissions(){
        $permissions['orderDevolutionsIndex']=Auth::user()->hasPermissionTo('orderDevolutions.index');
        $permissions['orderDevolutionsCreate']=Auth::user()->hasPermissionTo('orderDevolutions.create');
        $permissions['orderDevolutionsStore']=Auth::user()->hasPermissionTo('orderDevolutions.store');
        $permissions['orderDevolutionsEdit']=Auth::user()->hasPermissionTo('orderDevolutions.edit');
        $permissions['orderDevolutionsUpdate']=Auth::user()->hasPermissionTo('orderDevolutions.update');
        $permissions['orderDevolutionsShow']=Auth::user()->hasPermissionTo('orderDevolutions.show');
        $permissions['orderDevolutionsDestroy']=Auth::user()->hasPermissionTo('orderDevolutions.destroy');
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
        $orderDevolutions=OrderDevolution::query()
        ->when($request->input('name'), function($query, $item){
            $query->where('name','like','%'.$item.'%');
        })->orderBy('id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($orderDevolution)=>[
            'id'=>$orderDevolution->id,
            'name'=>$orderDevolution->name,
            'fecha'=>$orderDevolution->fecha,
            'motivo'=>$orderDevolution->motivo
        ]);
        //dd($orderDevolutions);

        return Inertia::render('OrderDevolutions/Index',[
            'orderDevolutions'=>$orderDevolutions,
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
    public function create($orderSale=0)
    {

            $orderSales=OrderSale::select('order_sales.id',DB::raw('concat(order_sales.id," - ",order_sales.fecha," - ",order_sales.name) as nombre'))
            ->join('order_sales_lines as osl','osl.order_sale_id','order_sales.id')
            ->join('movements as m','m.order_sales_line_id','osl.id')
            ->whereIn('order_sales.plantel_id', Auth::user()->plantels->pluck('id'))
            ->whereColumn('m.cantidad_salida','<','cantidad_entrada')
            ->distinct()
            //->pluck('nombre','order_sales.id')
            ->get()->map(fn ($registro) => [
                'value' => $registro->id,
                'label' => $registro->nombre,
            ]);
            //dd($orderSales->toArray());
            if($orderSale==0){
                foreach($orderSales as $order){
                    //dd($order);
                    $orderSale=$order;
                    break;
                }
                return Inertia::render('OrderDevolutions/Create',['orderSale'=>$orderSale, 'orderSales'=>$orderSales]);
            }else{
                $orderSaleSelected=OrderSale::select('order_sales.id',DB::raw('concat(order_sales.id," - ",order_sales.fecha," - ",order_sales.name) as nombre'))
                ->join('order_sales_lines as osl','osl.order_sale_id','order_sales.id')
                ->join('movements as m','m.order_sales_line_id','osl.id')
                ->where('order_sales.id', $orderSale)
                ->whereIn('order_sales.plantel_id', Auth::user()->plantels->pluck('id'))
                ->whereColumn('m.cantidad_salida','<','cantidad_entrada')
                ->distinct()
                //->pluck('nombre','order_sales.id')
                ->get()->map(fn ($registro) => [
                    'value' => $registro->id,
                    'label' => $registro->nombre,
                ]);
                return Inertia::render('OrderDevolutions/Create',['orderSale'=>$orderSaleSelected, 'orderSales'=>$orderSales]);
            }
            //dd($orderSaleSelected);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderDevolutionsCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $input=array('fecha'=>$datos['fecha'],
            'name'=>$datos['name'],
            'motivo'=>$datos['motivo'],
            //'order_sale_id'=>$datos['order_sale_id']['value']);
            'order_sale_id'=>$datos['order_sale_id']);
            $orderDevolution=OrderDevolution::create($input);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        $orderSale=$orderDevolution->orderSale;
        //dd($orderSale->name);
        $orderDevolutionLines=OrderDevolutionLine::where('order_devolution_id',$id)
        ->get()
        ->map(fn ($linea) => [
            'id' => $linea->id,
            'order_devolution_id' => $linea->order_devolution_id,
            'plantel_id' => $linea->plantel_id,
            'plantel'=> $linea->plantel->name,
            'product_id' => $linea->product_id,
            'product'=>$linea->product->name,
            'cantidad' => $linea->cantidad,
            'contacto' => $linea->contacto,
            'bnd_salida_registrada' => $linea->bnd_salida_registrada,
        ]);
        //dd($orderDevolutionLines);
        //$route_consultaExistencias=route('movements.consultaExistencias');
        $route_storeLines=route('orderDevolutionLines.storeLines');
        $route_destroy_ln=route('orderDevolutionLines.destroy');
        //dd($route_consultaExistencias);
        $route_consultaExistencias=route('movements.consultaExistencias', array('id'=>$orderSale->id));
        //dd($route_consultaExistencias);
        return Inertia::render('OrderDevolutions/Show',
        ['orderDevolution'=>$orderDevolution,
        'orderSale'=>$orderSale,
         'orderDevolutionLines'=>$orderDevolutionLines,
         'route_consultaExistencias'=>$route_consultaExistencias,
        'route_storeLines'=>$route_storeLines,
        'route_destroy_ln'=>$route_destroy_ln]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        $orderSale=$orderDevolution->orderSale;

        $orderSales=OrderSale::select('order_sales.id',DB::raw('concat(order_sales.id," - ",order_sales.fecha," - ",order_sales.name) as nombre'))
            ->join('order_sales_lines as osl','osl.order_sale_id','order_sales.id')
            ->join('movements as m','m.order_sales_line_id','osl.id')
            ->whereIn('order_sales.plantel_id', Auth::user()->plantels->pluck('id'))
            ->whereColumn('m.cantidad_salida','<','cantidad_entrada')
            ->distinct()
            //->pluck('nombre','order_sales.id')
            ->get()->map(fn ($registro) => [
                'value' => $registro->id,
                'label' => $registro->nombre,
            ]);

        return Inertia::render('OrderDevolutions/Edit', ['orderDevolution'=>$orderDevolution, 'orderSales'=>$orderSales, 'orderSale'=>$orderSale]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderDevolutionsUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $orderDevolution=OrderDevolution::findOrFail($id);
            $orderDevolution->name=$datos['name'];
            $orderDevolution->fecha=$datos['fecha'];
            $orderDevolution->motivo=$datos['motivo'];
            $orderDevolution->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orderDevolution=OrderDevolution::findOrFail($id);
        //dd($user);
        try{
            $orderDevolution->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderDevolutions.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function registrarDevolucion($id)
    {
        $orderDevolution=OrderDevolution::findOrfail($id);
        $orderDevolutionLines=OrderDevolutionLine::where('order_devolution_id',$id)
        ->whereIn('plantel_id', Auth::user()->plantels->pluck('id'))
        ->get()
        ->map(fn ($linea) => [
            'id' => $linea->id,
            'order_devolution_id' => $linea->order_devolution_id,
            'plantel_id' => $linea->plantel_id,
            'plantel'=> $linea->plantel->name,
            'product_id' => $linea->product_id,
            'product'=>$linea->product->name,
            'cantidad' => $linea->cantidad,
            'contacto' => $linea->contacto,
            'bnd_salida_registrada'=> $linea->bnd_salida_registrada
        ]);
        //dd($orderDevolutionLines);
        $route_consultaExistencias=route('movements.consultaExistencias');
        $route_storeLines=route('orderDevolutionLines.storeLines');
        $route_destroy_ln=route('orderDevolutionLines.destroy');
        //dd($route_consultaExistencias);

        return Inertia::render('OrderDevolutions/RegistrarDevolucion',
        ['orderDevolution'=>$orderDevolution,
         'orderDevolutionLines'=>$orderDevolutionLines,
         'route_consultaExistencias'=>$route_consultaExistencias,
        'route_storeLines'=>$route_storeLines,
        'route_destroy_ln'=>$route_destroy_ln]);
    }


}
