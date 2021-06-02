<?php

namespace App\Providers;

use App\Models\Offer;
use App\Models\Order;
use App\Models\OrdersProduct;
use App\Models\Pp;
use App\Models\ServicedeskTask;
use App\Models\ServicedeskTaskComment;
use App\Models\Support;
use App\Observers\OfferObserver;
use App\Observers\OrderObserver;
use App\Observers\PPObserver;
use App\Observers\ProductObserver;
use App\Observers\SupportObserver;
use App\Observers\UserObserver;
use App\User;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Date as DateFacade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Jenssegers\Date\Date;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        if ($this->app->environment() == 'local') {
            $this->app->register(\Reliese\Coders\CodersServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DateFacade::useClass(Date::class);
        Date::setlocale(config('app.locale'));

        $this->bladeRole();
        $this->bladeMoney();
        $this->bladeNumber();
        $this->jobFailedNotification();

        User::observe(UserObserver::class);
        Offer::observe(OfferObserver::class);
        Pp::observe(PPObserver::class);

        Order::observe(OrderObserver::class);
        OrdersProduct::observe(ProductObserver::class);

        ServicedeskTask::observe(\App\Observers\ServiceDeskTask::class);
        ServicedeskTaskComment::observe(\App\Observers\ServiceDeskTaskComment::class);
        if ($this->app->environment() == 'local') {
            DB::listen(function ($query) {
                Log::debug($query->sql);
                Log::debug($query->bindings);
                Log::debug($query->time);
            });
        }
    }

    public function bladeRole()
    {
        Blade::if('role', function ($value) {
            $current_role = request()->user()->role;
            if (is_array($value)) {
                return in_array($current_role, $value);
            }
            return $current_role === $value;
        });
    }

    public function bladeMoney()
    {
        Blade::directive('money', function ($expression) {
            $values = explode(',', $expression);
            $value = $values[0];
            $decimals = $values[1] ?? 0;
            return "<?php echo number_format($value, $decimals, '.', '&nbsp;').'&nbsp;'.auth()->user()->pp->currency; ?>";
        });
    }

    public function bladeNumber()
    {
        Blade::directive('number', function ($expression) {
            $values = explode(',', $expression);
            $value = $values[0];
            $decimals = $values[1] ?? 0;
            return "<?php echo number_format($value, $decimals, '.', '&nbsp;'); ?>";
        });
    }

    public function jobFailedNotification()
    {
        Queue::failing(function (JobFailed $event) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($event->exception);
            }
            // Log::stack(['stack', 'telegram'])->critical('Ошибка при выполнении задания!', [
            //     'connectionName' => $event->connectionName,
            //     'job' => $event->job,
            //     'exception' => $event->exception,
            // ]);
        });
    }
}
