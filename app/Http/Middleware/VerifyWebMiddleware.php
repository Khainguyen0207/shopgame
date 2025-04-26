<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Telegram\Bot\Api;

class VerifyWebMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if ($request->acceptsHtml()) {
                abort(404);
            }
        } catch (\Exception $e) {
            $telegram = new Api(config('services.telegram.bot_token'));
            $debugChatId = config('services.telegram.chat_id');

            $telegram->sendMessage([
                'chat_id' => $debugChatId,
                'text' => $e->getMessage(),
            ]);
        }

        return $next($request);
    }
}
