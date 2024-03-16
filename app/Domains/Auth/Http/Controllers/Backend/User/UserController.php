<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Domains\Auth\Http\Requests\Backend\User\DeleteUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\EditUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\StoreUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\UpdateUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\ClearUserSessionRequest;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Services\RoleService;
use App\Domains\Auth\Services\PermissionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;
    protected $roleService;
    protected $permissionService;

    public function __construct(
        UserService $userService,
        RoleService $roleService,
        PermissionService $permissionService
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        return view('backend.auth.user.index');
    }

    public function create()
    {
        return view('backend.auth.user.create')
            ->withRoles($this->roleService->get())
            ->withCategories($this->permissionService->getCategorizedPermissions())
            ->withGeneral($this->permissionService->getUncategorizedPermissions());
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());
        return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully updated.'));
    }

    public function show(User $user)
    {
        return view('backend.auth.user.show')
            ->withUser($user);
    }

    public function edit(EditUserRequest $request, User $user)
    {
        return view('backend.auth.user.edit')
            ->withUser($user)
            ->withRoles($this->roleService->get())
            ->withCategories($this->permissionService->getCategorizedPermissions())
            ->withGeneral($this->permissionService->getUncategorizedPermissions())
            ->withUsedPermissions($user->permissions->modelKeys());
    }

    public function destroy(DeleteUserRequest $request, User $user)
    {
        $user->delete(); // Soft delete the user
        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully deleted.'));
    }

    public function deleted()
    {
        $deletedUsers = User::onlyTrashed()->get();
        return view('backend.auth.user.deleted')->withDeletedUsers($deletedUsers);
    }

    public function deactivate($id)
    {
        $user = User::findOrFail($id); // Find the user by ID
        $user->delete(); // Soft delete the user
        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully deactivated.'));
    }

    public function storeFromArray(StoreUserRequest $request)
    {
        $user = $this->userService->createFromArray($request->validated());
        return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully created.'));
    }

    public function createFromArray(array $data): User
    {
        // Validate the incoming data if necessary

        // Create the user with the provided data
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Hash the password before storing
            // Add any other necessary fields
        ]);
    }

    public function restore(Request $request, $id)
    {
        $user = User::onlyTrashed()->findOrFail($id); // Find the soft deleted user by ID
        $user->restore(); // Restore the user
        return redirect()->route('admin.auth.user.deactivated')->withFlashSuccess(__('The user was successfully restored.'));
    }

    public function clearSession(ClearUserSessionRequest $request)
    {
        // Clear the user's session logic here
        // For example, you can revoke all user tokens, logout the user from all devices, etc.

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('User session has been cleared successfully.'));
    }
}
