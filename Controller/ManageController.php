<?php

namespace Mweb\AdminBundle\Controller;

use Doctrine\Common\Annotations\AnnotationReader;
use Mweb\AdminBundle\Form\ConfirmDeleteType;
use Mweb\CoreBundle\Entity\Content;
use Mweb\AdminBundle\Entity\Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Mweb\AdminBundle\Form\PositionType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ManageController extends Controller
{
        
        
        public function indexAction(Request $request)
        {
                $langs = explode('|', $this->container->getParameter('locales'));
        
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
        
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entities = [];
                foreach ($adminParams as $entityName=>$entityParams){
                        if($entityParams['showOnHome'] === true) {
                                //Appel le repository de l'entité visionné
                                $entityRepo = $em->getRepository($entityParams['class']);
                                //Appel la liste des items qui compose cette entité
                                $entities[$entityName] = $entityRepo->findBy(array('status' => array(0, 1)), array('updated' => 'DESC'), 4);
                        }
                }
        
        
                return $this->render('MwebAdminBundle::index.html.twig', [
                        'entities' => $entities,
                        'entitiesParams' => $adminParams,
                        'langs' => $langs
                ]);
                
        }
        
        public function viewAction(Request $request, $entityAlias, $id)
        {
                
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entityParams = $adminParams[$entityAlias];
                
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
                
                //Appel le repository de l'entité visionné
                $entityRepo = $em->getRepository($entityParams['class']);
                
                $entityEdit = $entityRepo->findOneBy(array('id' => $id));
                
                
                $output = array(
                        'entity' => $entityView,
                        'id' => $id,
                        'entityAlias' => $entityAlias
                );
                
                return $this->render('MwebAdminBundle:' . $entityParams['views'] . ':view.html.twig', $output);
        }
        
        
        public function listAction($entityAlias, Request $request)
        {
                
                $langs = explode('|', $this->container->getParameter('locales'));
                
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entityParams = $adminParams[$entityAlias];
                
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
                
                //Appel le repository de l'entité visionné
                $entityRepo = $em->getRepository($entityParams['class']);
                //Appel la liste des items qui compose cette entité
                $entities = $entityRepo->findBy(array('status' => array(0, 1)));
                
                return $this->render('MwebAdminBundle:' . $entityParams['views'] . ':list.html.twig', [
                        'entityAlias' => $entityAlias,
                        'entities' => $entities,
                        'entityParams' => $entityParams,
                        'langs' => $langs
                ]);
        }
        
        public function editPositionAction($entityAlias, Request $request)
        {
                
                $langs = explode('|', $this->container->getParameter('locales'));
                
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entityParams = $adminParams[$entityAlias];
                
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
                
                //Appel le repository de l'entité visionné
                $entityRepo = $em->getRepository($entityParams['class']);
                //Appel la liste des items qui compose cette entité
                $entities = $entityRepo->findBy(array('status' => array(0, 1)), array('position' => 'asc'));
        
                $positionForm =$this->createFormBuilder(array('entities', $entities))
                        ->add('entities', CollectionType::class ,array(
                                'required'       => true,
                                'allow_add'      => true,
                                'entry_type'  => PositionType::class,
                        ))
                        ->getForm();
        
        
                $positionForm->handleRequest($request);
        
                if($positionForm->isValid()){
                        
                        foreach ($positionForm->get('entities')->getData() as $elm){
                                $entity = $entityRepo->find($elm['id']);
                                $entity->setPosition(intval($elm['position']));
                                $em->persist($entity);
                                
                        }
                        $em->flush();
        
                        $this->addFlash('success', $this->get('translator')->trans('admin.edit.positionSuccess', array(), 'mweb'));
                        
                        return $this->redirect($this->generateUrl('mweb_admin_list_entity', ['entityAlias' => $entityAlias]));
                        
                }
                
                return $this->render('MwebAdminBundle:' . $entityParams['views'] . ':edit-position.html.twig', [
                        'entityAlias' => $entityAlias,
                        'entities' => $entities,
                        'entityParams' => $entityParams,
                        'langs' => $langs,
                        'positionForm' => $positionForm->createView()
                ]);
        }
        
        public function editAction($entityAlias, $id, $_locale, Request $request)
        {
        
                $user = $this->get('security.token_storage')->getToken()->getUser();
                
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entityParams = $adminParams[$entityAlias];
                
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
                
                //Appel le repository de l'entité visionné
                $entityRepo = $em->getRepository($entityParams['class']);
                
                if ($id != "undefined" && $id !== null && $id !== 'new') {
                        $entityEdit = $entityRepo->find($id);
                        $cloneEntityEdit = clone($entityEdit);
                        $entityEdit->setTranslatableLocale($_locale);
                        $em->refresh($entityEdit);

        
                } else {
                        $entityEdit = new $entityParams['class'];
                        $entityEdit->setCreatedBy($user);
                }
                
                
                $locales = explode('|', $this->getParameter('locales'));
                if(($key = array_search($_locale, $locales)) !== false) {
                        unset($locales[$key]);
                }
                
                
                $form = $this->createForm($entityParams['form'], $entityEdit, array(
                        'action' => $this->generateUrl('mweb_admin_edit_entity', ['entityAlias' => $entityAlias, 'id' => $id, '_locale' => $_locale]),
                        'attr' => array('locales' => $locales)
                        
                        
                ));
                
                //SI LE FORM EST POSTE
                if($request->request->get($form->getName())) {
                        $reflectionClass = new \ReflectionClass(get_class($entityEdit));
        
                        // Prepare doctrine annotation reader
                        $reader = new AnnotationReader();
                        
                        // Lecture des annotations
                        foreach ($reflectionClass->getProperties() as $reflectionProperty) {

                                // Si ReindexField existe on reindex le champ ;)
                                if ($annotation = $reader->getPropertyAnnotation($reflectionProperty, 'Mweb\AdminBundle\Mapping\Annotation\ReindexField')) {

                                        $requestData = $request->request->get($form->getName());
                                        if (isset($requestData[$reflectionProperty->getName()])) {
                                                $requestData[$reflectionProperty->getName()] = array_values($requestData[$reflectionProperty->getName()]);
                                                $request->request->set($form->getName(), $requestData);
                                        }
                                }
                        }
                        
                }
                
                $form->handleRequest($request);
                
                //lorsqu'on envoit la modal
                if ($form->isValid()) {
                        
                        
                        $entityEdit->setTranslatableLocale($_locale);
        
                        $entityEdit->setUpdatedBy($user);

                        if(count($entityEdit->getOldUrl() )) {
                                $oldsUrls = array();
                                //parcours le champ oldURL pour supprimer les valeurs vides
                                foreach ($entityEdit->getOldUrl() as $url) {
                                        if (trim($url)) $oldsUrls[] = $url;
                                }
                                $entityEdit->setOldUrl($oldsUrls);
                        }


                        $em->persist($entityEdit);
                        $em->flush();

                        //Si le slug a changé, on ajoute l'ancienne URL dans le champ oldUrl
                        if(isset($cloneEntityEdit) && $cloneEntityEdit->getSlug() !== $entityEdit->getSlug()){
                                $oldUrl = $entityEdit->getOldUrl();

                                foreach ($entityParams['pathProperties'] as $key=>$pathProperty){
                                        $method = 'get'.ucfirst($pathProperty);
                                        $param[$key] = $cloneEntityEdit->$method();
                                }

                                $oldUrl[] = $this->generateUrl($entityParams['path'], $param, UrlGeneratorInterface::ABSOLUTE_URL);
                                $entityEdit->setOldUrl($oldUrl);
                                $em->persist($entityEdit);
                                $em->flush();

                        }

                        $this->addFlash('success', $this->get('translator')->trans('admin.edit.success', array(), 'mweb'));
                        
                        switch ($form["goTo"]->getData()) {
                                case 'otherLanguages-es':
                                case 'otherLanguages-fr':
                                case 'otherLanguages-de':
                                case 'otherLanguages-en':
                                        $lang = substr($form["goTo"]->getData(), 15, 2);
                                        
                                        return $this->redirect($this->generateUrl('mweb_admin_edit_entity', ['entityAlias' => $entityAlias, 'id' => $id, '_locale' => $lang]));
                                        break;
                                case 'stayHere':
                                        return $this->redirect($this->generateUrl('mweb_admin_edit_entity', ['entityAlias' => $entityAlias, 'id' => $entityEdit->getId(), '_locale' => $_locale]));
                                        break;
                                case 'addAnother':
                                        return $this->redirect($this->generateUrl('mweb_admin_edit_entity', ['entityAlias' => $entityAlias, 'id' => 'new', '_locale' => $_locale]));
                                        break;
                                case 'seeList':
                                        return $this->redirect($this->generateUrl('mweb_admin_list_entity', ['entityAlias' => $entityAlias]));
                                        break;
                                default:
                                        return $this->redirect($this->generateUrl('mweb_admin_list_entity', ['entityAlias' => $entityAlias]));
                                        break;
                        }
                        
                        // redirige sur la liste des entit"s
                        
                        
                } else {
                        //AJAX VERSION
                        if ($request->isXmlHttpRequest()) {
                                $output = array(
                                        'type' => 'success',
                                        'form' => $this->renderView('MwebAdminBundle:' . $entityParams['views'] . ':modal-edit-entity.html.twig', [
                                                'form' => $form->createView(),
                                                'entity' => $entityEdit,
                                                'id' => $id
                                        ])
                                );
                                
                        }
                }
                
                if ($request->isXmlHttpRequest()) {
                        $response = new Response();
                        
                        $response->headers->set('Content-Type', 'application/json');
                        $response->setContent(json_encode($output));
                        return $response;
                } else {
                        $output = array(
                                'form' => $form->createView(),
                                'entity' => $entityEdit,
                                'id' => $id,
                                'entityAlias' => $entityAlias,
                                'lang' => $_locale
                        );
                        
                        return $this->render('MwebAdminBundle:' . $entityParams['views'] . ':edit.html.twig', $output);
                }
        }
        
        public function deleteAction($entityAlias, $id, Request $request)
        {
                //Appel les paramètes du bundle présent ds config.yml
                $adminParams = $this->container->getParameter('mweb_admin.entities');
                //Récupère le tableau de valeur correspondant à l'entité listé
                $entityParams = $adminParams[$entityAlias];
                
                //Appel entityManager
                $em = $this->getDoctrine()->getManager();
                
                //Appel le repository de l'entité visionné
                $entityRepo = $em->getRepository($entityParams['class']);
                
                
                $entityEdit = $entityRepo->find($id);
                
                $form = $this->createForm(ConfirmDeleteType::class, null, array(
                        'action' => $this->generateUrl('mweb_admin_delete_entity', ['entityAlias' => $entityAlias, 'id' => $id])
                ));
                
                $form->handleRequest($request);
                
                $hasBeenDelete = false;
                
                
                if ($form->isValid() && $form->get('confirm')->getData()) {
                        
                        $entityEdit->setStatus(-1);
                        $em->persist($entityEdit);
                        $em->flush();
                        $hasBeenDelete = true;
                        $output['status'] = true;
                        
                        $this->addFlash('success', $this->get('translator')->trans('admin.edit.successDelete', array(), 'mweb'));
        
                        $output['status'] = 'refresh';
                         
                }else{
                        $output['status'] = 'error';
                        $output['message'] = $this->get('translator')->trans('admin.edit.deleteConfirmMiss', array(), 'mweb');
                }
                
                
                //AJAX VERSION
                if ($request->isXmlHttpRequest()) {
                        $output['hasBeenDelete'] = $hasBeenDelete;
                        
                        $output['form'] = $this->renderView('MwebAdminBundle:default:modal-delete-entity.html.twig', [
                                        'form' => $form->createView(),
                                        'entity' => $entityEdit,
                                        'id' => $id
                                ]);
                }
                
                
                if ($request->isXmlHttpRequest()) {
                        $response = new Response();
                        
                        $response->headers->set('Content-Type', 'application/json');
                        $response->setContent(json_encode($output));
                        return $response;
                } else {
                        $output = array(
                                'form' => $form->createView(),
                                'entity' => $entityEdit,
                                'id' => $id,
                                'entityAlias' => $entityAlias,
                        );
                        
                        return $this->render('MwebAdminBundle:' . $entityParams['views'] . ':edit.html.twig', $output);
                        
                        
                }
        }

        public function updateCacheAction(){
                $user = $this->get('security.token_storage')->getToken()->getUser();

                $em = $this->getDoctrine()->getManager();


                $mw_cache = $em->getRepository('MwebAdminBundle:Config')->findOneByConfName('mw_cache');
                if($mw_cache) {
                        $mw_cache->setConfValue(uniqid());
                        $em->persist($mw_cache);
                        $em->flush();
                }else{
                        $mw_cache = new Config();
                        $mw_cache->setConfName('mw_cache');
                        $mw_cache->setConfValue(uniqid());
                        $em->persist($mw_cache);
                        $em->flush();
                }

                $this->addFlash('success', $this->get('translator')->trans('admin.updateCache', array(), 'mweb'));

                return $this->redirect($this->generateUrl('mweb_admin'));
        }
}