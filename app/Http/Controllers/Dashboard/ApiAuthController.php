<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use function auth;
use Exception;
use function request;
use function response;
use function session;

class ApiAuthController extends BaseDashboardController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     /**
     * @SWG\Post(
     *   path="/auth/register",
     *   summary="User Register",
     *   tags={"Auth"},
     *   @SWG\Parameter(
     *     name="name",
     *     in="query",
     *     description="User Name",
     *     required=true,
     *     type="string"
     *   ),
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
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(property="id", type="integer", description="UUID"),
     *                  @SWG\Property(property="name", type="string"),
     *                  @SWG\Property(property="email", type="string"),
     *                  @SWG\Property(property="updated_at", type="date"),
     *                  @SWG\Property(property="created_at", type="date"),
     *              ),
     *             @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=422,
     *      description="Unprocessable Entity",
     *      @SWG\Schema(
     *          @SWG\Property(property="message", type="string"),
     *          @SWG\Property(
     *              property="errors",
     *              type="object",
     *              @SWG\Property(property="email", type="array", @SWG\items(type="string")),
     *              @SWG\Property(property="name", type="array", @SWG\items(type="string")),
     *              @SWG\Property(property="password", type="array", @SWG\items(type="string")),
     *          ),
     *      )
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = $this->me();
        session(['user' => $user]);
        return $this->respondWithToken($token, $user->original);
    }

    /**
     * @SWG\Post(
     *   path="/auth/login",
     *   summary="User Login",
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
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="access_token", type="string"),
     *             @SWG\Property(property="id", type="integer", description="UUID"),
     *             @SWG\Property(property="name", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(property="token_type", type="string"),
     *             @SWG\Property(property="expires_in", type="integer"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     *      @SWG\Schema(
     *          @SWG\Property(property="error", type="string"),
     *      )
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function register(UserRegisterRequest $request)
    {
        try {
            $user = User::create(request(['name', 'email', 'password']));
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse('Register new User', $user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token = null, $user = null)
    {
        return response()->json([
            'access_token' => $token,
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * env('JWT_EXPIRES_IN'),
        ]);
    }
}
