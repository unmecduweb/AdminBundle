<?php
namespace Mweb\AdminBundle\Listener;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This is to show admin menu on each views when user is granted admin
 *
 * @author jona
 */
class AdminMenu
{

        private $token_storage;
        private $twig;
        private $adminEntities;
        private $adminMenu;

        public function __construct(\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $token_storage, \Twig_Environment $twig, $adminEntities, $adminMenu)
        {
                $this->token_storage = $token_storage;
                $this->twig = $twig;
                $this->adminEntities = $adminEntities;
                $this->adminMenu = $adminMenu;
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
}
