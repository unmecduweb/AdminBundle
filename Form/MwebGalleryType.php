<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class MwebGalleryType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                parent::buildForm($builder, $options);
        }
        
        public function getParent()
        {
                return CollectionType::class;
        }
        
        
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
                return 'mw_gallery';
        }
}

