<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Domains\Auth\Http\Requests\Backend\User\DeleteUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\EditUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\StoreUserRequest;
use App\Domains\Auth\Http\Requests\Backend\User\UpdateUserRequest;
use App\Domains\Auth\Http\Controllers\Backend\User\DeactivatedUserController;
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
            $data = $request->validated();
            $userType = $data['type'] ?? User::TYPE_USER;

            if ($userType === User::TYPE_USER) {
                $user = $this->userService->createFromArray($data);
            } else {
                $user = $this->userService->store($data);
            }

            return redirect()->route('admin.auth.user.show', $user)->withFlashSuccess(__('The user was successfully created.'));
        } catch (\Exception $e) {
            \Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(__('There was a problem creating this user. Please try again.'));
        }
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

    public function deactivated(DeactivatedUserController $deactivatedUserController)
    {
        return $deactivatedUserController->index();
    }

    /**
     * Delete the specified user.
     *
     * @param DeleteUserRequest $request
     * @param User $user
     * @return mixed
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully deleted.'));
        } catch (\Exception $e) {
            \Log::error('Error soft deleting user: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('There was a problem soft deleting this user. Please try again.'));
        }
    }

    /**
     * Permanently delete the specified user.
     *
     * @param DeleteUserRequest $request
     * @param User $user
     * @return mixed
     */
    public function permanentlyDelete(DeleteUserRequest $request, User $user)
    {
        try {
            $this->userService->destroy($user);
            return redirect()->route('admin.auth.user.deleted')->withFlashSuccess(__('The user was permanently deleted.'));
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting user: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('There was a problem permanently deleting this user. Please try again.'));
        }
    }
}
