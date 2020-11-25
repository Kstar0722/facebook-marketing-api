<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Dashboard\BaseDashboardController;
use App\Http\Requests\ResetPasswordRequest;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Exception;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends BaseDashboardController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */

    /**
     * @SWG\Post(
     *   path="/password/create",
     *   summary="Send Reset Password link to email",
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description="User Email",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="error", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="Not Found",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function create(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            //return $this->sendResponse('We have e-mailed your password reset link!', $user);
            if (!$user) {
                return response()->json(['error' => 'We can`t find a user with that e-mail address.'], 400);
            }

            $passwordReset = PasswordReset::updateOrCreate(
                ['email' => $user->email],
                [
                    'email' => $user->email,
                    'token' => \Str::random(60)
                ]
            );
            //return $this->sendResponse('We have e-mailed your password reset link!', $passwordReset);
            if ($user && $passwordReset) {
                $user->notify(new PasswordResetRequest($passwordReset->token));
            }
            return $this->sendResponse('We have e-mailed your password reset link!');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param  ResetPasswordRequest  $request
     * @return JsonResponse
     */

    /**
     * @SWG\Post(
     *   path="/password/reset",
     *   summary="Reset Password using reset link",
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description="User Email",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="User Password",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="token",
     *     in="query",
     *     description="Reset Password Token",
     *     required=true,
     *     type="string"
     *   ),
     * 
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="error", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=404,
     *      description="Not Found",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    
    public function reset(ResetPasswordRequest $request)
    {
        try {
            $passwordReset = PasswordReset::where([
                ['token', $request->token],
                ['email', $request->email]
            ])->first();

            if (!$passwordReset) {
                return response()->json(['error' => 'This password reset token is invalid.'], 400);
            }

            $user = User::where('email', $passwordReset->email)->first();
            if (!$user) {
                return response()->json(['error' => 'We can`t find a user with that e-mail address.'], 400);
            }

            $user->password = $request->password;
            $user->save();
            $passwordReset->delete();
            $user->notify(new PasswordResetSuccess());

        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse('Password successfully changed.', $user);
    }

}
