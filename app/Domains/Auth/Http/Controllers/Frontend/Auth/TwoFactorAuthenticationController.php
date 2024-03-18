<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @return mixed
     */
    public function destroy(Request $request)
    {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Invalidate the user's secret key
        $user->laraguard_secret = null;

        // Invalidate the user's recovery codes
        $user->laraguard_recovery = [];

        // Save the changes to the user
        $user->save();

        // Flash a success message
        session()->flash('flash_warning', __('Two-factor authentication has been disabled.'));

        // Redirect the user to the two-factor authentication settings page
        return redirect()->route('frontend.auth.account.2fa.show')->withFlashSuccess(__('Two-factor authentication disabled'));
    }
}
