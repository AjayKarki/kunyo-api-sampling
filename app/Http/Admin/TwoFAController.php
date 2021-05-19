<?php

namespace App\Http\Controllers\Admin;

use Foundation\Models\User;
use Illuminate\Http\Request;
use Neputer\Providers\RouteServiceProvider;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class TwoFAController
{

    /**
     * @var GoogleAuthenticator
     */
    private $authenticator;

    public function __construct(GoogleAuthenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * 2FA Setup Page
     */
    public function setup()
    {
        $user = auth()->user();
        if ($user->two_fa_enabled){
            flash('error', '2 Factor Authentication is already enabled for your account');
            return redirect()->back();
        }

        if (request()->ajax()){
            $secret = $this->authenticator->generateSecret();
            $user->two_fa_secret = $secret;
            $user->two_fa_enabled = false;
            $user->save();

            $qr = GoogleQrUrl::generate($user->email, $secret, 'Kunyo.co');
            return response()->json([
                'qr' => $qr,
                'secret' => $secret
            ]);
        }

        return view('admin.2fa.setup');
    }

    /**
     * Show the input where user can ent
     * @param User $user
     */
    public function promptCode()
    {
        if (auth()->user()->two_fa_enabled)
            return view('admin.2fa.prompt');

        return redirect()->route('admin.2fa.setup');
    }

    /**
     * Verify the code entered by user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric|digits:6'
        ]);

        $user = auth()->user();
        $secret = $user->two_fa_secret;

        $g = new GoogleAuthenticator();

        if ($g->checkCode($secret, $request->get('code'))) {
            session()->put('2fa_verified', true);
            $user->two_fa_enabled = true;
            $user->save();

            return response()->json([
                'message' => 'Code verified',
                'redirect' => RouteServiceProvider::HOME
            ]);
        } else {
            return response()->json([
                'status' =>
                    ['message' => 'The entered code is not valid.']
            ], 400);
        }
    }

    /**
     * Reset 2FA Reset
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset()
    {
        $user = auth()->user();
        $user->two_fa_enabled = false;
        $user->save();
        return response()->json([
            'message' => '2FA Reset',
        ]);

    }


}
