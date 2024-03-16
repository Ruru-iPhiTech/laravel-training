<?php

namespace App\Domains\Auth\Http\Controllers\Backend\User;

use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use Illuminate\Http\Request;

class DeactivatedUserController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $deactivatedUsers = User::onlyTrashed()->get();
        return view('backend.auth.user.deactivated')->withDeactivatedUsers($deactivatedUsers);
    }

    public function update(Request $request, $id, $status)
    {
        $user = User::withTrashed()->findOrFail($id); // Find the soft deleted user by ID
        $user->update(['deleted_at' => ($status == 1) ? null : now()]); // Activate or deactivate the user based on $status

        return redirect()->route(
            ($status == 1) ?
                'admin.auth.user.index' :
                'admin.auth.user.deactivated'
        )->withFlashSuccess(__('The user was successfully updated.'));
    }
}
