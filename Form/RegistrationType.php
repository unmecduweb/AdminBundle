<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                
        }
        
        public function getParent()
        {
                return 'FOS\UserBundle\Form\Type\RegistrationFormType';
        }
        
        public function getName()
        {
                return 'mweb_user_change_password';
        }
}

