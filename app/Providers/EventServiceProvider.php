<?php

namespace App\Providers;

use App\Models\LnCashBox;
use App\Models\Movement;
use App\Models\OrderDevolutionLine;
use App\Observers\LnCashBoxObserver;
use App\Observers\MovementObserver;
use App\Models\OrderDevolutionLineBox;
use App\Observers\OrderDevolutionLineObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        LnCashBox::observe(LnCashBoxObserver::class);
        Movement::observe(MovementObserver::class);
        OrderDevolutionLine::observe(OrderDevolutionLineObserver::class);
        
    }
}
