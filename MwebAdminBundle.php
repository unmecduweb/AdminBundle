<?php

namespace Mweb\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MwebAdminBundle extends Bundle
{

        public function getParent()
        {
                return 'FOSUserBundle';
        }

}
