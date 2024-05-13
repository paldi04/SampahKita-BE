<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends ApiController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        $credentials = array(
            $field => $request->username,
            'password' => $request->password,
        );
        $token = auth()->attempt($credentials);
        
        if (!$token) {
            return $this->sendError('Unauthorized', [], 401);
        }

        return $this->sendResponse([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Check token expiration.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return $this->sendResponse(auth()->payload());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return $this->sendResponse([], 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = auth()->refresh();
        if(!$token){
            return $this->sendError('Unauthorized', [], 401);
        }
        return $this->sendResponse([
            'token' => $token,
            'type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Change password.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword($id, ChangePasswordRequest $request)
    {
        if ($id !== auth()->user()->id) {
            if (auth()->user()->user_role_id !== 1) {
                return $this->sendError('Unauthorized', [], 401);
            }
            $user = auth()->user();
        } else {
            $user = User::find($id);
            if (!$user) {
                return $this->sendError('User not found.', [], 404);
            }
        }

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return $this->sendError('Old password did not match!', [], 400);
        }

        User::where('id', auth()->user()->id)->update(['password' => Hash::make($request->new_password)]);

        return $this->sendResponse([], 'Password has been changed');
    }
    
}