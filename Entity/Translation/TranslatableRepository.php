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

        /**
         * Find an entry by criteria
         * Need this special function, because of translatable
         * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232
         *
         * @param $params
         * @return mixed
         */
        public function findOneBySlug($params)
        {
                if(is_array($params)){
                        $slug = $params['slug'];
                }else{
                        $slug = $params;
                }

                $query = $this->createQueryBuilder('object')
                        ->where('object.slug = :slug')
                        ->setParameter('slug', $slug)
                        ->getQuery();

                $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

                return $query->getOneOrNullResult();
        }


        /**
         * Find an entry by criteria
         * Need this special function, because of translatable
         * https://github.com/stof/StofDoctrineExtensionsBundle/issues/232
         *
         * @param $params
         * @return mixed
         */
        public function findOneByDevAlias($devAlias, $locale)
        {

                $query = $this->createQueryBuilder('object')
                        ->where('object.devAlias = :devAlias')
                        ->andWhere('object.status = 1')
                        ->andWhere('object.localesEnabled LIKE :locale')
                        ->setParameter(':locale', '%' . $locale . '%')
                        ->setParameter('devAlias', $devAlias)
                        ->getQuery();

                $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

                return $query->getOneOrNullResult();
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

        public function mwFindBy($criteria, $_locale, array $orderBy = null, $limit = null, $offset = null)
        {

                $qb = $this->createQueryBuilder('object');
                $qb->where('object.status = 1');
                $qb->andWhere('object.localesEnabled LIKE :locale OR object.localesEnabled is null OR object.localesEnabled = :empty');


                $i=0;
                foreach ($criteria as $key => $criterion) {
                        if($criterion == null) $condition = 'object.' . $key . ' is null';
                        else if($criterion == "not null") $condition = 'object.' . $key . ' is not null';
                        else $condition = 'object.' . $key . ' = :param' . $i;

                        $qb->andWhere($condition);
                        $i++;
                }
                $i=0;
                foreach ($criteria as $key => $criterion) {
                        if($criterion != null && $criterion != 'not null')$qb->setParameter('param' . $i, $criterion);
                        $i++;
                }

                $qb->setParameter(':locale', '%'.$_locale.'%');
                $qb->setParameter(':empty', serialize([]));

                if($orderBy)$qb->orderBy('object.'.key($orderBy),$orderBy[key($orderBy)]);
                else $qb->orderBy('object.created','DESC');
                if($limit)$qb->setMaxResults($limit);
                if($offset)$qb->setFirstResult($offset);

                $query = $qb->getQuery();

                return $query->getResult();
        }
}
