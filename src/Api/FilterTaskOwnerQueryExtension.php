<?php
namespace App\Api;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;

/* Filtrer les commentaires exposés par l'API

Par défaut, API Platform expose toutes les entrées de la base de données. Mais pour les commentaires, seuls ceux qui ont été publiés devraient apparaître dans l'API.

Lorsque vous avez besoin de filtrer les éléments retournés par l'API, créez un service qui implémente QueryCollectionExtensionInterface pour gérer la requête Doctrine utilisée pour les collections, et/ou QueryItemExtensionInterface pour gérer les éléments : */


//https://symfony.com/doc/current/the-fast-track/fr/26-api.html

class FilterTaskOwnerQueryExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        if (User::class === $resourceClass) {
            $queryBuilder->andWhere(sprintf("%s.taskOwner = '1'", $queryBuilder->getRootAliases()[0]));
        }
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        if (User::class === $resourceClass) {
            $queryBuilder->andWhere(sprintf("%s.taskOwner = '1'", $queryBuilder->getRootAliases()[0]));
        }
    }
}