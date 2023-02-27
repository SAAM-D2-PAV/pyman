### Les événements (ou "events" en anglais) sont des signaux envoyés par différentes parties de l'application pour signaler qu'un changement ou une action s'est produite. Les événements peuvent être écoutés par d'autres parties de l'application, qui peuvent y réagir en effectuant des actions spécifiques.
<>
### Symfony est livré avec un système de gestion d'événements intégré, appelé "EventDispatcher". Ce système permet de définir des événements personnalisés, de les déclencher (ou "dispatch") dans votre code et de les écouter (ou "listen to") en y attachant des listeners (ou "écouteurs").
<>

### Symfony dispose également d'un certain nombre d'événements intégrés (ou "built-in events" en anglais), qui sont déclenchés automatiquement par différentes parties du framework. Voici quelques exemples d'événements intégrés de Symfony :

`kernel.request`: Déclenché lorsque Symfony reçoit une nouvelle requête HTTP.

`kernel.controller`: Déclenché juste avant que le contrôleur de Symfony ne soit appelé pour traiter une requête HTTP.

`kernel.view`: Déclenché après que le contrôleur de Symfony a renvoyé une réponse, mais avant que cette réponse ne soit renvoyée au navigateur.

`kernel.response`: Déclenché juste avant que la réponse de Symfony ne soit renvoyée au navigateur.

`kernel.terminate`: Déclenché après que la réponse de Symfony a été renvoyée au navigateur.

<>

> Ces événements peuvent être écoutés et y attacher des listeners (ou "écouteurs"), qui peuvent effectuer des actions spécifiques en réponse à ces événements. Les événements intégrés de Symfony sont souvent utilisés pour effectuer des actions générales, telles que la journalisation, la sécurité, la traduction, etc. de manière centralisée, sans avoir besoin de répéter le même code dans toute l'application.