#  PYMAN
## Gestion des demandes de prestations audiovisuelles
> version 1.2 - 2022304
<br/></br>
Pyman est une application développée par le SAAM D2 (service de l'action administrative et des moyens) de l'administration centrale. https://www.education.gouv.fr/service-de-l-action-administrative-et-des-moyens-saam-6818

Le service de l'action administrative et des moyens assure la gestion administrative, logistique et financière de l'administration centrale. Il contribue au fonctionnement des services administratifs du ministère de l'éducation nationale et de la jeunesse.


## Présentation

L'application de gestion de projets audiovisuels est une solution qui permet de créer, gérer et suivre des projets. Elle offre une interface utilisateur conviviale et des fonctionnalités avancées pour simplifier la gestion de projets.

L'application permet aux utilisateurs de créer des projets, d'y ajouter des tâches et des documents. Chaque tâche peut être attribuée à une ou plusieurs personnes être liée à du matériel, des documents, un lieu de prestation et sont classées par catégorie.

L'pplication permet également de centraliser des documents et des informations sur les lieux de prestation et le matériel utilisé, facilitant ainsi la gestion des ressources pour chaque projet. Un calendrier intégré permet une lecture des tâches par jour, semaine et mois, avec la possibilité de trier et de filtrer les tâches selon différents critères.

En plus de cela, l'application dispose d'un système de notification qui alerte les utilisateurs, ce qui permet de rester informé des changements importants sans avoir à surveiller constamment l'application.

Enfin, l'application de gestion de projets audiovisuels offre des statistiques détaillées sur les projets, les tâches et les demandeurs.


L'application est toujours en cours de développement avec Symfony version 5.4.* [Site de Symfony][Symfony 5] et PHP 8.0.* 15 avril 2022)

## Tech

Pyman utilise les technologies suivantes :

- [Symfony 5] - Symfony est un ensemble de composants PHP réutilisables et un framework PHP pour les projets web
- [Twig] - Moteur de template pour PHP
- [Webpack] - 
- [Bootstrap] - 
- [API plateform] -

## Installation

Pyman nécessite [composer], [Node], [Yarn] et [git] pour fonctionner.


```sh
git clone git@github.com:SAAM-D2-PAV/pyman.git
```

```sh
touch .env.local
```
ajouter ces deux lignes au fichier 
```sh
DATABASE_URL="mysql://(user):(password)@127.0.0.1:3306/(database-name)?serverVersion=10.5.8-MariaDB"
MAILER_DSN=smtp://localhost or your smtp server (you can use mailtrap)
```

```sh
composer install
```
```sh
yarn/npm install
```

[API]

Certaines données sont accessibles via l'api.

L'authentification se fait via le [bundle] Lexik JWT Authentification.
Copier le code ci-dessous dans le fichier .env.local
```sh
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
###< nelmio/cors-bundle ###
```

Ne pas oublier de générer les clés SSL
Generate the SSL keys

```sh
php bin/console lexik:jwt:generate-keypair
```
### Cross-Origin Resource Sharing (CORS)

Autorisation d'accès à l'API et gestion des [CORS]

.env.local
```sh
ici seul le locahost est autorisé à accèder à l api
###> nelmio/cors-bundle ###
CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'
###< nelmio/cors-bundle ###
```
 config / packages / nelmio_cors.yaml
```sh
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
    paths:
        '^/': null
 ```

## Lancer l'application

```sh
symfony serve (https) / symfony serve -d / symfony serve -no--tls (http)
```     

Webpack Encore

```sh
yarn encore dev watch
```

## Plugins et Librairies CDN

Pyman utilise les plugins et librairies suivantes dans son code

| Plugin | README |
| ------ | ------ |
| Font-Awesome | https://fontawesome.com/ |
| Animate CSS | https://animate.style/|
| Materia Bootstrap Theme (for dev version) | https://bootswatch.com/materia/ |
| Simple-DataTables | https://github.com/fiduswriter/Simple-DataTables |
| Full Calendar | https://fullcalendar.io/ |
| CKEditor | https://symfony.com/bundles/FOSCKEditorBundle/current/installation.html |
| chart.js | https://www.chartjs.org/docs/latest/ |
| shufflejs | https://vestride.github.io/Shuffle/ |




   
  
   [Symfony 5]: <https://symfony.com/>
   [composer]: <https://getcomposer.org/doc/00-intro.md>
   [Twig]: <https://twig.symfony.com/>
   [Webpack]: <https://symfony.com/doc/current/frontend.html>
   [git]: <https://git-scm.com/>
   [Bootstrap]: <https://getbootstrap.com/docs/5.0/getting-started/introduction/>
   [Yarn]: <https://yarnpkg.com/>
   [Node]: <https://nodejs.org/en/>
   [Doctrine]: <https://symfony.com/doc/current/doctrine.html#installing-doctrine>
   [model-db]: ./doc/modelisation-db.jpg "Modèle de données"
   [CKEditor]: <https://symfony.com/bundles/FOSCKEditorBundle/current/installation.html>
   [API Plateform]: https://api-platform.com/docs
   [schema-db]: ./doc/pyman_dbschema.svg  "schéma de données"
   [API]: https://api-platform.com/docs/distribution/
   [bundle]: https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html
   [CORS]: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS

