<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\RolesCreateRequest;
use App\Http\Requests\RolesUpdateRequest;

class RolesController extends Controller
{
    public function getPermissions(){
        $permissions['rolesIndex']=Auth::user()->hasPermissionTo('roles.index');
        $permissions['rolesCreate']=Auth::user()->hasPermissionTo('roles.create');
        $permissions['rolesStore']=Auth::user()->hasPermissionTo('roles.store');
        $permissions['rolesEdit']=Auth::user()->hasPermissionTo('roles.edit');
        $permissions['rolesUpdate']=Auth::user()->hasPermissionTo('roles.update');
        $permissions['rolesShow']=Auth::user()->hasPermissionTo('roles.show');
        $permissions['rolesDestroy']=Auth::user()->hasPermissionTo('roles.destroy');
        $permissions['rolesAssignPermissionsToARole']=Auth::user()->hasPermissionTo('roles.assignPermissionsToARole');
        return $permissions;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        /*
        ->map(fn($user)=>[
                'id'=>$user->id,
                'name'=>$user->name,
                'mail'=>$user->email
            ])
        */
        $sysMessage = $request->session()->get('sysMessage');
        $roles = Role::query()
            ->when($request->input('roleName'), function ($query, $roleName) {
                $query->where('name', 'like', '%' . $roleName . '%');
            })
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name
            ]);

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'filters' => $request->only(['roleName']),
            'sysMessage' => $sysMessage,
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
        return Inertia::render('Roles/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RolesCreateRequest $request)
    {
        $datos = $request->all();

        try {
            $user = Role::create($datos);
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('roles.index')->with('sysMessage', 'Registro Creado.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrfail($id);
        $permissions = Permission::all()->map(fn ($permission) => [
            'key' => $permission->id,
            'title' => $permission->name,
        ]);
        $selectedPermissions = $role->permissions->pluck('id');
        /*$selectedPermissions->transform(function ($item, $key) {
            return strval($item);
        });*/
        //dd($selectedPermissions);
        $sysMessage = session()->get('sysMessage');
        //dd($sysMessage);

        return Inertia::render('Roles/Show', ['role' => $role, 
                                              'permissions' => $permissions,  
                                              'selectedPermissions' => $selectedPermissions,
                                              'sysMessage'=>$sysMessage]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrfail($id);
        return Inertia::render('Roles/Edit', ['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RolesUpdateRequest $request, $id)
    {
        $datos = $request->all();
        //dd($datos);
        try {
            $role = Role::findOrFail($id);
            $role->name = $datos['name'];

            $role->save();
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('roles.index')->with('sysMessage', 'Registro Actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        //dd($user);
        try {
            $role->delete();
        } catch (Exception $e) {
            dd($e);
        }
        return redirect()->route('roles.index')->with('sysMessage', 'Registro Borrado.');
    }

    public function assignPermissionsToARole(Request $request)
    {
        //dd($request->all());
        $datos = $request->all();
        foreach($datos['permissions'] as $permiso){
            if($permiso==""){
                array_pop($datos['permissions']);
            }
        }
        
        try {
            $role = Role::findOrFail($datos['role']);
            
            //dd($role);
            $permissions = Permission::whereIn('id',$datos['permissions'])->get();
            
            //dd($permissions);
            $role->syncPermissions($permissions);
        } catch (Exception $e) {
            dd($e);
        }

        return redirect()->route('roles.show', $datos['role'])->with('sysMessage', 'Permisos Actualizados.');
    }
}
