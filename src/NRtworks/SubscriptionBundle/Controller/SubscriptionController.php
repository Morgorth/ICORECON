<?php

namespace NRtworks\SubscriptionBundle\Controller;

//form loadings
use NRtworks\SubscriptionBundle\Form\NewCustomer;
use NRtworks\SubscriptionBundle\Form\NewUser;

//entity loadings
use NRtworks\SubscriptionBundle\Entity\Customer;
use NRtworks\SubscriptionBundle\Entity\icousers;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use FOS\UserBundle\Controller\RegistrationController as FOSRegistrationController;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

//$session = new Session();
//$session->start();

// the following controller is used to display the basic page for subscription
class SubscriptionController extends Controller
{
    
    // BASIC PAGE FUNCTION
    public function indexAction()
    {
        
        $customer = new Customer();
        
        $form = $this->createForm(new NewCustomer(), new Customer());
        
        return $this->render(
            'NRtworksSubscriptionBundle:Subscription:subscription.html.twig',array('SectionIdentification' => 'Customer_Subscription','form'=>$form->createView())
        );

    }
    
    // the following controller is used to treat the subscription form
    public function SubscriptionHandlingAction(Request $request)
    {
        
            if ($request->isMethod('POST')) 
            {
                
                $form = $this->createForm(new NewCustomer(), new Customer());
                $form->bind($request);

                if ($form->isValid()) 
                {
                    
                    // get the form data 
                    $newcustomer = $form->getData();                    
                   
                    //get the date and set it in the entity
                    $datecreation = new \DateTime(date('d-m-Y'));                     
                    $newcustomer->setdatecreation($datecreation);
                    
                    
                    echo $newcustomer->getname();
                    //persist the data
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($newcustomer);
                    $em->flush();
                    
                    //set a unique session to secure the next page
                    $uniquesessionkey = "RhygQd112dsq5fv1zqJHF21g5s4ZVAN";
                    $session = $this->getRequest()->getSession();
                    $session->set('ICOreconciliatorUniqueSessionSubscription', $uniquesessionkey);
                    $session->set('ICOReconciliatorLastCustomerCreated', $newcustomer->getid());
                    
                   
                             
                    //prepare the form for the customer's admin
                    //$customeradmin = new icousers();
                    //$formfirstuser = $this->createForm(new NewUser(), $customeradmin);
                    
                    
                    $twig_options = array();                    
                    $twig_options["customers_created"]=$newcustomer->getId();
                    $twig_options['SectionIdentification'] = 'Customer_Subscription';
                    //$twig_options['form']=$formfirstuser->createview();
                    
                    
                    return $this->render('NRtworksSubscriptionBundle:Subscription:subscription_success.html.twig',$twig_options);  
                    
                }
                
                // CASE: the form returned is invalid
             return $this->render('NRtworksSubscriptionBundle:Subscription:subscription_failed.html.twig',array('SectionIdentification' => 'Customer_Subscription'));
             
            }
            
            // if the controller is called without post data, redirect to basic page
          return $this->render('NRtworksSubscriptionBundle:Subscription:subscription_failed.html.twig',array('SectionIdentification' => 'Customer_Subscription'));
    }
    
    
}


?>