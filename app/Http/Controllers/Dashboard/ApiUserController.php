<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Models\FbAccount;
use App\Models\AdAccount;
use App\Services\FbService;
use Exception;
use Illuminate\Http\Request;

class ApiUserController extends BaseDashboardController
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  FbService  $fbService
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @SWG\Put(
     *   path="/user/{userId}",
     *   summary="Update facebook token to long-lived token  after logged in to facebook, Fetch Ad accounts about by fb_user_id",
     *   tags={"User"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     description="User ID",
     *     required=true,
     *     type="integer"
     *   ),
     *   @SWG\Parameter(
     *     name="fb_token",
     *     in="query",
     *     description="Facebook token",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="fb_user_id",
     *     in="query",
     *     description="Facebook User ID",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="id", type="integer", description="UUID"),
     *             @SWG\Property(property="name", type="integer", description="UUID"),
     *             @SWG\Property(property="email", type="integer", description="UUID"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="error", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    
    public function update(Request $request, FbService $fbService, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response()->json(['error' => 'Not find user'], 400);
        }
        try {
            $fbResponse = $fbService->getFbLongToken($request->fb_token);
            $fbExpirationTime = time() + $fbResponse->expires_in;
            FbAccount::updateOrCreate(
                [
                    'user_id' => $id,
                    'fb_user_id' => $request->fb_user_id
                ],
                [   
                    'user_id' => $id,
                    'fb_user_id' => $request->fb_user_id,
                    'fb_access_token' => $fbResponse->access_token,
                    'fb_token_expiration_time' => $fbExpirationTime
                ]
            );
            $adAccounts = $fbService->getUserAccounts($request->fb_user_id, $fbResponse->access_token);

            foreach($adAccounts as $adAccount) {
                $account = AdAccount::where([
                    ['user_id', $id],
                    ['ad_account_id', $adAccount['id']]
                ])->first();
                if (empty($account)) {
                    AdAccount::create([
                        'user_id' => $id,
                        'fb_user_id' => $request->fb_user_id,
                        'ad_account_id' => $adAccount['id'],
                        'ad_account_name' => $adAccount['name']
                    ]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['id' => $user->id, 'name' => $user->name, 'email' => $user->email]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
