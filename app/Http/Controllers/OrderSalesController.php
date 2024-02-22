<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Plantel;
use App\Models\Product;
use App\Models\OrderSale;
use Illuminate\Http\Request;
use App\Models\OrderSalesLine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\OrderSalesCreateRequest;
use App\Http\Requests\OrderSalesUpdateRequest;

class OrderSalesController extends Controller
{
    public function getPermissions(){
        $permissions['orderSalesIndex']=Auth::user()->hasPermissionTo('orderSales.index');
        $permissions['orderSalesCreate']=Auth::user()->hasPermissionTo('orderSales.create');
        $permissions['orderSalesStore']=Auth::user()->hasPermissionTo('orderSales.store');
        $permissions['orderSalesEdit']=Auth::user()->hasPermissionTo('orderSales.edit');
        $permissions['orderSalesUpdate']=Auth::user()->hasPermissionTo('orderSales.update');
        $permissions['orderSalesShow']=Auth::user()->hasPermissionTo('orderSales.show');
        $permissions['orderSalesDestroy']=Auth::user()->hasPermissionTo('orderSales.destroy');
        $permissions['orderDevolutionsCreate']=Auth::user()->hasPermissionTo('orderDevolutions.create');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //dd($request->session()->get('sysMessage'));
        //dd(Auth::user()->plantels->pluck('id'));
        $sysMessage=$request->session()->get('sysMessage');
        $filtros = $request->input('search');
        $m=OrderSale::query()
        ->select('order_sales.id','order_sales.fecha','order_sales.name', 'p.name as plantel')
        ->join('plantels as p','p.id','order_sales.plantel_id')
        ->whereIn('order_sales.plantel_id', Auth::user()->plantels->pluck('id'));
        if (isset($filtros['plantel_id'])) {
            $m->when($filtros['plantel_id'], function ($query, $plantel_id) {
                $query->where('order_sales.plantel_id', $plantel_id);
            });
        }
        if (isset($filtros['fecha'])) {
            $m->when($filtros['fecha'], function ($query, $fecha) {
                $query->where('order_sales.fecha', $fecha);
            });
        }
        $orderSales=$m->when($request->input('fecha'), function($query, $fecha){
            $query->whereDate('fecha',$fecha);
        })
        ->orderBy('order_sales.id','desc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($orderSale)=>[
            'id'=>$orderSale->id,
            'fecha'=>$orderSale->fecha,
            'name'=>$orderSale->name,
            'plantel'=>$orderSale->plantel
        ]);
        //dd($orderSales);

        $planteles = Plantel::get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        return Inertia::render('OrderSales/Index',[
            'orderSales'=>$orderSales,
            'filters'=>$request->only(['fecha', 'plantel_id']),
            'sysMessage'=>$sysMessage,
            'permissions'=>$this->getPermissions(),
            'planteles'=>$planteles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);

        $plantel = Auth::user()->plantels->first()->id;
        //dd($plantel);

        $planteles->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        $productos=Product::get()->map(fn ($product) => [
            'value' => $product->id,
            'label' => $product->name,
        ]);
        $productos->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        return Inertia::render('OrderSales/Create',['planteles'=>$planteles,
        'plantel'=>$plantel,
        'productos'=>$productos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderSalesCreateRequest $request)
    {

        $datos_order_sale=$request->only('fecha','name','plantel_id');
        $datos_lineas=$request->input('lineas');
        //dd($datos_lineas);
        try{
            $orderSale=OrderSale::create($datos_order_sale);
            foreach($datos_lineas as $linea){
                $plantel=Plantel::find($linea['plantel_id']);
                $linea['contacto']=$plantel->director;
                $linea['order_sale_id']=$orderSale->id;
                //dd($linea);
                $l=OrderSalesLine::create($linea);
            }
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderSales.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $orderSale=OrderSale::findOrfail($id);
        $lineas=OrderSalesLine::select('order_sales_lines.*','p.name as plantel','pro.name as product',
        DB::raw('(select sum(m.cantidad_entrada)
        from movements as m where m.order_sales_line_id=order_sales_lines.id and
        m.deleted_at is null) as total_entradas'))
        ->join('plantels as p','p.id','order_sales_lines.plantel_id')
        ->join('products as pro','pro.id','order_sales_lines.product_id')
        ->where('order_sale_id', $orderSale->id)
        ->whereIn('plantel_id', Auth::user()->plantels->pluck('id'))
        ->get();

        $route_verObservaciones=route('obsEntries.verObservaciones');
        $route_verEntradas=route('movementsPartials.verEntradas');

        //dd($lineas->toArray());

        return Inertia::render('OrderSales/Show',
        ['orderSale'=>$orderSale, 'lineas'=>$lineas, 'route_verObservaciones'=>$route_verObservaciones,
        'route_verEntradas'=>$route_verEntradas]);
    }

    public function print($id)
    {
        $orderSale=OrderSale::findOrfail($id);
        $lineas=OrderSalesLine::select('order_sales_lines.*','p.name as plantel','p.address','pro.name as product')
        ->join('plantels as p','p.id','order_sales_lines.plantel_id')
        ->join('products as pro','pro.id','order_sales_lines.product_id')
        ->where('order_sale_id', $orderSale->id)
        ->whereIn('plantel_id', Auth::user()->plantels->pluck('id'))
        ->orderBy('plantel_id')
        ->get();




        //dd($lineas);

        return Inertia::render('OrderSales/Print', ['orderSale'=>$orderSale, 'lineas'=>$lineas]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $orderSale=OrderSale::findOrfail($id);
        $planteles = Plantel::whereIn('id', Auth::user()->plantels->pluck('id'))->get()->map(fn ($plantel) => [
            'value' => $plantel->id,
            'label' => $plantel->name,
        ]);
        $planteles->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        $productos=Product::get()->map(fn ($product) => [
            'value' => $product->id,
            'label' => $product->name,
        ]);
        $productos->prepend(["value"=>null,'label'=>"Selecionar Opción"]);
        $lineas=OrderSalesLine::select('order_sales_lines.*','p.name as plantel','pro.name as product')
        ->where('order_sale_id',$orderSale->id)
        ->join('plantels as p','p.id','order_sales_lines.plantel_id')
        ->join('products as pro','pro.id','order_sales_lines.product_id')
        ->get();
        //dd($lineas);
    return Inertia::render('OrderSales/Edit', ['orderSale'=>$orderSale,'planteles'=>$planteles,'productos'=>$productos,'lineas'=>$lineas]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(OrderSalesUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $orderSale=OrderSale::findOrFail($id);
            $orderSale->fecha=$datos['fecha'];
            $orderSale->name=$datos['name'];
            $orderSale->plantel_id=$datos['plantel_id'];
            //dd($orderSale);
            $orderSale->save();

            if(isset($datos['lineas'])){
                foreach($datos['lineas'] as $linea){
                    $plantel=Plantel::find($linea['plantel_id']);
                    $linea['contacto']=$plantel->director;
                    $linea['order_sale_id']=$orderSale->id;
                    //dd($linea);
                    $l=OrderSalesLine::create($linea);
                }
            }

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('orderSales.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $orderSale=OrderSale::findOrFail($id);

        foreach($orderSale->orderSalesLines as $linea){
            foreach($linea->movements as $movement){
                if ($movement->cantidad_salida>0){
                    //dd('fil');
                    return redirect()->route('orderSales.index')->with('sysMessage', 'No es posible borrar el registro. Existen movimientos vinculados');
                }

            }

        }
        //dd($movimientos);
        try{
            foreach($orderSale->orderSalesLines as $linea){
                foreach($linea->movements as $movement){

                        $movement->delete();

                    $linea->delete();
                }
                $orderSale->delete();
            }

        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('orderSales.index')->with('sysMessage', 'Registros Borrado.');
    }

    public function actualizarLineasOrderSales($id){
        return $lineas=OrderSalesLine::select('order_sales_lines.*','p.name as plantel','pro.name as product',
        DB::raw('(select sum(m.cantidad_entrada)
        from movements_partials as m where m.order_sales_line_id=order_sales_lines.id and
        m.deleted_at is null) as total_entradas'))
        ->join('plantels as p','p.id','order_sales_lines.plantel_id')
        ->join('products as pro','pro.id','order_sales_lines.product_id')
        ->where('order_sale_id', $id)
        ->whereIn('plantel_id', Auth::user()->plantels->pluck('id'))
        ->get();

    }

}
