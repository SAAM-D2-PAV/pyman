#  PYMAN
## Gestion des demandes de prestations audiovisuelles
> version Beta - 2022304
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
DATABASE_URL="mysql://(user):(password)@127.0.0.1:3306/(database-name)?serverVersion=10.5.8-MariaDB"
MAILER_DSN=smtp://localhost or your smtp server (you can use mailtrap)
```

```sh
composer update
```
```sh
composer update
```

```sh
yarn install
```

[Doctrine]

```sh
php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate
```

```sh
symfony serve (https) / symfony serve -d / symfony serve -no--tls (http)
```

```sh
Web server listening                                                               
The Web server is using PHP FPM 7.3.24                                             
https://127.0.0.1:8000                                                             
```       

Webpack Encore

```sh
yarn watch
```

Fixtures !!!!!! no longer available !!!!!!
```sh
bin/console doctrine:fixtures:load 
```
[API]

Certaines données sont accessibles via l'api.

L'authentification se fait via le [bundle] Lexik JWT Authentification.
Copier le code ci-dessous dans le fichier .env.local
```sh
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=
REACT_APP_URL="https://localhost:8000/" VOTRE URL
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

.env || .env.local
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




## Development

Composer.json
```sh
 "require": {
       "php": ">=8.0",
        "ext-ctype": "*",
        "ext-iconv": "*",
        "ext-json": "*",
        "api-platform/core": "^2.7",
        "composer/package-versions-deprecated": "1.11.99.1",
        "doctrine/annotations": "^1.0",
        "doctrine/doctrine-bundle": "^2.2",
        "doctrine/doctrine-fixtures-bundle": "^3.4",
        "doctrine/doctrine-migrations-bundle": "^3.0",
        "doctrine/orm": "^2.8",
        "friendsofsymfony/ckeditor-bundle": "^2.3",
        "knplabs/knp-paginator-bundle": "^5.6",
        "lexik/jwt-authentication-bundle": "^2.16",
        "nelmio/cors-bundle": "^2.2",
        "phpdocumentor/reflection-docblock": "^5.2",
        "phpstan/phpdoc-parser": "^1.8",
        "sensio/framework-extra-bundle": "^6.0",
        "spipu/html2pdf": "^5.2",
        "symfony/apache-pack": "^1.0",
        "symfony/asset": "5.4.*",
        "symfony/console": "5.4.*",
        "symfony/debug-bundle": "5.4.*",
        "symfony/dotenv": "5.4.*",
        "symfony/expression-language": "5.4.*",
        "symfony/flex": "^1.3.1",
        "symfony/form": "5.4.*",
        "symfony/framework-bundle": "5.4.*",
        "symfony/mailer": "5.4.*",
        "symfony/maker-bundle": "1.43",
        "symfony/mime": "5.4.*",
        "symfony/monolog-bundle": "^3.0",
        "symfony/property-access": "5.4.*",
        "symfony/property-info": "5.4.*",
        "symfony/proxy-manager-bridge": "5.4.*",
        "symfony/security-bundle": "5.4.*",
        "symfony/serializer": "5.4.*",
        "symfony/stopwatch": "5.4.*",
        "symfony/string": "5.4.*",
        "symfony/twig-bundle": "5.4.*",
        "symfony/ux-autocomplete": "^2.7",
        "symfony/ux-chartjs": "^2.7",
        "symfony/ux-react": "^2.7",
        "symfony/validator": "5.4.*",
        "symfony/var-dumper": "5.4.*",
        "symfony/web-profiler-bundle": "5.4.*",
        "symfony/webpack-encore-bundle": "^1.15",
        "symfony/yaml": "5.1.*",
        "symfonycasts/reset-password-bundle": "^1.7",
        "twig/cssinliner-extra": "^3.3",
        "twig/extra-bundle": "^3.3",
        "twig/twig": "^2.12|^3.0"
    },
```
package.json
```sh
"dependencies": {
        "@babel/preset-react": "^7.18.6",
        "@hotwired/stimulus": "^3.0.0",
        "@symfony/stimulus-bridge": "^3.0.0",
        "@symfony/ux-autocomplete": "file:vendor/symfony/ux-autocomplete/assets",
        "@symfony/ux-chartjs": "file:vendor/symfony/ux-chartjs/assets",
        "@symfony/ux-react": "file:vendor/symfony/ux-react/assets",
        "@symfony/webpack-encore": "^2.0.0",
        "autoprefixer": "^10.4.14",
        "bootstrap": "^4.6.0",
        "chart.js": "^3.4.1",
        "core-js": "^3.0.0",
        "dotenv-webpack": "^8.0.1",
        "file-loader": "^6.0.0",
        "jquery": "^3.5.1",
        "popper.js": "^1.16.1",
        "postcss": "^8.4.22",
        "postcss-loader": "^7.2.4",
        "react": "^18.0",
        "react-dom": "^18.0",
        "regenerator-runtime": "^0.13.2",
        "sass": "^1.32.8",
        "sass-loader": "^12.6.0",
        "stimulus": "^2.0.0",
        "tailwindcss": "^3.3.1",
        "tom-select": "^2.0.1",
        "webpack-notifier": "^1.6.0",
        "animate.css": "^4.1.1",
        "axios": "^1.3.5",
        "ckeditor": "^4.12.1",
        "shufflejs": "^6.0.0",
        "simple-datatables": "^3.0.2",
        "sweetalert2": "^11.3.8"
}
```


[//]: # (These are reference links used in the body of this note and get stripped out when the markdown processor does its job. There is no need to format nicely because it shouldn't be seen. Thanks SO - http://stackoverflow.com/questions/4823468/store-comments-in-markdown-syntax)

   
  
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

