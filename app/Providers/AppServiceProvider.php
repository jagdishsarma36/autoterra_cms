<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use App\Services\RazorpayService;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RazorpayService::class, function ($app) {
            return new RazorpayService();
        });

        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    public function boot(): void
    {
        // Load helper file
        require_once app_path('Helpers/content.php');

        // Custom Blade directive: @pageContent('home', 'hero.heading')
        Blade::directive('pageContent', function (string $expression) {
            return "<?php echo pageContent({$expression}); ?>";
        });

        // Custom Blade directive: @pageContentJson('home', 'faq.items')
        Blade::directive('pageContentJson', function (string $expression) {
            return "<?php echo json_encode(pageContentJson({$expression})); ?>";
        });
    }
}
