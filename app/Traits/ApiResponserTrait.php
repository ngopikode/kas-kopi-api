<?php

namespace App\Traits;

use App\Models\ErrorLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

trait ApiResponserTrait
{
    /**
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @return JsonResponse
     */
    public static function successResponse(
        mixed  $data = [],
        string $message = 'Data fetched successfully',
        int    $code = ResponseAlias::HTTP_OK,
        array  $headers = []
    ): JsonResponse
    {
        // data has key headers
        if (
            isset($data['wrapper-v2']) && isset($data['headers']) && is_array($data['headers'])
        ) {
            $headers = array_merge($headers, $data['headers']);
            $data = $data['records'];
        }

        return response()->json([
            'success' => true, // todo, remove this after all mobile apps updated to v2 api
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ], $code)->withHeaders($headers);
    }

    /**
     * @param mixed $errors
     * @param int $code
     * @param string|null $message
     * @return JsonResponse
     */
    public static function failResponse(
        mixed   $errors = [],
        int     $code = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        ?string $message = null
    ): JsonResponse
    {
        return response()->json([
            'success' => false, // todo, remove this after all mobile apps updated to v2 api
            'status' => 'error',
            'message' => $message ?? self::httpCodeName($code),
            'errors' => $errors
        ], $code);
    }

    /**
     * @param mixed $errors
     * @param string $message
     * @param int $code
     * @param Request|null $request
     * @return JsonResponse
     */
    public static function errorResponse(
        mixed   $errors = [],
        string  $message = "Internal Server Error",
        int     $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        Request $request = null
    ): JsonResponse
    {
        $requestId = self::saveErrorLog(message: $message, request: $request, errors: $errors);

        if ($errors instanceof Exception) {
            $errorData = [
                'file' => $errors->getFile(),
                'line' => $errors->getLine(),
                'message' => $errors->getMessage()
            ];

        }
        return match (config('app.debug')) {
            true => response()->json([
                'status' => 'error',
                'message' => $message,
                'request_id' => $requestId,
                'errors' => $errorData ?? $errors
            ], $code),
            default => response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Internal Server Error',
                'request_id' => $requestId
            ], $code)
        };
    }

    /**
     * @param int $code
     * @return string
     */
    public static function httpCodeName(int $code): string
    {
        $httpCode = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            102 => 'Processing',
            103 => 'Early Hints',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            207 => 'Multi-Status',
            208 => 'Already Reported',
            226 => 'IM Used',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => 'Switch Proxy',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Payload Too Large',
            414 => 'URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            421 => 'Misdirected Request',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => 'Too Early',
            426 => 'Upgrade Required',
            428 => 'Precondition Required',
            429 => 'Too Many Requests',
            431 => 'Request Header Fields Too Large',
            451 => 'Unavailable For Legal Reasons',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Under Maintenance',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            506 => 'Variant Also Negotiates',
            507 => 'Insufficient Storage',
            508 => 'Loop Detected',
            510 => 'Not Extended',
            511 => 'Network Authentication Required',
        ];

        return $httpCode[$code] ?? 'Unknown';
    }

    /**
     * @param string $message
     * @param Request|null $request
     * @param mixed $errors
     * @return string
     */
    public static function saveErrorLog(
        string  $message = "Internal Server Error",
        Request $request = null,
        mixed   $errors = []
    ): string
    {
        $requestId = 'KSKP-' . Carbon::now()->format('YmdHis') . '-' . Carbon::now()->timestamp;
        Log::error("Error on request $requestId: $message");

        if (!$request) $request = request();

        $userId = $request->user()?->id ?? "-";
        $auth = (bool)$request->user()?->id;
        $method = $request->method();
        $url = $request->fullUrl();
        $headers = $request->header();
        $parameters = $request->all();

        // if errors are Exception, convert it to string
        if ($errors instanceof Exception) {
            $file = $errors->getFile();
            $line = $errors->getLine();
            $message = $errors->getMessage();

            $errorTrace = $errors->getTrace();

            $auth = (bool)$request->user()?->id;
            $userId = $request->user()?->id ?? "-";
            $errData = "### Request Error" .
                "\n```Method: " . json_encode($request->method()) .
                "\nURL: " . $request->fullUrl() . "```" .
                "\nHeaders: \n```" . json_encode($request->header()) . "```" .
                "\nParameters: \n```" . json_encode($request->all()) . "```" .
                "\n### Auth" .
                "\n```User: $userId" .
                "\nIs Auth: " . json_encode($auth) . "```" .
                "\n### Error Description" .
                "\n```File: " . $errors->getFile() .
                "\nLine: " . $errors->getLine() . "```" .
                "\n### Message: \n```" . $errors->getMessage() . "```";

            // report error to discord
            dispatch(function () use ($errData) {
                $webhook = env('DISCORD_ERROR_LOG');
                if (!$webhook) return;
                $env = config('app.env');
                $error = [
                    "content" => "## Application Error Log ($env)",
                    "embeds" => [
                        [
                            "description" => $errData,
                            "color" => 0x87CEEB
                        ]
                    ]
                ];
                Http::post($webhook, $error)->json();
            });
        }

        // save to error log table
        try {
            ErrorLog::create([
                'request_id' => $requestId,
                'user_id' => $userId,
                'is_authenticated' => $auth,
                'url' => $url,
                'method' => $method,
                'headers' => json_encode($headers),
                'parameters' => json_encode($parameters),
                'file' => $file ?? null,
                'line' => $line ?? null,
                'message' => $message,
                'trace' => json_encode($errorTrace ?? $errors)
            ]);
        } catch (Exception $e) {
            Log::error("Error on saving error log: {$e->getMessage()}");
        }

        return $requestId;
    }
}
