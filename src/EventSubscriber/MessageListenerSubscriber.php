<?php

namespace App\EventSubscriber;

use App\LesEvenements\NosPropresEvenements\Events\AdvertEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\LesEvenements\NosPropresEvenements\Events\MessagePostEvent;

class MessageListenerSubscriber implements EventSubscriberInterface
{
    public function onAdvertPostMessage(MessagePostEvent $event)
    {
        // ...
    }

    public function onAdvertUpdateMessage(MessagePostEvent $event)
    {
        // ...
    }
    ///
    /// La méthode de l'interface que l'on doit implémenter
    public static function getSubscribedEvents()
    {
        return [
            AdvertEvents::POST_MESSAGE => 'onAdvertPostMessage',
            AdvertEvents::POST_UPDATE => 'onAdvertUpdateMessage'
        ];
    }
}
