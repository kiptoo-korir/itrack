<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FetchLanguagesInRepo implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    protected $repoId;
    protected $languagesArray;

    /**
     * Create a new event instance.
     *
     * @param mixed $languagesArray
     */
    public function __construct(int $repoId, $languagesArray)
    {
        $this->repoId = $repoId;
        $this->languagesArray = $languagesArray;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array|\Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('languages_in_repo.'.$this->repoId);
    }

    public function broadcastWith()
    {
        return [
            'languages' => $this->languagesArray,
        ];
    }
}
