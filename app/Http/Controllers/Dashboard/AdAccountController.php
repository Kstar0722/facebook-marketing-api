<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AdAccount;
use App\Models\FbAccount;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class AdAccountController extends BaseDashboardController
{


     /**
     * @SWG\Definition(
     *     definition="AdAccount",
     *     type="object",
     *     description="Response for Ad Account",
     *     properties={
     *          @SWG\Property(property="id", type="integer", description="UUID"),
     *          @SWG\Property(property="user_id", type="integer", description="User UUID"),
     *          @SWG\Property(property="fb_user_id", type="string", description="FB User ID"),
     *          @SWG\Property(property="product_id", type="integer | null", description="Product UUID"),
     *          @SWG\Property(property="ad_account_id", type="string", description="FB Ad Account Name"),
     *          @SWG\Property(property="ad_account_name", type="string", description="FB Ad Account ID"),
     *          @SWG\Property(property="created_at", type="string", description="Created Date"),
     *          @SWG\Property(property="updated_at", type="string", description="Updated Date"),
     *     }
     * )
     */

    /**
     * @SWG\Get(
     *   path="/ad_account/list",
     *   summary="Get All Facebook Ad Accounts",
     *   tags={"Ad Account"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *             @SWG\Property(property="status", type="string"),
     *             @SWG\Property(property="data", type="array", @SWG\items(
     *                  type="object",
     *                  @SWG\Property(property="fb_account_id", type="string", description="Facebook account id"),
     *                  @SWG\Property(property="ad_accounts", type="array", description="Facebook ad accounts", @SWG\items(
     *                       ref="#/definitions/AdAccount"
     *                  )),
     *              )),
     *             @SWG\Property(property="message", type="string"),
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *   ),
     *   @SWG\Response(
     *      response=401,
     *      description="Unauthorized"
     *   ),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    public function accounts()
    {
        $user_id = Auth::user()->id;
        $data = FbAccount::where('fb_accounts.user_id', $user_id)
            ->leftJoin('ad_accounts', 'fb_accounts.fb_user_id', '=', 'ad_accounts.fb_user_id')
            ->where('ad_accounts.user_id', $user_id)->get();

        $result = array();
        foreach($data as $record) {
            $index = array_search($record->fb_user_id, array_column($result, 'fb_account_id'));
            $ad_account = array(
                'id' => $record->id,
                'user_id' => $record->user_id,
                'fb_user_id' => $record->fb_user_id,
                'product_id' => $record->product_id,
                'ad_account_id' => $record->ad_account_id,
                'ad_account_name' => $record->ad_account_name,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            );
            if ($index > -1 && $ad_account['ad_account_id']) {
                $ad_accounts = $result[$index]['ad_accounts'];
                array_push($ad_accounts, $ad_account);
                $result[$index]['ad_accounts'] = $ad_accounts;

            } else {
                $fb_account = array(
                    'fb_account_id' => $record->fb_user_id,
                    'ad_accounts' => array()
                );
                if ($ad_account['ad_account_id']) {
                    $fb_account['ad_accounts'] = array($ad_account);
                }

                array_push($result, $fb_account);
            }
        }
        return $this->sendResponse('Success', $result);   
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return JsonResponse
     */

     /**
     * @SWG\Put(
     *   path="/ad_account/{id}",
     *   summary="Add/Update/Remove Product to Ad Account",
     *   tags={"Ad Account"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="Ad Account ID",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="product_id",
     *     in="query",
     *     description="Product ID, The Product id will remove from ad account when it empty",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *          ref="#/definitions/AdAccount"   
     *      )
     *   ),
     *   @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     *      @SWG\Schema(
     *          @SWG\Property(property="status", type="string"),
     *          @SWG\Property(property="message", type="string"),
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
    public function update(Request $request, $id) {
        $user_id = Auth::user()->id;
        $adAccount = AdAccount::where([
            ['user_id', $user_id],
            ['ad_account_id', $id]
        ])->first();
        if (empty($adAccount)) {
            return $this->sendError("Can't find Ad Account");
        }
        $productId = $request->product_id ?? null;
        if ($productId) {
            $product = Product::find($productId);
            if (empty($product)) {
                return $this->sendError("Can't find Product");
            }   
        }
        try {
            $adAccount->update([
                'product_id' => $productId
            ]);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
        $adAccount = AdAccount::where([
            ['user_id', $user_id],
            ['ad_account_id', $id]
        ])->first();
        return $this->sendResponse('Success', $adAccount);
    }
}
