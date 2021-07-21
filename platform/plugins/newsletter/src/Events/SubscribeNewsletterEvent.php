<?php

namespace Botble\Newsletter\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Botble\Newsletter\Models\Newsletter;

class SubscribeNewsletterEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Newsletter
     */
    public $newsLetter;

    /**
     * Create a new event instance.
     *
     * @param Newsletter $newsletter
     */
    public function __construct(Newsletter $newsletter)
    {
        $this->newsLetter = $newsletter;
    }
}
