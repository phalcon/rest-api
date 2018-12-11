<?php

namespace Gewaer\Models;

use Phalcon\Cashier\Subscription as PhalconSubscription;

class Subscription extends PhalconSubscription
{
    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
        $this->belongsTo('user_id', 'Gewaer\Models\Users', 'id', ['alias' => 'user']);
    }
}
