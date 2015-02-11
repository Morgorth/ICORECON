<?php

namespace NRtworks\SubscriptionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NRtworksSubscriptionBundle extends Bundle
{ 
        public function getParent()
    {
        return 'FOSUserBundle';
    }
}


?>