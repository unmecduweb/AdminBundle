<?php
namespace Mweb\AdminBundle\Entity\Translation;


use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractTranslation;

/**
 * @ORM\Table(name="mweb_translations_entity", indexes={
 *      @ORM\Index(name="entity_translation_idx", columns={"locale", "object_class", "field", "foreign_key"})
 * })
 * @ORM\Entity(repositoryClass="Mweb\AdminBundle\Entity\Translation\TranslatableRepository")
 */

class EntityTranslation extends AbstractTranslation
{
    //put your code here
    
}
