<?php

    namespace NRtworks\SubscriptionBundle\Controller;
   
    //NRtworks specifics
    use NRtworks\SubscriptionBundle\Entity\Customer; 
    use NRtworks\SubscriptionBundle\Entity\icousers;
    use NRtworks\SubscriptionBundle\Form\customerAdminAddNewUser;
    

    
    
    //Symfony specifics
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Session\Session;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    function getRankFromIdObject($array,$tofind)
    {
        $i = 0;
        foreach($array as $object)
        {
            if($object->getId() == $tofind)
            {
                return $i;
            }
            $i++;

        }
        return false;
    }
    
    
    class UserManagerController extends Controller
    {
        public function HomeAction(Request $request)
        {
                 
                return $this->render(
                'NRtworksSubscriptionBundle:UserManager:home.html.twig', array('SectionIdentification' => 'UserManager'));
            
        }
        
        public function ListAction(Request $request)
        {
 
              return $this->render(
                'NRtworksSubscriptionBundle:UserManager:user_list.html.twig', array('SectionIdentification' => 'UserManager'));                    
            
        }
        public function addNewUserAction(Request $request)
        {
 
              return $this->render(
                'NRtworksSubscriptionBundle:UserManager:add_new.html.twig', array('SectionIdentification' => 'UserManager'));                    
            
        }
        
        public function renderFormAddNewUserAction()
        {
                              
            $form = $this->createForm(new customerAdminAddNewUser(), new icousers(),array('action'=> $this->generateUrl('_UserManager_formHandling_addNewUser')));
            
            return $this->container->get('templating')->renderResponse('NRtworksSubscriptionBundle:RegisterAdmin:add_a_user.html.twig', array(
                'add_a_user' => $form->createView(),
                ));
            
        }
        
        
        // ------------- -------------------------------- RESTful API controllers   -------------------------------- -------------------
        
        // this one gets the user list from the database
        public function getUserListAction()
        {
            if($this->getUser())
            {
                //identifying user & customer
                $user = $this->getUser();
                $customer = new Customer();

                $customer = $user->getCustomer();            
                $customerID = $customer->getId();
                
                                
                //getting all the users linked to the customer
                $em = $this->getDoctrine()->getManager();
                $user_array = $em->getRepository('NRtworksSubscriptionBundle:icousers')->findByCustomer($customerID);
                /*\Doctrine\Common\Util\Debug::dump($user_array);*/
                              
                // serializing the array
                $serializer = $this->get('jms_serializer');
                
                $user_array = $serializer->serialize($user_array, 'json');
                                
                return new Response($user_array);
            }
            else
            {
                 
                              
            }                                        
        }
        //this one gets the usertype list from the database
        public function getUserTypeListAction()
        {
            if($this->getUser())
            {

                $em = $this->getDoctrine()->getManager();
                $userTypeList = $em->getRepository('NRtworksSubscriptionBundle:Usertype')->findAll();
                              
                // serializing the array
                $serializer = $this->get('jms_serializer');
                $userTypeList = $serializer->serialize($userTypeList, 'json');               
                return new Response($userTypeList);
            }
            else
            {
                 
                              
            }                                        
        }
        
        // ----------------------------- SAVE INTO DB CONTROLLER -----------------------------
        public function updateUserListAction(Request $request)
        {
            //getting some external components
            $userManager = $this->container->get('fos_user.user_manager');
            $serializer = $this->get('jms_serializer');
            
            $updated_users =  Array();
            $failed_users = Array();
            
            if($this->getUser())
            {
                //getting the userlist
                $customer = new Customer(); 
                $customer = $this->getUser()->getCustomer();            
                $customerID = $customer->getId();                                                
                //getting all the users linked to the customer
                $em = $this->getDoctrine()->getManager();
                $user_array = $em->getRepository('NRtworksSubscriptionBundle:icousers')->findByCustomer($customerID); 
                $userTypeList = $em->getRepository('NRtworksSubscriptionBundle:Usertype')->findAll();
                
                if ('POST' === $request->getMethod()) 
                {
                    $raw_data = $request->getContent();   
                    $raw_data = json_decode($raw_data,true);  
                    
                    foreach($raw_data as $user)
                    {   
                        //get the type of the user as a PHP object
                        $comleteType = $userTypeList[getRankFromIdObject($userTypeList,$user['type']['id'])];                       
                        
                        if($user['id'] == "new")
                        {
                            
                            $new_user = $userManager->createUser();
                            $new_user->setEnabled(false);
                            $new_user->setUsername($user['username']);
                            $new_user->setEmail($user['email']);
                            $new_user->setCustomer($customer);
                            $new_user->setType($comleteType);                                                                                    
                            $new_user->addRole($comleteType->getSymfonyRole());
                            $new_user->setPlainPassword($user['email']);
                            
                            try
                            {
                                if($user['username'] =="throwError"){ throw new \Exception;}
                                $userManager->updateUser($new_user);
                                array_push($updated_users,$user);
                            }
                            catch(\Exception $e)
                            {
                                array_push($failed_users,$user);
                            }
                            
                            
                        }
                        elseif(isset($user['to_delete']))
                        {
                            $complete_user = $user_array[getRankFromIdObject($user_array,$user['id'])];
                            try
                            {
                                $userManager->deleteUser($complete_user);
                                array_push($updated_users,$user);
                            }
                            catch(\Exception $e)
                            {
                                array_push($failed_users,$user);
                            }
                            
                            
                        }
                        else
                        {
                            $complete_user = $user_array[getRankFromIdObject($user_array,$user['id'])];
                            $complete_user->setUsername($user['username']);                           
                            $complete_user->setType($comleteType);
                            
                            $roles = $complete_user->getRoles();
                            foreach($roles as $role)
                            {
                                $complete_user->removeRole($role);                                
                            }
                            $complete_user->addRole($comleteType->getSymfonyRole());
                            
                            try
                            {
                                if($user['username'] =="throwError"){ throw new \Exception;}
                                $userManager->updateUser($complete_user);
                                array_push($updated_users,$user);
                            }
                            catch(\Exception $e)
                            {
                                array_push($failed_users,$user);
                            }
                            
                        }                          
                    }
                    
                   if(count($failed_users)>0)
                   {
                        $res = json_encode(["status"=>"failed","msg"=>"Some users failed to be saved","failed"=>$failed_users,"successed"=>$updated_users]);                     
                        return new Response($res); 
                   }
                   else
                   {
                       $res = json_encode(["status"=>"ok","msg"=>"All changes are saved","failed"=>$failed_users,"successed"=>$updated_users]);                     
                       return new Response($res); 
                   }
                    
                }
                else
                {
                    //no post data
                } 
              }
              else
              {
                  //not identified
              }                                 
            
        }
        
        // ------------ USER ENTERED DATA CHECKING FUNTIONS ------------
        
        // check if a username is already in use for this customer
        public function checkIfUsernameExistsAction(Request $request)
        {
            if($this->getUser())
            {
                //identifying user & customer
                $user = $this->getUser();
                $customer = new Customer();

                $customer = $user->getCustomer();            
                $customerID = $customer->getId();
           
                if ('POST' === $request->getMethod()) 
                {
                    $usernameToCheck = $request->getContent();
                    $usernameToCheck = json_decode($usernameToCheck)->value;
                    
                    //getting all the users linked to the customer
                    $em = $this->getDoctrine()->getManager();
                    if($em->getRepository('NRtworksSubscriptionBundle:icousers')->findBy(array('username'=>$usernameToCheck,'customer'=>$customerID)))
                    {
                        $res = ['status'=>"user found",'msg'=>"This user already exists, please change"];
                        
                    }
                    else
                    {
                        $res = ['status'=>"ok",'msg'=>''];
                    }
                    
                    $res = json_encode($res);
                      
                    return new Response($res);
                }  
            }
        }
        
        // not used anymore //
        public function setNewUserAction(Request $request)
        {
            if($this->getUser())
            {
                //identifying user & customer
                $user = $this->getUser();
                $customer = new Customer();

                $customer = $user->getCustomer();            
                $customerID = $customer->getId();
                
                /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
                $userManager = $this->container->get('fos_user.user_manager');
                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->container->get('event_dispatcher');

                $user = $userManager->createUser();
                $user->setEnabled(false);

                $form = $this->createForm(new customerAdminAddNewUser(), new icousers(),array('action'=> $this->generateUrl('_UserManager_formHandling_addNewUser')));
                
                if ('POST' === $request->getMethod()) 
                {
                    $form->bind($request);

                    //form received
                    if ($form->isValid()) 
                    {
                        $user = $form->getData();
                        
                        $user->addRole('ROLE_BASIC_USER');
                        $user->setCustomer($customer);                    
                        $userManager->updateUser($user);

                        return new Response("ok");
                    }
                }
                else
                {    
                  return new Response("No form to handle");
                }

            }
            return new Response("SECURITY ERROR");
        }//controller ends
    }


?>
