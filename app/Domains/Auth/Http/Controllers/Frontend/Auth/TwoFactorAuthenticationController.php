<?php

namespace App\Domains\Auth\Http\Controllers\Frontend\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
        // Check if two-factor authentication is already enabled for the user
        if ($request->user()->hasTwoFactorAuthenticationEnabled() && !$request->session()->has('authenticated_once')) {
            return redirect()->route('frontend.auth.account.2fa.show');
        }

        $secret = $request->user()->createTwoFactorAuth();
        $request->session()->put('authenticated_once', true);

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
     /**
     * Validate the two-factor authentication code.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);
    
        // Check if the user has previously authenticated once
        if ($request->session()->has('authenticated_once')) {
            // Redirect the user to the dashboard if already authenticated once
            return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication enabled successfully.');
        }
    
        // Mark the user as authenticated once
        $request->session()->put('authenticated_once', true);
    
        // Redirect the user to the dashboard after successful validation
        return redirect()->route('admin.dashboard')->with('success', 'Two-factor authentication enabled successfully.');
    }
}
