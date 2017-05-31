<?php

namespace Mweb\AdminBundle\Controller;

use Mweb\AdminBundle\Form\RegistrationType;
use Mweb\AdminBundle\Form\EditUserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UserController extends Controller
{
        
        
        public function editAction(Request $request, $userId)
        {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');
                $currentUser = $this->get('security.token_storage')->getToken()->getUser();
                
                if ($userId != "undefined" && $userId !== null && $userId !== 'new') {
                        if ($currentUser->hasRole('ROLE_SUPER_ADMIN')) {
                                $userEdit = $userManager->findUserBy(['id' => $userId]);
                                $form = $this->createForm(EditUserType::class, $userEdit);
                        } else {
                                throw new HttpException(403, 'You are not allow to edit a user');
                                
                        }
                } else {
                        
                        $userEdit = $userManager->createUser();
                        $userEdit->setEnabled(true);
                        $userEdit->addRole('ROLE_ADMIN');
                        $form = $this->createForm(RegistrationType::class, $userEdit);
                }
                
                
                $form->setData($userEdit);
                
                $form->handleRequest($request);
                
                if ($form->isSubmitted()) {
                        $error=false;
                        //Check if email exist
                        $exists = $userManager->findUserBy(array('email' => $userEdit->getEmail()));
                        if ($exists instanceof User) {
                                $form->get('email')->addError(new FormError('L\'email est déjà utilisé'));
                                $error=true;
                        }
                        
                        //Check if email exist
                        $exists = $userManager->findUserBy(array('username' => $userEdit->getUsername()));
                        if ($exists instanceof User) {
                                $error=true;
                                $form->get('username')->addError(new FormError('Le nom d\'utilisateur est déjà utilisé'));
                        }
                        
                        if ($form->isValid() && !$error) {
                                
                                $userManager->updateUser($userEdit);
                                $this->addFlash('success', $this->get('translator')->trans('admin.user.successCreation', array(), 'mweb'));
                                
                                return $this->redirect($this->generateUrl('mweb_admin_user_list'));
                                
                        }
                }
                
                
                return $this->render('MwebAdminBundle:User:edit.html.twig', array(
                        'form' => $form->createView()
                ));
        }
        
        public function listAction(Request $request)
        {
        
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');
        
                $users = $userManager->findUsers();
                
                 return $this->render('MwebAdminBundle:User:list.html.twig', [
                         'entityAlias' => 'user',
                         'entities' => $users
                 ]);
        }
        
        
}
