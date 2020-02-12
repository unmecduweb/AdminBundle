# Mweb\AdminBundle

AdminBundle is a content manager system.

Exemple of configuration
````
mweb_admin:
    noIllu: niche
    fileManagerFolder: uploads
    menus:
        post:
            beautyName: Articles
    entities:
        post:
            beautyName: Articles
            menu: post
            showOnHome: true
            class: Mweb\CoreBundle\Entity\Post
            form: Mweb\CoreBundle\Form\PostType
            listProperties:
                name:
                    dataName: title
                    beautyName: Nom
                    type: string
                status:
                     dataName: status
                     beautyName: En ligne
                     type: boolean
                category:
                     dataName: category
                     beautyName: Catégorie
                     type: string
                tag:
                     dataName: tag
                     beautyName: Tag
                     type: string
     
````
Ajouter dans le fichier config la resolve target entities avec la class user que vous souhaitez utilisez
````
# Doctrine Configuration
doctrine:
    orm:
        resolve_target_entities:
            Mweb\AdminBundle\Entity\UserInterface: Mweb\AdminBundle\Entity\User
````

Require : <br />
    - FosUserBundle<br />
    - LexikTranslationBundle<br />
    - LiipImagineBundle
    
    
###Features : 
Cacher un champ dans l'édition pour les non super-admin
  
   ````
    Exemple :
     ->add('color', TextType::class, [
            'label' => 'admin.museum.color',
            'attr' => array('data-role' => 'mw-superadmin'),
            'required' => false
    ])
    //Ne pas oublier le required false ou les admin ne pourront pas créer un nouveau document.

 ````

    
Small tinymce 

````    
    ->add('description', TextareaType::class, [
                                    'label' => 'admin.page.desc',
                                    'required' => false,
                                    'attr' => array('class' => 'tinymce-small ou tinymce')
                            ])
````            