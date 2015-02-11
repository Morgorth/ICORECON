<?php

namespace NRtworks\MainUserInterfaceBundle\Controller;

//Symfony elements
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


class MainUserInterfaceController extends Controller
{
    
    
    // BASIC PAGE FUNCTION
    public function homeAction()
    {
       
       

        return $this->render(
            'NRtworksMainUserInterfaceBundle:MainUserInterface:home.html.twig',array('SectionIdentification' => 'MainUserInterface')
        );

    }
    
    
}


?>