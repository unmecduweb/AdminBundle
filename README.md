# Mweb\AdminBundle

AdminBundle is a content manager system.

Exemple of configuration

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
     
Require : <br />
    - FosUserBundle<br />
    - LexikTranslationBundle<br />
    - LiipImagineBundle