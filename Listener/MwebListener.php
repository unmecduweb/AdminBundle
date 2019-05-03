<?php
namespace Mweb\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This is to show admin menu on each views when user is granted admin
 *
 * @author fab
 */
class MwebListener
{

        private $token_storage;
        private $twig;
        private $adminEntities;
        private $adminMenu;
        private $em;

        public function __construct(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $token_storage, \Twig_Environment $twig, $adminEntities, $adminMenu, EntityManager $em)
        {
                $this->token_storage = $token_storage;
                $this->twig = $twig;
                $this->adminEntities = $adminEntities;
                $this->adminMenu = $adminMenu;
                $this->em = $em;
        }

        public function showMenu(\Symfony\Component\HttpKernel\Event\FilterResponseEvent $event)
        {
                $request = $event->getRequest();

                // Matched route
                $_route  = $request->attributes->get('_route');


                $token = $this->token_storage->getToken();
                if ($event->isMasterRequest() && $token && is_object($token->getUser()) && (substr($_route,0,6)=='lexik_' || substr($_route,0,8)=='fos_user' || substr($_route,0,10)=='mweb_admin') && $_route!='mweb_admin_tinymce_browser') {

                        if ($token->getUser()->hasRole('ROLE_ADMIN')) {

                                $response = $event->getResponse();
                                $menu = $this->twig->render('MwebAdminBundle:menu:menu.html.twig', ['adminEntities' => $this->adminEntities, 'adminMenu' => $this->adminMenu]);

                                if (preg_match('#<html#', $response->getContent())) {
                                        $response->setContent(preg_replace('#(<body[ a-zA-Z0-9\"\'=_-]*>)#', '$1' . $menu, $response->getContent()));
                                }

                                return $response;
                        }


                }
        }

        //Vérifie le status du site, si hors ligne, et si l'utilisateur n'est pas admin, page temporaire
        public function startWebsite(GetResponseEvent $event){

                if ($event->isMasterRequest()) {
                        $request = $event->getRequest();
                        $_route = $request->attributes->get('_route');

                        $mw_status = $this->em->getRepository('MwebAdminBundle:Config')->findOneByConfName('mw_status_website');
                        $token = $this->token_storage->getToken();

                        $user = null;
                        if ($token !== null) $user = $token->getUser();

                        if ((substr($_route, 0, 8) !== 'fos_user') && substr($_route, 0, 10) !== 'mweb_admin') {
                                if (!is_object($user) || !$user->hasRole('ROLE_ADMIN')) {
                                        if ($mw_status->getConfValue() == 'offline') {
                                                $response = new Response();

                                                $response->setStatusCode(Response::HTTP_OK);
                                                $response->headers->set('Content-Type', 'text/html');
                                                if($this->twig->getLoader()->exists('MwebCoreBundle:Page:offline.html.twig')){

                                                        $response->setContent($this->twig->render('MwebCoreBundle:Page:offline.html.twig'));

                                                }else{

                                                        $response->setContent($this->twig->render('MwebAdminBundle:Page:offline.html.twig'));

                                                }
                                                $event->setResponse($response);

                                        }

                                }
                        }
                }
        }
}
