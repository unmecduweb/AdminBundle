<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EditUserType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                
                $builder->remove('plainPassword');
                
        }
        
        public function getParent()
        {
                return 'fos_user_registration';
        }
        
        public function getName()
        {
                return 'mweb_user_change_password';
        }
}

