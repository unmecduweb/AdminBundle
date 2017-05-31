<?php
// src/AppBundle/Entity/User.php

namespace Mweb\AdminBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="mweb_user")
 */
class User extends BaseUser
{
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;
        
        /**
         * @var string
         * @Assert\Regex(
         *     pattern="^[A-Za-z0-9\.]+@[A-Za-z]+\.[A-Za-z]+$^",
         *     message="Email incorrecte"
         * )
         */
        protected $email;
        
        
        /**
         * @var string
         * @Assert\Regex(
         *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
         *  message="Mot de passe doit contenir au minimum 7 caractères et contenir au mois un chiffre, une miniscule et une majuscule"
         * )
         */
        protected $plainPassword;

        public function __construct()
        {
                parent::__construct();
                // your own logic
        }
}
