<?php

namespace Trax\XapiStore\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Trax\Core\ExceptionHandler;
use Trax\XapiValidation\Exceptions\XapiValidationException;
use Trax\XapiStore\Stores\Logs\Logger;
use Trax\Core\Contracts\HttpException;

class XapiExceptionHandler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        XapiBadRequestException::class,
        XapiAuthorizationException::class,
        XapiNotFoundException::class,
        XapiConflictException::class,
        XapiPreconditionFailedException::class,
        XapiNoContentException::class,
        XapiValidationException::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // xAPI exceptions.
        if ($exception instanceof XapiBadRequestException
            || $exception instanceof XapiAuthorizationException
            || $exception instanceof XapiNotFoundException
            || $exception instanceof XapiConflictException
            || $exception instanceof XapiPreconditionFailedException
            || $exception instanceof XapiNoContentException
            || $exception instanceof XapiValidationException
        ) {

            $this->logXapiError($request, $exception);

            return response(
                $exception->getMessage(),
                $exception->status(),
                $exception->headers()
            );
        }

        // Not in the context of an xAPI request.
        if (!$request->hasHeader('X-Experience-API-Version')) {
            return parent::render($request, $exception);
        }

        // Other exceptions.
        if ($exception instanceof AuthenticationException) {
            return response($exception->getMessage(), 401);
        }
        if ($exception instanceof AuthorizationException) {
            return response($exception->getMessage(), 403);
        }
        return response($exception->getMessage(), 400);
    }

    /**
     * Log an xAPI error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Core\Contracts\HttpException  $exception
     * @return void
     */
    protected function logXapiError(\Illuminate\Http\Request $request, HttpException $exception): void
    {
        $api = 'unknown';
        if ($request->is('*/statements')) {
            $api = 'statement';
        }
        if ($request->is('*/activities')) {
            $api = 'activity';
        }
        if ($request->is('*/agents')) {
            $api = 'agent';
        }
        if ($request->is('*/activities/state')) {
            $api = 'state';
        }
        if ($request->is('*/activities/profile')) {
            $api = 'activity_profile';
        }
        if ($request->is('*/agents/profile')) {
            $api = 'agent_profile';
        }
        
        $method = strtoupper($request->method());
        $status = $exception->status();
        $data = null;

        // When the exception returns an error.
        // XapiNoContentException does not!
        if ($status != 200 && $status != 204) {

            // Request.
            $headers = array_map(function ($header) {
                return implode(',', $header);
            }, $request->headers->all());

            $data = [
                'request' => [
                    'headers' => $headers,
                ],
                'response' => [
                    'status' => $status,
                    'message' => $exception->getMessage(),
                ],
                'details' => [
                    'exception' => get_class($exception),
                ]
            ];

            if (!empty($request->query())) {
                $data['request']['params'] = $request->query();
            }

            if (!empty($exception->errors())) {
                $data['details']['errors'] = $exception->errors();
            }
        }

        // xAPI exceptions.
        if ($exception instanceof XapiValidationException) {
            $data['details']['data'] = $exception->data();
        }

        // Logging.
        Logger::log($api, $method, null, $data);
    }
}