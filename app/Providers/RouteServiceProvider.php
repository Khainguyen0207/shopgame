<?php

namespace App\Providers;

use App\Http\Middleware\VerifyWebMiddleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define the rate limiter and load the application's routing configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            $this->checkAccess();

            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->group(base_path('routes/member.php'));
        });
    }

    public function checkAccess(): void
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        $location = $this->getLocation($ip);

        if (!Cache::has('access')) {
            if ($userAgent && !Str::contains(Str::lower($userAgent), 'bot')) {
                if (!$location['error']) {
                    Cache::set('access', [
                        $ip => $location['location']
                    ]);
                } else {
                    Cache::set('access', [
                        $ip => $userAgent
                    ]);
                }
            }
        } else {
            if ($userAgent && !Str::contains(Str::lower($userAgent), 'bot')) {
                $cachedData = Cache::get('access');

                $newKey = $ip;

                if (! $location['error']) {
                    $newValue = $location['location'];

                    if (!isset($cachedData[$newKey])) {
                        $cachedData[$newKey] = $newValue;
                        Cache::set('access', $cachedData);
                    }
                } else {
                    $newValue = $userAgent;
                    if (!isset($cachedData[$newKey])) {
                        $cachedData[$newKey] = $newValue;
                        Cache::set('access', $cachedData);
                    }
                }
            }
        }
    }

    private function getLocation(string $ip): array
    {
        $response = Http::get('https://ipinfo.io/' . $ip . '?token=c3bb2726375941');

        if ($response->ok()) {
            $data = $response->json();

            $city = $data['city'] ?? null;
            $country = $data['country'] ?? null;
            $region = $data['region'] ?? null;
            $location = $city . ', ' . $region . ', ' . $country;

            return [
                'error' => false,
                'location' => $location,
            ];
        }

        return [
            'error' => true,
            'location' => null,
        ];
    }
}
