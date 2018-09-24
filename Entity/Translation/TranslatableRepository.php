<?php

namespace Mweb\AdminBundle\Entity\Translation;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Gedmo\Sortable\Entity\Repository\SortableRepository;
use Gedmo\Translatable\TranslatableListener;


/**
 * Class TranslatableRepository
 *
 * This is my translatable repository that offers methods to retrieve results with translations
 */
class TranslatableRepository extends SortableRepository
{
        
        /**
         * @var string Default locale
         */
        protected $defaultLocale;
        
        /**
         * Sets default locale
         *
         * @param string $locale
         */
        public function setDefaultLocale($locale)
        {
                $this->defaultLocale = $locale;
        }
        
        /**
         * Returns translated one (or null if not found) result for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         * @param string $hydrationMode A Doctrine results hydration mode
         *
         * @return QueryBuilder
         */
        public function getOneOrNullResult(QueryBuilder $qb, $locale = null, $hydrationMode = null)
        {
                return $this->getTranslatedQuery($qb, $locale)->getOneOrNullResult($hydrationMode);
        }
        
        /**
         * Returns translated results for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         * @param string $hydrationMode A Doctrine results hydration mode
         *
         * @return QueryBuilder
         */
        public function getResult(QueryBuilder $qb, $locale = null, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
        {
                return $this->getTranslatedQuery($qb, $locale)->getResult($hydrationMode);
        }
        
        /**
         * Returns translated array results for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         *
         * @return QueryBuilder
         */
        public function getArrayResult(QueryBuilder $qb, $locale = null)
        {
                return $this->getTranslatedQuery($qb, $locale)->getArrayResult();
        }
        
        /**
         * Returns translated single result for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         * @param string $hydrationMode A Doctrine results hydration mode
         *
         * @return QueryBuilder
         */
        public function getSingleResult(QueryBuilder $qb, $locale = null, $hydrationMode = null)
        {
                return $this->getTranslatedQuery($qb, $locale)->getSingleResult($hydrationMode);
        }
        
        /**
         * Returns translated scalar result for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         *
         * @return QueryBuilder
         */
        public function getScalarResult(QueryBuilder $qb, $locale = null)
        {
                return $this->getTranslatedQuery($qb, $locale)->getScalarResult();
        }
        
        /**
         * Returns translated single scalar result for given locale
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         *
         * @return QueryBuilder
         */
        public function getSingleScalarResult(QueryBuilder $qb, $locale = null)
        {
                return $this->getTranslatedQuery($qb, $locale)->getSingleScalarResult();
        }
        
        /**
         * Returns translated Doctrine query instance
         *
         * @param QueryBuilder $qb A Doctrine query builder instance
         * @param string $locale A locale name
         *
         * @return Query
         */
        protected function getTranslatedQuery(QueryBuilder $qb, $locale = null)
        {
                $locale = null === $locale ? $this->defaultLocale : $locale;
                
                $query = $qb->getQuery();
                
                $query->setHint(
                        Query::HINT_CUSTOM_OUTPUT_WALKER,
                        'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
                );
                
                $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
                
                return $query;
        }
        
        /**
         * Finds all entities in the repository.
         *
         * @return array The entities.
         */
        public function findAll()
        {
                return $this->findBy(array());
        }
        
        /**
         * Finds entities by a set of criteria.
         * Default option in mweb status and statusTrans at 1
         *
         * @param array $criteria
         * @param array|null $orderBy
         * @param int|null $limit
         * @param int|null $offset
         *
         * @return array The objects.
         */
        public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
        {
                
                //Si le critère n'existe pas par défaut on sélectionne les entités avec un statut à 1
                if (!isset($criteria['status'])) $criteria['status'] = 1;
                if ($criteria['status'] === false) unset($criteria['status']);
                
                return parent::findBy($criteria, $orderBy, $limit, $offset);
                
        }
        
        /**
         * Finds a single entity by a set of criteria.
         * Default option in mweb status and statusTrans at 1
         *
         * @param array $criteria
         * @param array|null $orderBy
         *
         * @return object|null The entity instance or NULL if the entity can not be found.
         */
        public function findOneBy(array $criteria, array $orderBy = null)
        {
                
                if (!isset($criteria['status'])) $criteria['status'] = 1;
                
                return parent::findOneBy($criteria, $orderBy);
        }


        public function findOldUrl($url, $locales)
        {
                $qb = $this->createQueryBuilder('entity')
                        ->select('entity')
                        ->where('entity.oldUrl LIKE :oldUrl')
                        ->andWhere('entity.status = 1')
                        ->setParameter(':oldUrl', '%'.$url.'%');

                foreach ($locales as $locale) {
                        $query[$locale] = $qb->getQuery();
                        $query[$locale]->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
                        $query[$locale]->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
                        $results[$locale]  = $query[$locale]->getResult();
                }

                return $results;
        }
}
