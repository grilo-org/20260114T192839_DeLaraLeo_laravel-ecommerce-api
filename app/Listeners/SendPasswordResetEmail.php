<?php

namespace App\Listeners;

use App\Domain\Events\PasswordResetRequested;
use App\Notifications\PasswordResetNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordResetEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(PasswordResetRequested $event): void
    {
        $event->user->notify(new PasswordResetNotification($event->token));
    }
}

