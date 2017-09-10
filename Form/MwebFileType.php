<?php

namespace Mweb\AdminBundle\Form;

use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class MwebFileType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                parent::buildForm($builder, $options);
        }
        
        public function getParent()
        {
                return HiddenType::class;
        }
        
        
        /**
         * {@inheritdoc}
         */
        public function getBlockPrefix()
        {
                return 'mw_file';
        }
}

