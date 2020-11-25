<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Dashboard\BaseDashboardController;
use App\Services\FbAdsService;
use App\Services\FbService;

class FacebookAdsController extends BaseDashboardController
{

    public function __construct(FbAdsService $fbAdsService)
    {
        $this->fbService = $fbAdsService;
    }

    public function getAllKeywordsStats()
    {
        try {
            $keywordsStats = $this->fbService->getKeywordsStats();
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse('Ad keywords Stats', $keywordsStats);
    }


}
