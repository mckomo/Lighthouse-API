<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        'Symfony\Component\HttpKernel\Exception\HttpException',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->wantsJson()) {

            $code = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : 500;
            $message = [
                'error' => empty($e->getMessage())
                    ? $this->getClassName($e)
                    : $e->getMessage()
            ];

            return response()->json($message, $code);
        }

        return parent::render($request, $e);
    }

    private function getClassName($obj) {
        $parts = explode('\\', get_class($obj));

        return array_pop($parts);
    }
}
