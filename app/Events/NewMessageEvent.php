<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Événement pour diffuser les nouveaux messages de chat en temps réel
 * Utilise Pusher pour envoyer les messages instantanément à tous les utilisateurs connectés
 */
class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Les données du message à diffuser
     *
     * @var array
     */
    public $messageData;

    /**
     * Créer une nouvelle instance d'événement pour un message de chat
     *
     * @param array $messageData Les données du message (nom, contenu, timestamp)
     * @return void
     */
    public function __construct(array $messageData)
    {
        $this->messageData = $messageData;
    }

    /**
     * Obtenir les canaux sur lesquels l'événement doit être diffusé
     * Utilise un canal public pour que tous les utilisateurs puissent recevoir les messages
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('chat-messages');
    }

    /**
     * Le nom de l'événement à diffuser via Pusher
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'new-message';
    }

    /**
     * Les données à envoyer avec l'événement
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => $this->messageData
        ];
    }
}