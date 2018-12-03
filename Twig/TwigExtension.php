<?php

namespace Mweb\AdminBundle\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Description of MwebADminBundle\TiwgExtension
 *
 * @author Fab
 */
class TwigExtension extends \Twig_Extension
{
        
        
        private $container;
        private $vichHelper;
        private $liipCacheHelper;
        private $requestStack = false;
        private $em;
        private $mwCache;
        
        public function __construct($container, RequestStack $requestStack,  $em)
        {
                $this->mwCache = null;
                $this->container = $container;
                $this->em = $em;
                $this->requestStack = $requestStack->getCurrentRequest();
                
                $this->liipCacheHelper = $this->container->get('liip_imagine.cache.manager');

                
        }

        public function getName()
        {
                return 'mweb_twig_extension';
        }

        public function getFilters()
        {
                return array(
                        new \Twig_SimpleFilter('stripAccents', [$this, 'stripAccents']),
                        new \Twig_SimpleFilter('summary', [$this, 'summary']),
                );
        
        }

        public function getFunctions()
        {
                return array(
                        new \Twig_SimpleFunction('getMwebTranslatedUrls', [$this, 'getMwebTranslatedUrls']),
                        new \Twig_SimpleFunction('liip', [$this, 'liip']),
                        new \Twig_SimpleFunction('mw_liip', [$this, 'mw_liip']),
                        new \Twig_SimpleFunction('getLocales', [$this, 'getLocales']),
                        new \Twig_SimpleFunction('isSelected', [$this, 'isSelected']),
                        new \Twig_SimpleFunction('isAjax', [$this, 'isAjax']),
                        new \Twig_SimpleFunction('array_unset', array($this, 'arrayUnset')),
                        new \Twig_SimpleFunction('getFileManagerFolder', array($this, 'getFileManagerFolder')),
                        new \Twig_SimpleFunction('getElementByDevAlias', array($this, 'getElementByDevAlias')),
                        new \Twig_SimpleFunction('getPagesNav', array($this, 'getPagesNav')),
                        new \Twig_SimpleFunction('mw_cache', array($this, 'mw_cache'))
                );
        }


        function getMwebTranslatedUrls()
        {
                $ret = array();
                $langs = explode('|',$this->container->getParameter('locales'));
                $currentRoute = $this->requestStack->attributes->get('_route');
                $params = array_merge((array)$this->requestStack->attributes->get('_route_params'), $_GET);

//                $curLocale = $this->getLocale();
                if ($currentRoute) {
                        foreach($langs as $lg) {
                                $ret[$lg] = $this->container->get('router')->generate($currentRoute, array_merge($params, array('_locale'=>$lg)));
                        }
                }

                return $ret;
        }
        
        /**
         * Retourne la liste des locales
         *
         * @param bool $excludeCurrent
         * @return array
         *
         */
        function getLocales($excludeCurrent = false)
        {
                $langs = explode('|',$this->container->getParameter('locales'));
                
                if($excludeCurrent)
                        unset($langs[$this->getLocale()]);
                
                return $langs;
        }


        public function liip($entity, $thumb, $field="imageFile"){
        
                $path = $this->vichHelper->asset($entity, $field);
                // Suppression du premier / erreur entre vich et liip
                $path = trim(substr($path,1,strlen($path)));
                
                if($path) {
                        
                        return $this->liipCacheHelper->getBrowserPath($path, $thumb);
                }else{
                        
                        if($this->container->getParameter('mweb_admin.noIllu'))return $this->liipCacheHelper->getBrowserPath($this->container->getParameter('mweb_admin.noIllu'), $thumb);
                        return '';
                }
        }
        
        public function mw_liip($path, $thumb){
                
                $path = $this->getFileManagerFolder().$path;
                
                if($path) {
                        
                        return $this->liipCacheHelper->getBrowserPath($path, $thumb);
                }else{
                        
                        if($this->container->getParameter('mweb_admin.noIllu'))return $this->liipCacheHelper->getBrowserPath($this->container->getParameter('mweb_admin.noIllu'), $thumb);
                        return '';
                }
        }

        public function isAjax()
        {

                return $this->container->get('request')->isXmlHttpRequest();
        }

        function isSelected($var, $val, $class = "selected", $defaultClass = "")
        {
                $return = $defaultClass;

                if ($var == $val) {
                        $return = $class;
                }

                return $return;
        }


        public function beautifulDate(\DateTime $date, $locale)
        {

                $months = [
                        'fr' => ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
                        'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Augustus', 'September', 'October', 'November', 'December']
                ];

                $month = $months[$locale][$date->format('n') - 1];

                switch ($locale) {
                        case 'fr':
                                return $date->format('j') . ' ' . $month . ' ' . $date->format('Y');
                                break;
                        case 'en':
                                return $month . ' ' . $date->format('j') . $date->format('S') . ' ' . $date->format('Y');
                                break;
                }
        }

        /**
         * Delete a key of an array
         *
         * @param array $array Source array
         * @param string $key The key to remove
         *
         * @return array
         */
        public function arrayUnset($array, $key)
        {
                unset($array[$key]);

                return $array;
        }
        
        /**
         * Remove accent
         *
         * @param $str
         *
         * @return string
         */
        
        public function stripAccents($str){
                return iconv('UTF-8', 'US-ASCII//TRANSLIT', $str);
                
        }
        
        /**
         * Retourne le chemin vers le dossier d'uploads de responsiveFilemanager
         *
         * @return string
         */
        public function getFileManagerFolder(){
                return $this->container->getParameter('mweb_admin.fileManagerFolder').'/';
        }
        
        public function getPagesNav()
        {
                $entites = $this->container->getParameter('mweb_admin.entities');
                if(isset($entites['page']['class'])){
                        $repo = $this->em->getRepository($entites['page']['class']);
                        $pages = $repo->findByShowInMenu(1, array('position' => 'asc'));
                }
                
                return $pages;
        }
        
        public function getElementByDevAlias($devAlias, $entity = 'page'){
                $entites = $this->container->getParameter('mweb_admin.entities');
                if(isset($entites[$entity]['class'])){
                        $repo = $this->em->getRepository($entites[$entity]['class']);
                        $object = $repo->findOneByDevAlias($devAlias, $this->requestStack->getLocale());
                        return $object;
                }else{
                        return false;
                }
                
        }

        /**
         * Apllique un paramètre aux URLs de CSS ET DE JS pour supprimer la cache quand il y a besoin
         *
         * @return string
         */
        public function mw_cache()
        {
                if(!$this->mwCache) {
                        $mw_conf_cache = $this->em->getRepository('MwebAdminBundle:Config')->findOneByConfName('mw_cache');
                        if($mw_conf_cache === null) $this->mwCache= '0';
                        else $this->mwCache = $mw_conf_cache->getConfValue();

                }
                return '?mwc=' .$this->mwCache;
        }

        /**
         * Limiter la taille dun string
         * @param $str
         * @param int $limit
         * @return string
         */
        public function summary($str, $limit = 180)
        {
                $text = strip_tags($str);
                return substr($text, 0, $limit) . '[...]';
        }
}
