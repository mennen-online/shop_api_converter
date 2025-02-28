<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * @return PermissionCollection
     */
    public function index(Request $request)
    {
        $this->authorize('list', Permission::class);

        $search = $request->get('search', '');
        $permissions = Permission::where('name', 'like', "%{$search}%")->paginate();

        return new PermissionCollection($permissions);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return PermissionResource
     */
    public function store(Request $request)
    {
        $this->authorize('create', Permission::class);

        $validated = $this->validate($request, [
            'name' => 'required|max:64',
            'roles' => 'array',
        ]);

        $permission = Permission::create($validated);

        $roles = Role::find($request->roles);
        $permission->syncRoles($roles);

        return new PermissionResource($permission);
    }

    /**
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return PermissionResource
     */
    public function show(Permission $permission)
    {
        $this->authorize('view', Permission::class);

        return new PermissionResource($permission);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return PermissionResource
     */
    public function update(Request $request, Permission $permission)
    {
        $this->authorize('update', $permission);

        $validated = $this->validate($request, [
            'name' => 'required|max:40',
            'roles' => 'array',
        ]);

        $permission->update($validated);

        $roles = Role::find($request->roles);
        $permission->syncRoles($roles);

        return new PermissionResource($permission);
    }

    /**
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $this->authorize('delete', $permission);

        $permission->delete();

        return response()->noContent();
    }
}
