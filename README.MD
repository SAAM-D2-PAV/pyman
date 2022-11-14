#  PYMAN
## Gestion des demandes de prestations audiovisuelles
> version Beta - 2022205
<br/></br>
Pyman est une application développée par le pôle audiovisuel du SAAM D2 (service de l'action administrative et des moyens) de l'administration centrale. https://www.education.gouv.fr/service-de-l-action-administrative-et-des-moyens-saam-6818

Le service de l'action administrative et des moyens assure la gestion administrative, logistique et financière de l'administration centrale. Il contribue au fonctionnement des services administratifs du ministère de l'éducation nationale et de la jeunesse.


## Features

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
composer install or update
```
[CKEditor]
```sh
yarn add ckeditor@^4.13.0
yarn install/update
```

```sh
touch .env.local
DATABASE_URL="mysql://(user):(password)@127.0.0.1:3306/(database-name)?serverVersion=10.5.8-MariaDB"
MAILER_DSN=smtp://localhost or your smtp server (you can use mailtrap)
```


[Doctrine]


![Modèle de données][model-db]

![Schéma de données][schema-db]


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
        "symfony/maker-bundle": "^1.29",
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
        "symfony/ux-autocomplete": "^2.2",
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
"devDependencies": {
        "@hotwired/stimulus": "^3.0.0",
        "@symfony/stimulus-bridge": "^3.0.0",
        "@symfony/ux-autocomplete": "file:vendor/symfony/ux-autocomplete/assets",
        "bootstrap": "^4.6.0",
        "@symfony/webpack-encore": "^2.0.0",
        "core-js": "^3.0.0",
        "file-loader": "^6.0.0",
        "jquery": "^3.5.1",
        "popper.js": "^1.16.1",
        "regenerator-runtime": "^0.13.2",
        "sass": "^1.32.8",
        "sass-loader": "^12.6.0",
        "stimulus": "^2.0.0",
        "tom-select": "^2.0.1",
        "webpack-notifier": "^1.6.0"
    },
"dependencies": {
     "animate.css": "^4.1.1",
     "chart.js": "^3.6.0",
     "ckeditor": "^4.12.1",
     "shufflejs": "^6.0.0",
     "simple-datatables": "^3.0.2",
     "sweetalert2": "^11.3.8"
}
```

## License

**Free Software, Hell Yeah!**

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

