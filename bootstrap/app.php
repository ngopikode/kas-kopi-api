<?php

use App\Http\Middleware\CheckRole;
use App\Traits\ApiResponserTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionHandler
{
    use ApiResponserTrait;
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        $middleware->group('api', [
            CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ValidationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return (new ApiExceptionHandler())->failResponse(
                    errors: $exception->validator->errors(),
                    message: $exception->validator->errors()->first()
                );
            }
            return null;
        });

        // if sanctum unauthenticated
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return (new ApiExceptionHandler())->failResponse(
                    errors: 'Unauthenticated',
                    code: Response::HTTP_UNAUTHORIZED
                );
            }
            return null;
        });

        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            if ($request->is('api/*')) {
                return match ($exception->getStatusCode()) {
                    Response::HTTP_BAD_REQUEST => (new ApiExceptionHandler())->failResponse(code: Response::HTTP_BAD_REQUEST),
                    Response::HTTP_FORBIDDEN => (new ApiExceptionHandler())->failResponse(code: Response::HTTP_FORBIDDEN),
                    Response::HTTP_NOT_FOUND => (new ApiExceptionHandler())->failResponse(code: Response::HTTP_NOT_FOUND),
                    Response::HTTP_METHOD_NOT_ALLOWED => (new ApiExceptionHandler())->failResponse(code: Response::HTTP_METHOD_NOT_ALLOWED),
                    Response::HTTP_TOO_MANY_REQUESTS => (new ApiExceptionHandler())->failResponse(code: Response::HTTP_TOO_MANY_REQUESTS),
                    Response::HTTP_SERVICE_UNAVAILABLE => (new ApiExceptionHandler())->errorResponse(code: Response::HTTP_SERVICE_UNAVAILABLE),
                    default => (new ApiExceptionHandler())->errorResponse(errors: $exception)
                };
            }
            return null;
        });

    })->create();
