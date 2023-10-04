<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Account;
use App\Models\CashBox;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    public function created(Payment $payment)
    {

    }

    public function deleting(Payment $payment)
    {

    }
}
