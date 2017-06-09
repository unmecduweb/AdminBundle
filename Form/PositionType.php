<?php

namespace Mweb\AdminBundle\Form;

use Mweb\CoreBundle\Entity\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PositionType extends AbstractType
{
       
       
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                
                $builder->add('id', HiddenType::class);
                $builder->add('position', HiddenType::class);
        }
        
        
        
        /**
         * @param OptionsResolverInterface $resolver
         */
        public function configureOptions(OptionsResolver $optionsResolver)
        {
                $optionsResolver->setDefaults(array(
                        'translation_domain' => 'mweb'
                ));
        }
        
        
}
