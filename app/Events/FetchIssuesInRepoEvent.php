<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FetchIssuesInRepoEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;
    protected $repoId;
    protected $issuesArray;

    /**
     * Create a new event instance.
     */
    public function __construct(int $repoId, array $issuesArray)
    {
        $this->repoId = $repoId;
        $this->issuesArray = $issuesArray;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array|\Illuminate\Broadcasting\Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('issues-in-repo.'.$this->repoId);
    }

    public function broadcastWith()
    {
        return [
            'issues' => $this->issuesArray,
        ];
    }
}
