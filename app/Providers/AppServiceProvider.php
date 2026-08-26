<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Blade::directive('jumlah', function ($expression) {
            return "<?php echo rtrim(rtrim(number_format({$expression}, 1, ',', '.'), '0'), ','); ?>";
        });
    }
}
