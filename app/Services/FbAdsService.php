<?php


namespace App\Services;

use Carbon\Carbon;
use FacebookAds\Api;
use FacebookAds\Object\Ad;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdCreative;
use FacebookAds\Object\Fields\AdCreativeFields;
use FacebookAds\Exception\Exception;
use FacebookAds\Object\Fields\AdFields;
use FacebookAds\Http\Exception\RequestException;


class FbAdsService extends FbService
{

    public function getKeywordsStats(): array
    {
        $params        = [AdFields::ID,];
        $ads           = $this->getAdsByAccountId($params);
        $keywordsStats = [];

        foreach ($ads as $ad) {
            array_push($keywordsStats, $this->fbGetRequest("/$ad->id/keywordstats"));
        }

        return $keywordsStats;
    }

    public function getAdsByAccountId($params)
    {
        return $this->account->getAds($params);
    }

}
