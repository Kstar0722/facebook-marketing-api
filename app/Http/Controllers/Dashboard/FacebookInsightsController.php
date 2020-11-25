<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\UserAccountsRequest;
use App\Models\AdAccount;
use App\Models\FbAccount;
use App\Models\Product;
use Illuminate\Http\Request;
use function response;

/**
 * @SWG\Definition(
 *     definition="Insights",
 *     type="object",
 *     description="Response for Insights",
 *     properties={
 *        @SWG\Property(property="ad_account_id", type="string", description="FB Ad Account ID"),
 *        @SWG\Property(property="ad_account_name", type="string", description="FB Ad Account Name"),
 *        @SWG\Property(property="data", type="array", @SWG\items(
 *             type="object",
 *             @SWG\Property(property="actions", type="object"),
 *             @SWG\Property(property="clicks", type="string"),
 *             @SWG\Property(property="cost_per_action_type", type="object"),
 *             @SWG\Property(property="cpc", type="string"),
 *             @SWG\Property(property="cpm", type="string"),
 *             @SWG\Property(property="cpp", type="string"),
 *             @SWG\Property(property="ctr", type="string"),
 *             @SWG\Property(property="date_start", type="string"),
 *             @SWG\Property(property="date_stop", type="string"),
 *             @SWG\Property(property="impressions", type="string"),
 *             @SWG\Property(property="spend", type="string"),
 *        )),
 *     }
 * )
 */

 /**
 * @SWG\Definition(
 *     definition="insights_params",
 *     type="object",
 *     description="Insights Params",
 *     properties={
 *          @SWG\Property(
 *              type="object",
 *              property="params",
 *              @SWG\Property(
 *                  property="date_preset",
 *                  type="string",
 *                  enum={"today", "yesterday", "this_month", "last_month", "this_quarter", "lifetime", "last_3d", "last_7d", "last_14d", "last_28d", "last_30d", "last_90d", "last_week_mon_sun", "last_week_sun_sat", "last_quarter", "last_year", "this_week_mon_today", "this_week_sun_today", "this_year"},
 *                  description="Default value: `last_30d`, Represents a relative time range. This field is ignored if `time_range` or `time_ranges` is specified.",
 *              ),
 *              @SWG\Property(
 *                  property="time_range",
 *                  type="object",
 *                  description="A single time range object. UNIX timestamp not supported. This param is ignored if `time_ranges` is provided.",
 *                  @SWG\Property(
 *                      property="since",
 *                      type="string",
 *                      description="A date in the format of `YYYY-MM-DD`, which means from the beginning midnight of that day."
 *                  ),
 *                  @SWG\Property(
 *                      property="until",
 *                      type="string",
 *                      description="A date in the format of `YYYY-MM-DD`, which means to the beginning midnight of the following day."
 *                  )
 *              ),
 *              @SWG\Property(
 *                  property="date_presets",
 *                  type="array",
 *                  description="Array of time range objects. Time ranges can overlap, for example to return cumulative insights. Each time range will have one result set. If time_ranges is specified, `date_preset` and `time_range` are ignored.",
 *                  @SWG\items(
 *                      type="object",
 *                      @SWG\Property(
 *                          property="since",
 *                          type="string",
 *                          description="A date in the format of `YYYY-MM-DD`, which means from the beginning midnight of that day."
 *                      ),
 *                      @SWG\Property(
 *                          property="until",
 *                          type="string",
 *                          description="A date in the format of `YYYY-MM-DD`, which means to the beginning midnight of the following day."
 *                     )
 *                  )
 *              )
 *          ),
 *     }
 * )
 */

class FacebookInsightsController extends BaseDashboardController
{
    /**
     * @SWG\Post(
     *   path="/insights",
     *   summary="Get insights data. it will return `Insights` data about all `Ad Accounts`, if `ad_account_id` and `product_id` aren't provided.",
     *   tags={"Insights"},
     *   security = { { "Bearer": {} } },
     *   @SWG\Parameter(
     *     name="ad_account_id",
     *     in="query",
     *     description="If `ad_account_id` is specified, it will return `insights` data by `ad_account_id`.",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="product_id",
     *     in="query",
     *     description="If `product_id` is specified, it will return `insights` data by `product_id`. This param is ignored, if `ad_account_id` is provided.",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *      name="_",
     *      in="body",
     *      type="object",
     *      description="Parameters available on this endpoint",
     *      @SWG\Schema(
     *          ref="#/definitions/insights_params"
     *      )
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     schema=@SWG\Schema(ref="#/definitions/Insights")
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

    public function insights(Request $request)
    {
        $adAccountId = $request->ad_account_id ?? null;
        $productId = $request->product_id ?? null;
        $userId = Auth::user()->id;
        $params = $request->params ?? [];
        $fields = [
            'action_values',
            'actions',
            'ad_click_actions',
            'ad_id',
            'ad_impression_actions',
            'ad_name',
            'adset_id',
            'adset_name',
            'campaign_id',
            'campaign_name',
            'clicks',
            'cost_per_action_type',
            'cpc',
            'cpm',
            'cpp',
            'created_time',
            'ctr',
            'impressions',
            'spend',
            'date_start',
            'date_stop',
        ];

        if ($adAccountId) {
            try {
                $adAccount = AdAccount::where([['user_id', $userId], ['ad_account_id', $adAccountId]])->first();
                if (empty($adAccount)) {
                    return $this->sendError("Can't find Ad Account");
                }
                $accessToken = $adAccount->user->fbAccounts->where('fb_user_id', '=', $adAccount->fb_user_id)->first()->fb_access_token;
                if (!$accessToken) {
                    return $this->sendError("Can't find FB Access Token");
                }
                $insights = $this->fbService->getAdAccountInsights($adAccountId, $accessToken, $fields, $params);
                $result = [
                    'ad_account_id' => $adAccountId,
                    'ad_account_name' => $adAccount->ad_account_name,
                    'data' => $insights
                ];
                return $this->sendResponse('Success', $result);
            } catch (Exception $e) {
                return $this->sendError($e->getMessage());
            }
        }
        if ($productId) {
            try {
                $product = Product::find($productId);
                if (empty($product)) {
                    return $this->sendError("Can't find Product");
                }
                $result = array();
                foreach($product->adAccounts as $adAccount) {
                    $accessToken = $adAccount->user->fbAccounts->where('fb_user_id', '=', $adAccount->fb_user_id)->first()->fb_access_token;
                    $insights = $this->fbService->getAdAccountInsights($adAccount->ad_account_id, $accessToken, $fields, $params);
                    $data = [
                        'ad_account_id' => $adAccount->ad_account_id,
                        'ad_account_name' => $adAccount->ad_account_name,
                        'data' => $insights
                    ];
                    array_push($result, $data);
                }
                return $this->sendResponse('Success', $result);
            } catch (Exception $e) {
                return $this->sendError($e->getMessage());
            }
        }
        $result = array();
        foreach(Auth::user()->fbAccounts as $fbAccount) {
            $data = [
                'fb_user_id' => $fbAccount->fb_user_id,
                'ad_accounts' => array()
            ];
            $adAccounts = AdAccount::where([['user_id', $userId], ['fb_user_id', $fbAccount->fb_user_id]])->get();
            $accessToken = $fbAccount->fb_access_token;
            foreach($adAccounts as $adAccount) {
                $insights = $this->fbService->getAdAccountInsights($adAccount->ad_account_id, $accessToken, $fields, $params);
                $adData = [
                    'ad_account_id' => $adAccount->ad_account_id,
                    'ad_account_name' => $adAccount->ad_account_name,
                    'data' => $insights
                ];
                array_push($data['ad_accounts'], $adData);
            }
            array_push($result, $data);
        }
        return $this->sendResponse('Success', $result);
    }

}
