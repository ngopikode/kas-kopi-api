<?php

namespace App\Traits;

trait RepositoryResponser
{
    /**
     * @param bool $error
     * @param mixed $data
     * @param string $message
     * @return array
     */
    protected function result(bool $error = false, mixed $data = [], string $message = ""): array
    {
        return ['error' => $error, 'data' => $data, 'message' => $message];
    }
}
