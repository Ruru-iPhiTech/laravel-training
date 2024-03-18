<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

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
class UserController
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
    public function __construct(UserService $userService, RoleService $roleService, PermissionService $permissionService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

/**
 * Display the form for creating a new user.
 *
 * @return \Illuminate\View\View
 */
public function create()
{
    $roles = $this->roleService->get(); // Assuming get() method returns roles
    $general = $this->permissionService->getUncategorizedPermissions();
    $categories = $this->permissionService->getCategorizedPermissions();

    return view('backend.auth.user.create', compact('roles', 'general', 'categories'));
}


    /**
     * Store a newly created user in storage.
     *
     * @param  StoreUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated());

        return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully created.'));
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('backend.auth.user.index');
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

public function update(UpdateUserRequest $request, User $user)
{
    $this->userService->update($user, $request->validated());

    return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully updated.'));
}

public function show(User $user)
{
    return view('backend.auth.user.show')->withUser($user);
}

    // Other methods...

    /**
     * Delete a user.
     *
     * @param  DeleteUserRequest  $request
     * @param  User  $user
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        $this->userService->delete($user);

        return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('The user was successfully deleted.'));
    }

    /**
     * Show the list of deleted users.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function deleted()
    {
        $deletedUsers = User::onlyTrashed()->get();

        return view('backend.auth.user.deleted')->withDeletedUsers($deletedUsers);
    }
}
