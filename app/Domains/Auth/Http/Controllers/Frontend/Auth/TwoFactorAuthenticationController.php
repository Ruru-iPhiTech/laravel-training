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
     * Create a new two-factor authentication secret for the authenticated user.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $secret = $request->user()->createTwoFactorAuth();

        return view('frontend.user.account.tabs.two-factor-authentication.enable')
            ->withQrCode($secret->toQr())
            ->withSecret($secret->toString());
            
    }

    /**
     * Show recovery codes for the authenticated user.
     *
     * @param  Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        return view('frontend.user.account.tabs.two-factor-authentication.recovery')
            ->withRecoveryCodes($request->user()->recoveryCodes());
    }

    /**
     * Generate new recovery codes for the authenticated user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->user()->generateRecoveryCodes();

        session()->flash('flash_warning', __('Any old backup codes have been invalidated.'));

        return redirect()->route('frontend.auth.account.2fa.show')->withFlashSuccess(__('Two Factor Recovery Codes Regenerated'));
    }
}
