* En Symfony, un événement (ou "event" en anglais) est un signal envoyé par une partie de votre application lorsqu'une action ou un changement se produit. Les événements peuvent être utilisés pour déclencher des actions ou des comportements spécifiques dans d'autres parties de votre application.

* Un événement Symfony peut être écouté (ou "listened to" en anglais) par des "event subscribers", qui sont des classes qui définissent des méthodes à appeler lorsqu'un événement est déclenché. Les event subscribers sont souvent utilisés pour encapsuler la logique liée à un événement spécifique et pour permettre une meilleure séparation des préoccupations dans votre code.

- Voici un exemple simple de classe d'event subscriber Symfony :

```sh
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MyEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        // Do something when a KernelEvents::RESPONSE event is triggered
    }
}
```

Dans cet exemple, la classe `MyEventSubscriber` implémente l'interface `EventSubscriberInterface` et définit une méthode `getSubscribedEvents()` qui retourne un tableau des événements auxquels cet event subscriber est abonné. Dans cet exemple, l'event subscriber est abonné à l'événement `KernelEvents::RESPONSE`, qui est déclenché lorsque Symfony envoie une réponse à une requête HTTP.

La classe `MyEventSubscriber` définit également une méthode `onKernelResponse()`, qui est appelée chaque fois que l'événement `KernelEvents::RESPONSE` est déclenché. Cette méthode prend un objet `ResponseEvent` en argument, qui contient des informations sur l'événement qui a déclenché l'appel de la méthode.

>En résumé, les event subscribers sont des classes qui écoutent des événements spécifiques et définissent des méthodes à appeler lorsqu'un événement est déclenché. Les event subscribers sont souvent utilisés pour encapsuler la logique liée à un événement spécifique et pour permettre une meilleure séparation des préoccupations dans votre code.

Ce dossier contient 3 subscribers dans le namespace namespace `App\EventSubscriber;`

* BackNavContentSubscriber
> Service de récupération des éléments du menu -> matériel type et catégorie
> src/EventSubscriber/BackNavContentSubscriber.php 
> Ce subscriber ajoute des données globales à twig 

* ProjectSuccessEmailSubscriber

> Service de gestion des logs des événements utilisateurs sur les projets et gestion des envois de mails

* TaskSuccessEmailSubscriber

> Service de gestion des logs des événements utilisateurs sur les tâches et gestion des envois de mails