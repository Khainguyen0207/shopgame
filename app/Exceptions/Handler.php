<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Exceptions\ReportableHandler;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        if (App::environment('production')) {
            $this->reportable(function (Throwable $e): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response {
                try {
                    $request = request();
                    $telegram = new Api(config('services.telegram.bot_token'));

                    $debugChatId = config('services.telegram.chat_id');

                    if (!$debugChatId) {
                        return parent::render($request, $e);
                    }

                    $data = [
                        'message' => $e->getMessage(),
                        'url' => $request->url(),
                        'method' => $request->method(),
                        'ip' => $request->ip(),
                        'formData' => json_encode($request->all()),
                        'filePath' => $e->getFile(),
                        'exceptionType' => $e::class,
                        'line' => $e->getLine(),
                    ];

                    $view = view('handler-logs-telegram', $data)->render();

                    $telegram->sendMessage([
                        'chat_id' => $debugChatId,
                        'text' => $view ?? 'Error',
                        'parse_mode' => 'HTML',
                    ]);
                } catch (\Exception $e) {
                    logger($e->getMessage(), $e->getTrace());
                }

                return parent::render($request, $e);
            });

        }
    }

    public function render($request, Throwable $e)
    {
        if (App::environment('production')
            && $e instanceof MethodNotAllowedHttpException
        ) {
            abort(404, 'Không tìm thấy tài nguyên.');
        }

        return parent::render($request, $e);
    }
}
