<?php

namespace App\Traits;

trait ServiceResponser
{
    use ApiResponserTrait;

    /**
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function finalResultSuccess(mixed $data = [], string $message = 'success'): array
    {
        return ['status' => true, 'data' => $data, 'message' => $message];
    }

    /**
     * @param mixed $dataFail
     * @param string $message
     * @return array
     */
    protected function finalResultFail(mixed $dataFail = [], string $message = ""): array
    {
        return ['status' => false, 'data' => $dataFail, 'message' => $message];
    }

    protected function finalResultError(mixed $dataError = [], string $message = ""): array
    {
        $requestId = self::saveErrorLog(message: $message, errors: $dataError);
        $dataError = [
            'request_id' => $requestId,
            'message' => $message,
        ];
        return ['status' => false, 'data' => $dataError, 'message' => $message];
    }
}
