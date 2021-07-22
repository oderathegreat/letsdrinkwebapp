<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/checkout/payment/notify',
        '/user/payment/notify',
        '/paytm-callback',
        '/the/genius/ocean/2441139',
        '/user/paytm/notify',
        '/razorpay-callback',
        '/user/razorpay/notify',
        '/6fc8fca0c81a9d449c4fb555201c0c0b/stk-push',
        '/status/stk-push',
        '/mobile/transaction-status-timeout',
        '/mobile/transaction-status-result',
    ];
}
