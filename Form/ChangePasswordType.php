<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                $builder->add('name');
        }
        
        public function getParent()
        {
                return 'fos_user_change_password';
        }
        
        public function getName()
        {
                return 'mweb_user_change_password';
        }
}

