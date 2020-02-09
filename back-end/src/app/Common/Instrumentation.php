<?php

namespace Fittie\Common;

use Illuminate\Support\Facades\Log;

class Instrumentation
{
    public function eventFired(Event $event)
    {
        Log::debug('Event fired', [
            'event' => $event->getData(),
        ]);
    }
}
