<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;
use App\Domains\Auth\Http\Requests\Backend\User\DeleteUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\EditUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\StoreUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\UpdateUserRequest;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\PermissionService;
use App\Domains\Auth\Services\RoleService;
use App\Domains\Auth\Services\UserService;

/**
 * Class UserController.
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * UserController constructor.
     *
     * @param  UserService  $userService
     * @param  RoleService  $roleService
     * @param  PermissionService  $permissionService
     */
    public function __construct(
        UserService $userService,
        RoleService $roleService,
        PermissionService $permissionService
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('backend.auth.user.index');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('backend.auth.user.create')
            ->withRoles($this->roleService->get())
            ->withCategories($this->permissionService->getCategorizedPermissions())
            ->withGeneral($this->permissionService->getUncategorizedPermissions());
    }

    /**
     * @param  UpdateUserRequest  $request
     * @param  User  $user
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Throwable
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Update the user details with the validated data from the request
        $this->userService->update($user, $request->validated());

        // Redirect to the user show page with a success flash message
        return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully updated.'));
    }

    /**
     * @param  User  $user
     * @return \Illuminate\Contracts\View\View
     */
    public function show(User $user)
    {
        return view('backend.auth.user.show')
            ->withUser($user);
    }

    /**
     * @param  EditUserRequest  $request
     * @param  User  $user
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(EditUserRequest $request, User $user)
    {
        return view('backend.auth.user.edit')
            ->withUser($user)
            ->withRoles($this->roleService->get())
            ->withCategories($this->permissionService->getCategorizedPermissions())
            ->withGeneral($this->permissionService->getUncategorizedPermissions())
            ->withUsedPermissions($user->permissions->modelKeys());
    }

    /**
     * @param  DeleteUserRequest  $request
     * @param  User  $user
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        $this->userService->delete($user);

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully deleted.'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function deleted()
    {
        // Your logic for displaying deleted users
    }
}
