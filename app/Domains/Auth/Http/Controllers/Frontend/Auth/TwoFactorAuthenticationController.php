<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use App\Domains\Auth\Services\UserService;
use App\Domains\Auth\Events\TwoFactor\TwoFactorDisabled;

/**
 * Class TwoFactorAuthenticationController.
 */
class TwoFactorAuthenticationController
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function create(Request $request)
    {
        $secret = $request->user()->createTwoFactorAuth();

        return view('frontend.user.account.tabs.two-factor-authentication.enable')
            ->withQrCode($secret->toQr())
            ->withSecret($secret->toString());
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function show(Request $request)
    {
        return view('frontend.user.account.tabs.two-factor-authentication.recovery')
            ->withRecoveryCodes($request->user()->getRecoveryCodes());
    }

    /**
     * @param  Request  $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $request->user()->generateRecoveryCodes();

        session()->flash('flash_warning', __('Any old backup codes have been invalidated.'));

        return redirect()->route('frontend.auth.account.2fa.show')->withFlashSuccess(__('Two Factor Recovery Codes Regenerated'));
    }

    /**
     * Disable two-factor authentication for the authenticated user.
     *
     * @param  Request  $request
     * @param  UserService  $userService
     * @return mixed
     */
    public function disable(Request $request, UserService $userService)
    {
        $validatedData = $request->validate([
            'code' => ['required', 'max:10', 'totp_code'],
        ]);

        $user = $request->user();

        // Check if the entered code matches the user's 2FA code
        if (!$user->laraguard()->verify($validatedData['code'])) {
            return redirect()->back()->withErrors(['code' => 'Invalid verification code.']);
        }

        // Disable two-factor authentication for the user
        $user->laraguard()->disable();

        // Dispatch an event indicating that two-factor authentication has been disabled
        event(new TwoFactorDisabled($user));

        // Redirect the user to a success page or any desired destination
        return redirect()->route('home')->with('success', 'Two-factor authentication has been disabled.');
    }
}
