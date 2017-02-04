<?php

namespace Mweb\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;

class MenuController extends EntityController
{
//    public function deleteAction($entity,$id, Request $request)
//    {
//        
//        $em = $this->getDoctrine()->getManager();
//        $repo = $em->getRepository('LECMSBundle:MenuElement');
//        
//        $menu = $repo->find($id);
//        
//        if (null === $menu)
//        {
//            return $this->createNotFoundException('This menu doesn\'t exists');
//        }
//        $name = $menu->getName();
//        $em->remove($menu);
//        $em->flush();
//        
//        $this->get('session')->getFlashBag()->add(
//                'success',
//                'Menu '.$name.' Deleted :)'
//                );
//        $url = $this->generateUrl('sk_admin_menulist');
//        return $this->redirect($url);
//        
//    }
    
    public function listAction($entity,Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('LECMSBundle:MenuElement');
        
        $adminParams= $this->container->getParameter('le_admin.entities');
        $entityParams = $adminParams[$entity];
//        $repo->setDefaultLocale($locale);
        $entities = $repo->findRootMenu($request->getLocale());
//        die('sfsdf');
        return $this->render('LEAdminBundle:entity:list.html.twig',['entityName'=>$entity,'entities'=>$entities,'entityParams'=>$entityParams]);
        
    }
    
    public function editAction(Request $request)
    {
        $locale = $request->getLocale();
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('LECMSBundle:MenuElement');
        if (null !== $id)
        {
            $repo->setDefaultLocale($locale);
            $menu = $repo->findMenu($id);
        }
        else
        {
            $menu = new MenuElement();
        }
        
        $entitiesParams = $this->container->getParameter('le_admin.entities');
        $entities = [];
        foreach ($entitiesParams as $entityName => $entityParams)
        {
           
            if (array_key_exists( 'menuIntegrable',$entityParams) && $entityParams['menuIntegrable'])
            {
                $repo = $em->getRepository($entityParams['class']); 
                $subEntities = $repo->findAll();
                $entities[$entityName] = $subEntities;
            }
            
            
        }
        
        
        
        $form = $this->createForm(new MenuElementType($entities),$menu);
        
        
        
        $request = $this->get('request');
        
        if ($request->getMethod() == 'POST') 
        {
            $menuArray = json_decode($request->request->get('jsonmenu'));
            $children = $this->menuArrayToEntities($menuArray,$em,$menu,$locale);
            $menu->setName($request->request->get('name'));
            $menu->setTextId($request->request->get('textId'));
            $menu->setChildren($children);
            $em->persist($menu);
            
            $em->flush();
            
            $url = $this->generateUrl('le_admin_list_entity',['entity'=>'Menu']);
            
            
            return $this->redirect($url);
            
            
        }
        
        $currentMenuArray = $this->menuEntitiesToArray($menu);
        
        
        return $this->render('LEAdminBundle:Menu:menu-edit.html.twig',['entity'=>$menu,'menu'=>$menu,'form'=>$form->createView(),'currentMenuArray'=>$currentMenuArray,'entities'=>$entities]);
    }
    
    private function menuArrayToEntities($menuArray, \Doctrine\ORM\EntityManager $em,  MenuElement $parent = null,$locale)
    {

        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $repo = $em->getRepository('LECMSBundle:MenuElement');
        $repo->setDefaultLocale($locale);
        foreach ($menuArray as $menuElement)
        {
//            //dump($menuElement);
//            die();
            $element = null;
            if (isset($menuElement->elementId))
            {
                $element = $repo->find($menuElement->elementId);
                $element->setTranslatableLocale($locale);
                $em->refresh($element);
            }
            if (null === $element)
            {
                $element = new MenuElement();
            }
            $element->setName($menuElement->name);
            $element->setEntityId($menuElement->targetId);
            $element->setEntityClass($menuElement->entityClass);
            $element->setPosition($menuElement->position);
            if (isset($menuElement->children) && count($menuElement->children))
            {
                $element->setChildren($this->menuArrayToEntities($menuElement->children,$em,$element,$locale));
            }
            $collection->add($element);
            if (null !== $parent)
            {
                $element->setParent($parent);
            }
            $em->persist($element);
            
        }
        
        return $collection;
        
    }
    
    private function translateChildren($menu,$locale,$em)
    {
//        if ($locale == 'en') // JE sens que ce truc va rester des plombes comme ça, pas vrai ? 20/2/2015
//        {
//            $locale = 'en_US';
//        }
        
        foreach ($menu->getChildren() as $child)
        {
            $child->setTranslatableLocale($locale);
            $em->refresh($child);
            //dump($locale);
            //dump($child);
            $child = $this->translateChildren($child,$locale,$em);
        }
        return $menu;
    }
    
    private function deletePreviousChildren($em,MenuElement $menu)
    {
        foreach ($menu->getChildren() as $children)
        {
            $this->deletePreviousChildren($em, $children);
            $em->remove($children);
        }
    }
    
    private function menuEntitiesToArray(MenuElement $menu)
    {
        $toReturn = [];
        foreach ($menu->getChildren() as $children)
        {
            $toReturn[$children->getPosition()]['name']=$children->getName();
            $toReturn[$children->getPosition()]['targetId']=$children->getEntityId();
            $toReturn[$children->getPosition()]['elementId']=$children->getId();
            $toReturn[$children->getPosition()]['entityClass']=$children->getEntityClass();
            $toReturn[$children->getPosition()]['position']=$children->getPosition();
            if (count($children->getChildren()))
            {
                $toReturn[$children->getPosition()]['children']=$this->menuEntitiesToArray($children);
            }
        }
        
        return $toReturn;
    }
}
