<?php

    namespace NRtworks\SubscriptionBundle\Controller;

    //FOS use    
    use FOS\UserBundle\FOSUserEvents;
    use FOS\UserBundle\Event\FormEvent;
    use FOS\UserBundle\Event\GetResponseUserEvent;
    use FOS\UserBundle\Event\UserEvent;
    use FOS\UserBundle\Event\FilterUserResponseEvent;
    use Symfony\Component\DependencyInjection\ContainerAware;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
    use Symfony\Component\Security\Core\Exception\AccessDeniedException;
    use FOS\UserBundle\Model\UserInterface;
    use FOS\UserBundle\Controller\RegistrationController as BaseController;
    
    //NRtworks specifics
    use NRtworks\SubscriptionBundle\Entity\Customer; 
    
    //Symfony specifics
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Session\Session;

    class RegistrationController extends BaseController
    {
        public function RegisterAdminUserAction(Request $request)
        {
            $em = $this->container->get('doctrine')->getManager();
            $customer = new Customer();
            $customer = $em->getRepository('NRtworksSubscriptionBundle:Customer')->findOneById(1);
            $type = $em->getRepository('NRtworksSubscriptionBundle:Usertype')->findOneById(5);
            /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
            $formFactory = $this->container->get('fos_user.registration.form.factory');
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->container->get('fos_user.user_manager');
            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->container->get('event_dispatcher');

            $user = $userManager->createUser();
            $user->setEnabled(true);

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

            if (null !== $event->getResponse())
            {
                return $event->getResponse();
            }

            $form = $formFactory->createForm();
            $form->setData($user);


            if ('POST' === $request->getMethod()) 
             {
                $form->bind($request);

                //form received
                if ($form->isValid()) 
                {
                    $event = new FormEvent($form, $request);
                    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                    $user->setType($type);
                    $user->addRole('ROLE_ACCOUNT_ADMIN');
                    $user->setCustomer($customer);                    
                    $userManager->updateUser($user);


                    if (null === $response = $event->getResponse()) 
                    {
                        $url = $this->container->get('router')->generate('fos_user_registration_confirmed');
                        $response = new RedirectResponse($url);
                    }

                    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                    return $response;
                }
            }
            else
            {    
                return $this->render('NRtworksSubscriptionBundle:RegisterAdmin:registration.html.twig', array(
                'SectionIdentification'=>'Customer_Subscription','RegisterAdminForm' => $form->createView(),
                ));
                
            }
        }
    }


?>
