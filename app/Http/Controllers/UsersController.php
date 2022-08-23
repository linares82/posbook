<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Plantel;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UsersCreateRequest;
use App\Http\Requests\UsersUpdateRequest;

class UsersController extends Controller
{
    public function getPermissions(){
        //dd(Auth::user()->getRoleNames());
        $permissions['usersIndex']=Auth::user()->hasPermissionTo('users.index');
        $permissions['usersCreate']=Auth::user()->hasPermissionTo('users.create');
        $permissions['usersStore']=Auth::user()->hasPermissionTo('users.store');
        $permissions['usersEdit']=Auth::user()->hasPermissionTo('users.edit');
        $permissions['usersUpdate']=Auth::user()->hasPermissionTo('users.update');
        $permissions['usersShow']=Auth::user()->hasPermissionTo('users.show');
        $permissions['usersDestroy']=Auth::user()->hasPermissionTo('users.destroy');
        $permissions['usersAssignRolesToAUser']=Auth::user()->hasPermissionTo('users.assignRolesToAUser');
        //dd($permissions);
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
        $users=User::query()
        ->when($request->input('userName'), function($query, $userName){
            $query->where('name','like','%'.$userName.'%');
        })
        ->paginate(10)
        ->withQueryString()
        ->through(fn($user)=>[
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'roles'=>$user->roles->map(fn($role)=>[
                'id'=>$role->id,
                'name'=>$role->name
            ])
        ]);
        //dd($users);

        return Inertia::render('Users/Index',[
            'users'=>$users,
            'filters'=>$request->only(['userName']),
            'sysMessage'=>$sysMessage,
            'usuarios'=>$users,
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
        return Inertia::render('Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UsersCreateRequest $request)
    {
        $datos=$request->all();

        try{
            $user=User::create($datos);
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('users.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::findOrfail($id);

        $roles = Role::all()->map(fn ($role) => [
            'key' => $role->id,
            'title' => $role->name,
        ]);
        $selectedRoles = $user->roles->pluck('id');
        $selectedRoles->transform(function ($item, $key) {
            return strval($item);
        });

        $planteles=Plantel::all()->map(fn ($plantel) => [
            'key' => $plantel->id,
            'title' => $plantel->name,
        ]);

        $selectedPlanteles=array();
        if(!is_null($user->plantels)){
            $selectedPlanteles = $user->plantels->pluck('id');
            /*$selectedPlanteles->transform(function ($plantel, $key) {
                return strval($plantel);
            });*/
        }
        

        return Inertia::render('Users/Show', 
        ['user'=>$user,
        'roles'=>$roles,'selectedRoles'=>$selectedRoles, 
        'planteles'=>$planteles, 'selectedPlanteles'=>$selectedPlanteles]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user=User::findOrfail($id);
        return Inertia::render('Users/Edit', ['user'=>$user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsersUpdateRequest $request, $id)
    {
        $datos=$request->all();
        //dd($datos);
        try{
            $user=User::findOrFail($id);
            $user->name=$datos['name'];
            $user->email=$datos['email'];
            if(!is_null($datos['password'])){
                $user->password=$datos['password'];
            }
            $user->save();
        }catch(Exception $e){
            dd($e);
        }

        return redirect()->route('users.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::findOrFail($id);
        //dd($user);
        try{
            $user->delete();
        }catch(Exception $e){
            dd($e);
        }
		return redirect()->route('users.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function assignRolesToAUser(Request $request)
    {
        //dd($request->all());
        $datos = $request->all();
        foreach($datos['roles'] as $role){
            if($role==""){
                array_pop($datos['roles']);
            }
        }
        
        try {
            $user = User::findOrFail($datos['user']);
            
            //dd($role);
            $roles = Role::whereIn('id',$datos['roles'])->get();
            
            //dd($permissions);
            $user->syncRoles($roles);
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('users.show', $datos['user'])->with('sysMessage', 'Roles Actualizados.');
    }

    public function assignPlantelsToAUser(Request $request)
    {
        //dd($request->all());
        $datos = $request->all();
        foreach($datos['plantels'] as $plantel){
            if($plantel==""){
                array_pop($datos['plantels']);
            }
        }
        
        try {
            $user = User::findOrFail($datos['user']);
            
            //dd($user);
            $plantels = Plantel::whereIn('id',$datos['plantels'])->get();
            
            //dd($plantels->toArray());
            $user->plantels()->sync($plantels);
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('users.show', $datos['user'])->with('sysMessage', 'Planteles Actualizados.');
    }
}

