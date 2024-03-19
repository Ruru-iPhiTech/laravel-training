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
use Illuminate\Support\Facades\Hash;

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('backend.auth.user.index');
    }

    /**
     * @return mixed
     */
    public function create()
    {
        return view('backend.auth.user.create')
            ->withRoles($this->roleService->get())
            ->withCategories($this->permissionService->getCategorizedPermissions())
            ->withGeneral($this->permissionService->getUncategorizedPermissions());
    }

    /**
     * @param  StoreUserRequest  $request
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createFromArray($request->validated());
            return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully created.'));
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('There was a problem creating this user. Please try again.'));
        }
    }

    public function createFromArray(array $data): User
    {
        return User::create([
            'type' => $data['type'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param  User  $user
     * @return mixed
     */
    public function show(User $user)
    {
        return view('backend.auth.user.show')
            ->withUser($user);
    }

    /**
     * @param  EditUserRequest  $request
     * @param  User  $user
     * @return mixed
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
     * @param  UpdateUserRequest  $request
     * @param  User  $user
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $updatedUser = $this->userService->update($user, $request->validated());
            return redirect()->route('admin.auth.user.show', $updatedUser)->withFlashSuccess(__('The user was successfully updated.'));
        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('There was a problem updating this user. Please try again.'));
        }
    }

    /**
     * @param  DeleteUserRequest  $request
     * Deactivate the specified user.
     *
     * @param  User  $user
     * @param  DeactivatedUserController  $deactivatedUserController
     * @return mixed
     */
    public function deactivate(User $user, DeactivatedUserController $deactivatedUserController)
    {
        try {
            return $deactivatedUserController->update(request(), $user, 0);
        } catch (\Exception $e) {
            \Log::error('Error deactivating user: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('There was a problem deactivating this user. Please try again.'));
        }
    }

    /**
     * Delete the specified user.
     *
     * @throws \App\Exceptions\GeneralException
     * @param DeleteUserRequest $request
     * @param User $user
     * @return mixed
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        abort_unless(config('boilerplate.access.user.permanently_delete'), 404);

        $this->userService->destroy($user);

        return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('The user was successfully deleted.'));
    }
}
