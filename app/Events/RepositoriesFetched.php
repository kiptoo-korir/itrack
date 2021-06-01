<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RepositoriesFetched implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    protected $repos;
    protected $user_id;

    /**
     * Create a new event instance.
     */
    public function __construct(array $repositories, int $id)
    {
        $this->repos = $repositories;
        $this->user_id = $id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array|\Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user_repos.'.$this->user_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => 'New repositories found.',
            'repos' => $this->repos,
        ];
    }
}
