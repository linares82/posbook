<?php

namespace App\Providers;

use App\Models\CashBox;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Movement;
use App\Models\LnCashBox;
use App\Observers\CashBoxObserver;
use App\Observers\ExpenseObserver;
use App\Observers\PaymentObserver;
use App\Models\OrderDevolutionLine;
use App\Observers\MovementObserver;
use App\Observers\LnCashBoxObserver;
use Illuminate\Support\Facades\Event;
use App\Models\OrderDevolutionLineBox;
use Illuminate\Auth\Events\Registered;
use App\Observers\OrderDevolutionLineObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        CashBox::observe(CashBoxObserver::class);
        LnCashBox::observe(LnCashBoxObserver::class);
        Movement::observe(MovementObserver::class);
        OrderDevolutionLine::observe(OrderDevolutionLineObserver::class);
        Payment::observe(PaymentObserver::class);
        Expense::observe(ExpenseObserver::class);
    }
}
