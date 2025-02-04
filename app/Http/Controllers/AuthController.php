<?php

namespace App\Http\Controllers;

use App\Constants\Errors\V1\ErrorCodes;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\VerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use App\Mail\VerifyEmailAddress;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param Request $request HTTP request containing user registration data.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                'regex:/^[a-zA-Z\s]+$/',
            ],
            'phone' => 'required|numeric|digits_between:10,15',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
            ],
            'g-recaptcha-response' => 'required|captcha'
        ], [
            'name.required' => ErrorCodes::E0R01 . ' The name field is required.',
            'name.min' => ErrorCodes::E0R02 . ' The name must be at least 3 characters.',
            'email.required' => ErrorCodes::E0R03 . ' The email field is required.',
            'email.email' => ErrorCodes::E0R04 . ' The email must be a valid email address.',
            'email.unique' => ErrorCodes::E0R05 . ' The email has already been taken.',
            'password.required' => ErrorCodes::E0R06 . ' The password field is required.',
            'password.min' => ErrorCodes::E0R07 . ' The password must be at least 5 characters.',
            'password.regex' => ErrorCodes::E0R08 . ' The password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'g-recaptcha-response.required' => ErrorCodes::E2001 . ' Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => ErrorCodes::E2002 . ' Captcha error! Try again later or contact site admin.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();
        $validatedData['name'] = htmlspecialchars(trim($validatedData['name']), ENT_QUOTES, 'UTF-8');
        $validatedData['email'] = filter_var(trim($validatedData['email']), FILTER_SANITIZE_EMAIL);

        $user = User::create([
            'name' => $validatedData['name'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        
        return redirect()->signedRoute('login')->with('success', ErrorCodes::S2001 . ' User registered successfully!');
    }

    /**
     * Log in the user.
     *
     * @param Request $request HTTP request containing user credentials.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
            'g-recaptcha-response' => 'required|captcha'
        ],[
            'g-recaptcha-response.required' => ErrorCodes::E2001 . ' Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => ErrorCodes::E2002 . ' Captcha error! Try again later or contact site admin.',
        ]);
    
        $email = filter_var(trim($loginData['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($loginData['password']);
    
        $user = User::where('email', $email)->first();
    
        if (!$user || !Hash::check($password, $user->password)) {
            return redirect()->route('login')->with([
                'error_code' => ErrorCodes::E1001,
                'message' => 'The provided credentials are incorrect.',
            ]);
        }
    
        $verificationCode = mt_rand(10000, 99999);
        $user->verification_code = $verificationCode;
        $user->save();
        
        Mail::to($user->email)->send(new VerifyEmail($user, $verificationCode));
        return redirect()->signedRoute('verifyCode')->with('success', ErrorCodes::S2002 . ' Please check your email for the verification code.');
    }

    /**
     * Log out the user.
     *
     * @param Request $request HTTP request containing logout action.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect()->signedRoute('login')->with('success', ErrorCodes::S2003 . ' You have been logged out successfully.');
    }
   
    /**
     * Verify the user's email using a verification code.
     *
     * @param Request $request HTTP request containing verification code.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyLoginCode(Request $request)
    {
        $verifyData = $request->validate([
            'verify' => 'required|string|min:5|max:5',
            'g-recaptcha-response' => 'required|captcha'
        ],[
            'g-recaptcha-response.required' => ErrorCodes::E2001 . ' Please verify that you are not a robot.',
            'g-recaptcha-response.captcha' => ErrorCodes::E2002 . ' Captcha error! Try again later or contact site admin.',
        ]);
    
        $confirmationCode = trim($verifyData['verify']);
        $user = User::where('verification_code', $confirmationCode)->first();
    
        if (!$user) {
            return redirect()->route('verifyCode')->withErrors([
                'message' => 'Invalid verification code.',
            ]);
        }
    
        $user->update(['verification_code' => null]);
    
        Auth::guard('web')->login($user);
        return redirect()->signedRoute('dashboard')->with('success', ErrorCodes::S2002 . ' Verification successful! You are now logged in.');
    }
}
