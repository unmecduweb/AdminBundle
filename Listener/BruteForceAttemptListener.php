<?php
namespace Mweb\AdminBundle\Listener;

use Anyx\LoginGateBundle\Event\BruteForceAttemptEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
/**
 * This is to show admin menu on each views when user is granted admin
 *
 * @author jona
 */
class BruteForceAttemptListener
{
        
        
        public function onBruteForceAttempt(BruteForceAttemptEvent $event)
        {
        
                throw new HttpException(
                        429,
                        'Too many failed authentication, please come back in an hour from now.'
                );
        }
}
