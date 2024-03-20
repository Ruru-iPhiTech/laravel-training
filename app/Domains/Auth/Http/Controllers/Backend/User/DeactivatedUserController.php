<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use Illuminate\Http\Request;

/**
 * Class UserStatusController.
 */
class DeactivatedUserController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * DeactivatedUserController constructor.
     *
     * @param  UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $deactivatedUsers = User::where('active', 0)->get();
        return view('backend.auth.user.deactivated', compact('deactivatedUsers'));
    }

    /**
     * @param  User  $user
     * @param  Request  $request
     * @param  int  $status
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function update(User $user, Request $request, $status)
    {
        $this->userService->mark($user, (int) $status);

        return redirect()->route(
            (int) $status === 1 || !$request->user()->can('admin.access.user.reactivate') ?
            'admin.auth.user.index' :
            'admin.auth.user.deactivated'
        )->withFlashSuccess(__('The user was successfully updated.'));
    }


    /**
     * Deactivate the specified user.
     *
     * @param  User  $user
     * @return mixed
     */
    public function deactivate(User $user)
    {
        try {
            $this->userService->mark($user, 0);
            return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully deactivated.'));
        } catch (\Exception $e) {
            \Log::error('Error deactivating user: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('There was a problem deactivating this user. Please try again.'));
        }
    }

    /**
     * Reactivate the specified user.
     *
     * @param  User  $user
     * @return mixed
     */
    public function reactivate(User $user)
    {
        try {
            $this->userService->mark($user, 1);
            return redirect()->route('admin.auth.user.index')->withFlashSuccess(__('The user was successfully reactivated.'));
        } catch (\Exception $e) {
            \Log::error('Error reactivating user: ' . $e->getMessage());
            return redirect()->back()->withErrors(__('There was a problem reactivating this user. Please try again.'));
        }
    }
}
