<?php


namespace App\Services;

use Facebook\Facebook;
use FacebookAds\Api;
use FacebookAds\Object\AdAccount;
use App\Models\FbAccount;
use FacebookAds\Exception\Exception;
use SammyK\LaravelFacebookSdk\LaravelFacebookSdk;
use function session;


class FbService extends HttpResponseService
{
    protected $api;
    protected $accessToken;
    protected $account;
    protected $accountId;
    protected $appSecret;
    protected $appId;
    protected $fb;

    public function __construct(LaravelFacebookSdk $fb)
    {
        parent::__construct('https://graph.facebook.com/');
        $this->appId = \Config::get('app.appID');
        $this->appSecret = \Config::get('app.appSecret');
        $this->accessToken = env('FB_ADS_MARKER_PERMISSIONS');
        $this->accountId = env('FB_ADS_ACCOUNT_ID');
        $this->initFbInstance();
        $this->account = new AdAccount($this->accountId);
        $this->fb = $fb;
        // $this->setDefaultToken();
    }

    public function getUserAccounts(string $user_id, string $fb_access_token)
    {
        return $this->graphGetRequest("/$user_id/adaccounts?fields=name", $fb_access_token);
    }

    public function getCampaigns()
    {
        $campaigns = $this->account->getCampaigns()->getResponse()->getContent();
        return $campaigns['data'];
    }

    public function getAdsets()
    {
        $adsets = $this->account->getAdSets()->getResponse()->getContent();

        return $adsets['data'];
    }

    public function getAdAccountInsights(string $account_id, string $access_token, $fields = [], $params = [])
    {
        $this->setAccessToken($access_token);
        $this->setAccountId($account_id);
        $this->initFbInstance();
        $this->setAccount();
        \Log::info('$this->appId'. $this->appId);
        $insights = $this->account->getInsights($fields, $params)->getResponse()->getContent();

        return $insights['data'];
    }

    public function getAdsByAccountId($params)
    {
        return $this->account->getAds($params);
    }

    public function setAccount()
    {
        $this->account = new AdAccount($this->accountId);
    }

    protected function fbGetRequest($url)
    {
        try {
            $response = $this->api->call("/$url", $this->accessToken);
        } catch (Exception $e) {
            echo 'Graph returned an error: '.$e->getMessage();
            exit;
        }

        return $graphNode = $response->getGraphNode();
    }

    private function setAccountId(string $accountId)
    {
        $this->accountId = $accountId;
    }

    public function setAccessToken(string $token)
    {
        $this->accessToken = $token;
    }

    protected function graphGetRequest(string $url, string $access_token)
    {
        try {
            $response = $this->fb->get($url, $access_token);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: '.$e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: '.$e->getMessage();
            exit;
        }

        return $response->getGraphEdge()->asArray();
    }

    private function initFbInstance()
    {
        Api::init($this->appId, $this->appSecret, $this->accessToken);
        $this->api = Api::instance();
    }

    public function getFbLongToken(string $token)
    {
        
        $uri = "/oauth/access_token?client_id=".$this->appId."&client_secret=".$this->appSecret."&grant_type=fb_exchange_token&fb_exchange_token=$token";
        $response = $this->getRequest($uri);

        return $response;
    }
}
