<?php


namespace App\Http\Controllers\Dashboard;


use App\Http\Controllers\Controller;
use App\Services\FbService;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @SWG\Swagger(
 *   basePath="/api",
 *   @SWG\Info(
 *     title="Facebook Ads Marketing API",
 *     version="1.0.0"
 *   ),
 *   produces={"application/json"},
 *   schemes= {"http", "https"},
 * )
 */

/**
 * @SWG\SecurityScheme(
 *   securityDefinition="Bearer",
 *   type="apiKey",
 *   in="header",
 *   name="Authorization"
 * )
 */

class BaseDashboardController extends Controller
{
    protected $fbService;

    public function __construct(FbService $fbService)
    {
        $this->fbService = $fbService;
    }

    /**
     * @param $message
     * @param $result
     * @param  int  $code
     * @return JsonResponse
     */

    public function sendResponse($message, $result = null, $code = 200)
    {
        $response = [
            'status' => 'success',
            'data' => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * @param $error
     * @param  array  $errorMessages
     * @param  int  $code
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => 'failed',
            'message' => $error,
        ];
        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    /**
     * @param Exception $e
     * @param  array  $messages
     */
    public function sendErrorException($e, $messages = [])
    {
        $this->sendError($e->getMessage(), $messages);
    }

}
