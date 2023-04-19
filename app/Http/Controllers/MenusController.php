<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Menu;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MenusCreateRequest;
use App\Http\Requests\MenusUpdateRequest;

class MenusController extends Controller
{
    public function getPermissions(){
        $permissions['menusIndex']=Auth::user()->hasPermissionTo('menus.index');
        $permissions['menusCreate']=Auth::user()->hasPermissionTo('menus.create');
        $permissions['menusStore']=Auth::user()->hasPermissionTo('menus.store');
        $permissions['menusEdit']=Auth::user()->hasPermissionTo('menus.edit');
        $permissions['menusUpdate']=Auth::user()->hasPermissionTo('menus.update');
        $permissions['menusShow']=Auth::user()->hasPermissionTo('menus.show');
        $permissions['menusDestroy']=Auth::user()->hasPermissionTo('menus.destroy');
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
        $menus=Menu::query()
        ->when($request->input('item'), function($query, $item){
            $query->where('item','like','%'.$item.'%');
        })->orderBy('orden','asc')->orderBy('depende_de', 'asc')
        ->paginate(100)
        ->withQueryString()
        ->through(fn($menu)=>[
            'id'=>$menu->id,
            'item'=>$menu->item,
            'orden'=>$menu->orden,
            'dependeDe'=>$menu->depende_de,
            'link'=>$menu->link,
            'permiso'=>$menu->permiso,
            'target'=>$menu->target,
            'imagen'=>$menu->imagen
        ]);
        //dd($users);

        return Inertia::render('Menus/Index',[
            'menus'=>$menus,
            'filters'=>$request->only(['item']),
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
        return Inertia::render('Menus/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenusCreateRequest $request)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $menu=Menu::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('menus.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu=Menu::findOrfail($id);

        return Inertia::render('Menus/Show', ['menu'=>$menu]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $menu=Menu::findOrfail($id);
        return Inertia::render('Menus/Edit', ['menu'=>$menu]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MenusUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $menu=Menu::findOrFail($id);
            $menu->item=$datos['item'];
            $menu->orden=$datos['orden'];
            $menu->depende_de=$datos['depende_de'];
            $menu->link=$datos['link'];
            $menu->permiso=$datos['permiso'];
            $menu->target=$datos['target'];
            $menu->imagen=$datos['imagen'];
            $menu->save();

        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('menus.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu=Menu::findOrFail($id);
        //dd($user);
        try{
            $menu->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('menus.index')->with('sysMessage', 'Registro Borrado.');
    }
}
