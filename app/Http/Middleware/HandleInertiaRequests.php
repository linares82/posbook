<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
    public $menuArmado=array();
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        if (Auth::check()) {
            $menu=$this->getMenu();
            //dd($menu);
            return array_merge(parent::share($request), [
                'auth'=>[
                    'user'=>['username'=>Auth::user()->name,
                    'id'=>Auth::user()->id,
                    ]
                ],
                'menudos'=>$menu
            ]);
        }else{
            return array_merge(parent::share($request));
        }

    }

    public function getMenu($padre = 1){
        //$m = $this->armaMenuPrincipal();
        //dd($m);
        if (!session()->has('menu')) {
            $m = $this->armaMenuPrincipal();
            session(['menu' => $m]);
        }
        return session('menu');
    }

    public function armaMenuPrincipal($padre = 1)
    {
        $construccionMenu=array();
        //$menu=$this->menuRepository->search(array('padre'=>$padre));
        //$usuario_actual=User::find(Auth::user()->id)->is('admin');
        $items = Menu::where('depende_de', $padre)
            ->where('activo', true)
            ->orderBy('orden', 'asc')->get();
        //Log::info($items);
        //dd($items->toArray());

        if (!empty($items)) {
            //dd($menu);
            foreach ($items as $item) {
                //$permiso=User::find(Auth::user()->id)->can($item->permiso);

                if ($item->permiso <> "home" and $item->permiso <> "logout") {
                    //dd($item->permiso);
                    $permiso = Auth::user()->hasPermissionTo($item->permiso);
                } else {
                    //dd($item->permiso);
                    $permiso = true;
                }
                $link = route($item->link);
                //dd($permiso);
                if ($permiso and $item->activo == 1) {
                    //dd($item->id);
                    $r = intval($this->tieneItems($item->id));
                    //dd(action($item->link));

                    if ($r == 1) {
                        array_push($construccionMenu, array('key'=>$item->id,
                        'value'=>$item->id,
                        'label'=>$item->item,
                        'title'=>$item->item,
                        'link'=>$link,
                        'imagen'=>$item->imagen,
                        'target'=>$item->target,
                        'children'=>$this->armaMenuPrincipal($item->id)));
                    } else {
                        array_push($construccionMenu, array('key'=>$item->id,
                        'value'=>$item->id,
                        'label'=>$item->item,
                        'title'=>$item->item,
                        'link'=>$link,
                        'target'=>$item->target,
                        'imagen'=>$item->imagen));
                    }
                    //Log::info($construccionMenu);
                }
            }
            return $construccionMenu;
        }


        //dd($this->menuArmado);

        //return $this->menuArmado;

    }

    public function tieneItems($padre)
    {
        $menu = Menu::where('depende_de', $padre)->where('activo', true)->count();

        //dd($padre);
        if ($menu == 0) {
            return -1;
        } else {
            return 1;
        }
    }

}
