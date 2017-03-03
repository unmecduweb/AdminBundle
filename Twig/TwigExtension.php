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
        private $request =false;
        private $vichHelper;
        private $liipCacheHelper;
        private $requestStack = false;
        
        
        public function __construct($container, RequestStack $requestStack)
        {
                $this->container = $container;
                
                if (preg_match('#^2#', Kernel::VERSION)) {
                        $this->request = $this->container->get('request');
                }else{
                        $this->requestStack = $requestStack->getCurrentRequest();
                }
                $this->vichHelper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
                $this->liipCacheHelper = $this->container->get('liip_imagine.cache.manager');
//                if ($this->container->get('router.request_context')->get('_route') !== null) {
//                        $this->locale = $this->container->get('request')->getLocale();
//                }
                
        }

        public function getName()
        {
                return 'mweb_twig_extension';
        }

        public function getFilters()
        {
                return array(
                        new \Twig_SimpleFilter('stripAccents', [$this, 'stripAccents']),
                );
        
        }

        public function getFunctions()
        {
                return array(
                        new \Twig_SimpleFunction('getMwebTranslatedUrls', [$this, 'getMwebTranslatedUrls']),
                        new \Twig_SimpleFunction('liip', [$this, 'liip']),
                        new \Twig_SimpleFunction('getLocales', [$this, 'getLocales']),
                        new \Twig_SimpleFunction('isSelected', [$this, 'isSelected']),
                        new \Twig_SimpleFunction('isAjax', [$this, 'isAjax']),
                        new \Twig_SimpleFunction('array_unset', array($this, 'arrayUnset')),
                );
        }


        function getMwebTranslatedUrls()
        {
                $ret = array();
                $langs = explode('|',$this->container->getParameter('locales'));
                $currentRoute = $this->request->attributes->get('_route');
                $params = array_merge((array)$this->request->attributes->get('_route_params'), $_GET);

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
        
        
}
