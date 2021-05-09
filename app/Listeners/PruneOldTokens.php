<?php

namespace App\Listeners;

use Laravel\Passport\Token;

class PruneOldTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Token::where('id', '<>', $event->accessTokenId)
            ->where('revoked', true)->delete();
    }
}
