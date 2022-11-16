<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return RoleCollection
     */
    public function index(Request $request)
    {
        $this->authorize('list', Role::class);

        $search = $request->get('search', '');
        $roles = Role::where('name', 'like', "%{$search}%")->paginate();

        return new RoleCollection($roles);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return RoleResource
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $validated = $this->validate($request, [
            'name' => 'required|unique:roles|max:32',
            'permissions' => 'array',
        ]);

        $role = Role::create($validated);

        $permissions = Permission::find($request->permissions);
        $role->syncPermissions($permissions);

        return new RoleResource($role);
    }

    /**
     * @param  \Spatie\Permission\Models\Role  $role
     * @return RoleResource
     */
    public function show(Role $role)
    {
        $this->authorize('view', Role::class);

        return new RoleResource($role);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return RoleResource
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);

        $validated = $this->validate($request, [
            'name' => 'required|max:32|unique:roles,name,'.$role->id,
            'permissions' => 'array',
        ]);

        $role->update($validated);

        $permissions = Permission::find($request->permissions);
        $role->syncPermissions($permissions);

        return new RoleResource($role);
    }

    /**
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);

        $role->delete();

        return response()->noContent();
    }
}
