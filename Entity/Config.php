<?php

namespace Mweb\AdminBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="mweb_config")
 */
class Config
{
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\GeneratedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * @var string
         * @ORM\Column(name="conf_name", type="string", length=255)
         */
        private $confName;

        /**
         * @var string
         * @ORM\Column(name="conf_value", type="string", length=255)
         */
        private $confValue;


        /**
         * Set confName
         *
         * @param string $confName
         *
         * @return Config
         */
        public function setConfName($confName)
        {
                $this->confName = $confName;

                return $this;
        }

        /**
         * Get confName
         *
         * @return string
         */
        public function getConfName()
        {
                return $this->confName;
        }

        /**
         * Set confValue
         *
         * @param string $confValue
         *
         * @return Config
         */
        public function setConfValue($confValue)
        {
                $this->confValue = $confValue;

                return $this;
        }

        /**
         * Get confValue
         *
         * @return string
         */
        public function getConfValue()
        {
                return $this->confValue;
        }
}
