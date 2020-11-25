<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FbAccount;
use App\Services\FbService;

use Illuminate\Support\Facades\Log;


class FBRefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fb:refresh_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Users FB Refresh Token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $fbService;

    public function __construct(FbService $fbService)
    {
        parent::__construct();
        $this->fbService = $fbService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info('--------------------- Runing FB refresh token --------------------');
        $fbAccounts = FbAccount::all();
        foreach($fbAccounts as $fbAccount) {
            if ($fbAccount->fb_access_token && strtotime('-1 day', $fbAccount->fb_token_expiration_time) < time()) {
                $fbResponse = $this->fbService->getFbLongToken($fbAccount->fb_access_token);
                try {
                    $fbAccount->update([
                        'fb_access_token' => $fbResponse->access_token,
                        'fb_token_expiration_time' => $fbResponse->expires_in + time(),
                        'updated_at' => date('Y-m-d G:i:s')
                    ]);
                } catch (Exception $e) {

                }
                
            }
            
        }
    }
}
