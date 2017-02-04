<?php

namespace Mweb\AdminBundle\Form;

use Symfony\Component\Form\AbstractType as SfAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AbstractType extends SfAbstractType
{
        
        /**
         * @param FormBuilderInterface $builder
         * @param array $options
         */
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
                
                
                $gotoChoices['seeList'] = "admin.edit.goto.seeList";
                $gotoChoices['stayHere'] = "admin.edit.goto.stayHere";
                foreach ($options['attr']['locales'] as $locale) {
                        $gotoChoices['otherLanguages-' . $locale] = 'admin.edit.goto.otherLanguages.' . $locale;
                }
                
                $builder
                        ->add('status', ChoiceType::class, [
                                'label' => 'admin.edit.status',
                                'expanded'=>true,
                                'choices' => array(
                                        '1' => "admin.edit.statusOn",
                                        '0' => "admin.edit.statusOff"
                                )
                        
                        ])
                        ->add('goTo', ChoiceType::class, [
                                'label' => 'admin.edit.goto',
                                'mapped' => false,
                                'choices' => $gotoChoices
                        ]);
        }
        
        
        /**
         * @param OptionsResolverInterface $resolver
         */
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
                $resolver->setDefaults(array(
                        'translation_domain' => 'mweb'
                ));
        }
        
        
}
