<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laramin\Utility\Onumoti;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    public $redirectTo = 'admin';

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        $pageTitle = "Admin Login";
        return view('admin.auth.login', compact('pageTitle'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function username()
    {
        return 'username';
    }

    public function login(Request $request)
    {
        Log::info('Admin Login Attempt Started', [
            'username' => $request->input('username'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        try {
            $this->validateLogin($request);
            Log::info('Login validation passed');

            $request->session()->regenerateToken();
            Log::info('Session token regenerated');

            if(!verifyCaptcha()){
                Log::warning('Captcha verification failed', ['username' => $request->input('username')]);
                $notify[] = ['error','Invalid captcha provided'];
                return back()->withNotify($notify);
            }
            Log::info('Captcha verified successfully');

            try {
                Onumoti::getData();
                Log::info('Onumoti data retrieved successfully');
            } catch (\Exception $e) {
                Log::warning('Onumoti verification failed (continuing with login)', [
                    'error' => $e->getMessage(),
                    'purchasecode' => env('PURCHASECODE') ? 'SET' : 'NOT SET'
                ]);
                // Continue with login even if license check fails
            }

            // If the class is using the ThrottlesLogins trait, we can automatically throttle
            // the login attempts for this application. We'll key this by the username and
            // the IP address of the client making these requests into this application.
            if (method_exists($this, 'hasTooManyLoginAttempts') &&
                $this->hasTooManyLoginAttempts($request)) {
                Log::warning('Too many login attempts', ['username' => $request->input('username')]);
                $this->fireLockoutEvent($request);
                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                Log::info('Login attempt successful', ['username' => $request->input('username')]);
                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            Log::warning('Login attempt failed - invalid credentials', ['username' => $request->input('username')]);
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);
            
        } catch (\Exception $e) {
            Log::error('Admin Login Exception', [
                'username' => $request->input('username'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $notify[] = ['error', 'Something went wrong. Please try again. Error: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }
    }


    public function logout(Request $request)
    {
        $this->guard('admin')->logout();
        $request->session()->invalidate();
        return $this->loggedOut($request) ?: redirect($this->redirectTo);
    }
}
