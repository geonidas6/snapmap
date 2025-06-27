<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTracemapEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Les données du tracemap à diffuser
     *
     * @var array
     */
    public $tracemapData;

    /**
     * Créer une nouvelle instance d'événement.
     *
     * @param array $tracemapData
     * @return void
     */
    public function __construct(array $tracemapData)
    {
        $this->tracemapData = $tracemapData;
    }

    /**
     * Obtenir les canaux sur lesquels l'événement doit être diffusé.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('tracemap-updates');
    }

    /**
     * Le nom de l'événement à diffuser.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-tracemap';
    }
}