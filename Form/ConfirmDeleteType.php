<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class ConfirmDeleteType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                
                $builder->add('confirm', CheckboxType::class,[
                        'label' => 'admin.edit.deleteConfirm'
                ]);
                
        }
    
        public function getName()
        {
                return 'mweb_confirm_delete';
        }
}

