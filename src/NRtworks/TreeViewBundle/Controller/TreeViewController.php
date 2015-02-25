<?php

namespace NRtworks\TreeViewBundle\Controller;

//model


//entity loadings
use NRtworks\SubscriptionBundle\Entity\Customer;

//form loading
use NRtworks\TreeViewBundle\Form\AccountFasteditForm;

//Symfony elements
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


$highest = 0;


class TreeViewController extends Controller
{
    
    
    // BASIC PAGE FUNCTION
    public function indexAction()
    {
       $COA = $this->getDoctrine()->getRepository('NRtworksTreeViewBundle:Account')->findById(1);
       

        return $this->render(
            'NRtworksTreeViewBundle:TreeView:COA.html.twig',array('COA'=>$COA)
        );

    }
    
    public function COAEditControllerAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        
        // let's get the tree
        $repo = $em->getRepository('NRtworksTreeViewBundle:Accounttree');
        $arrayTree = $repo->childrenHierarchy();          
        
        //let's get a default object, to be used when we add a new elements
        $temp = new Accounttree();
        $default_array = $temp->default_object();
       
        //let's get an edit form, to edit an element inside the tree       
        $form = $this->createForm(new AccountFasteditForm(),new Accounttree());
                
       return $this->render(
            'NRtworksTreeViewBundle:TreeView:COA2.html.twig',array('SectionIdentification'=>'TreeEditing','TreeHierarchy'=>$arrayTree[get_initial_tree_element($arrayTree)],'highestID' => get_highest_id_from_tree($arrayTree)+1,'default_object'=>$default_array,'edit_form'=>$form->createView())
        ); 
        
    }
    
    public function treeReloadAction()
    {
        
        $em = $this->getDoctrine()->getManager();
        
        // let's get the tree
        $repo = $em->getRepository('NRtworksTreeViewBundle:Accounttree');
        $arrayTree = $repo->childrenHierarchy();          
        
       return $this->render(
            'NRtworksTreeViewBundle:macro:tree_reload.html.twig',array('SectionIdentification'=>'TreeEditing','TreeHierarchy'=>$arrayTree[get_initial_tree_element($arrayTree)])
        ); 
        
    }
    
    public function treeEditSaveChangesAction(Request $request)
    {
        Try
        {                          
            $em = $this->getDoctrine()->getManager();
            
            $customer = new Customer();
            $customer = $em->getRepository('NRtworksSubscriptionBundle:Customer')->findOneById(1);
                      
            //getting the array from the web page
            $php_format = $request->request->get('table');
            
            // index 0 is ID
            // index 1 is temporary parent id
            // index 2 is actions performed
            
            //first, a loop to create the new accounts
            foreach($php_format as $element)
            {
                if($element[2]=="new")
                {
                        
                    //let's save the id created on the web page so we can update its children
                    $original_id = $element[0];
                    
                    //now let's save it
                    $to_create = new Accounttree();
                    $to_create->setCode($element[3]);
                    $to_create->setName($element[4]);
                    $to_create->setSense($element[5]);
                    $to_create->setCustomer($customer);                                                                               
                    $em->persist($to_create);
                    $em->flush();
                    echo "1 account created";
                    // and change its id in the array received from the web page
                    $element[0] = $to_create->getId();
                    $new_id = $element[0];
                    
                    // and above all, let's look if it had children
                    foreach($php_format as $element_deep)
                    {
                        if($element_deep[1]==$original_id)
                        {
                            $element_deep[1] = $new_id;
                        }
                        
                    }                    
                } 
            }
                                    
            //getting all the objects at once in order to edit
            $repo = $em->getRepository('NRtworksTreeViewBundle:Accounttree');
            $arrayTree = $repo->findByCustomer($customer->getid()); 
            $array_New_Accounts = Array();
            
            
            // a loop through all the elements of the array, checking actions to do
            foreach($php_format as $element)
            {
                if($element[2]!="delete")
                {    
                    $index = get_account_from_id($arrayTree, $element[0]);
                    
                    $temp = $arrayTree[$index];                    
                    $temp->setCode($element[3]);
                    $temp->setName($element[4]);
                    $temp->setSense($element[5]);
                    
                    //check if the account was moved
                    if($element[1]!="")
                    {
                       $temp->setParent($arrayTree[get_account_from_id($arrayTree, $element[1])]);                         
                    }
                    
                    $em->merge($temp);
                }
                elseif($element[2]=="delete")
                {
                   $to_delete = $arrayTree[get_account_from_id($arrayTree, $element[0])];
                   $em->remove($to_delete);
                    
                }               
            }
                        
            $em->flush();
            
            
            
            
            $response = array("code" => 100, "success" => true);
            return new Response(json_encode($response));
            
        }
        Catch (Exception $e)
        {
            
            return new Response($e->getMessage());
            
        }

        
    }    

    
    // ------------------------------------ treeEdit with angular-ui-tree -------------------------------
    
    // home controller
    public function treeViewAction($dimension)
    {
       $session = new Session();   
     
       if($dimension == "Account") { $session->set('NRtworks_DimensionIdentification',array("ChartOfAccounts"=>1)); }else {$session->remove("NRtworks_DimensionIdentification");}
       
       $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
       
       $parametersArray = [];
       $parametersArray["fields"] = $setUpForDimension->getFieldsNameToEdit($dimension);
       $parametersArray["dimension"] = $dimension;
       
       
       
       $session->set("parameters", $parametersArray);
        
       return $this->render(
            'NRtworksTreeViewBundle:treeView:home.html.twig',array('SectionIdentification'=>'TreeView','dimension'=>$dimension)
        ); 
        
    }
    
    
    
    // ----------------------- BELOW THE ROUTES USED By AngularJS to show the templates -------------------- //
    
    //_treeView_angularRoute_getTree
    public function treeViewEditGetTreeAction()
    {

          return $this->render(
            'NRtworksTreeViewBundle:treeView:treeViewTemplate.html.twig', array('SectionIdentification' => 'TreeView'));                    

    }      

    //_treeView_angularRoute_fullEdit
    public function treeEdit_fullEditAction()
    {
        $session = new Session();
        $parameters =  $session->get("parameters");
        
          return $this->render(
            'NRtworksTreeViewBundle:treeView:treeEdit_fullEdit.html.twig', array('SectionIdentification' => 'TreeViewv2','parameters'=>$parameters));                    

    }    

    
    // ------------- -------------------------------- RESTful API controllers   -------------------------------- -------------------
    
    // this one gets the user list from the database
    public function _API_getTreeAction(Request $request)
    {
        if($this->getUser())
        {                        
            
            //identifying user & customer
            $user = $this->getUser();
            $customer = new Customer();
            $customer = $user->getCustomer();            
            $customerID = $customer->getId();  
                                    
            $arrayFunctions = $this->get('arrayFunctions');
            $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
            $treeBuilder = $this->get('TreeView.treeBuilder');
            $serializer = $this->get('jms_serializer'); 
            
            $em = $this->getDoctrine()->getManager();
 
            //get the dimension for which we want to see the tree 
            $dimension = $request->getContent();
            $dimension = json_decode($dimension,true);
            $dimension = $dimension["dimensionpassed"];

            //let's get the list of the relevant elements
            $allaccounts = $setUpForDimension->getFlatList($dimension,$customer); 
            //we transform the flat list of accounts to a tree (with a recursion)  
            $arrayTree = $treeBuilder->buildBasicTree($allaccounts);
        
            //we need the highest ID of this tree to pass it to the front (when a new account is created is doesnt take an existing ID
            $highest = $arrayFunctions->arrayGetHighestIdFromTree($arrayTree,"nodes");

            // and we need a default object to allow the user to             
            $defaultObject = $setUpForDimension->getDefaultObject($dimension,$highest);
            
            $defaultTrueObject = $setUpForDimension->getDefaultTrueObject($dimension);
            $fieldParameters = $defaultTrueObject->getFieldsParameters();
            
            $fieldParameters = $setUpForDimension->buildSelectElements($dimension,$fieldParameters,$customer);
            
            // and build the parameters array containing the tree + other needed elements
            $parametersArray = [];
            $parametersArray["tree"] = $arrayTree;
            $parametersArray["dimension"] = $dimension;
            $parametersArray["fieldsSettings"] = $fieldParameters;
            $parametersArray["defaultObject"] = $defaultObject;
            //$parametersArray["highestID"] = $highest; 
            
            // serializing the array                                                                       
            $topass = $serializer->serialize($parametersArray, 'json');
            
            
            return new Response($topass);
        }
        else
        {


        }                                        
    }
    
    public function _API_saveTreeAction(Request $request)
    {
  
        if($this->getUser())
        {
            $em = $this->getDoctrine()->getManager();
            
            //identifying user & customer
            $user = $this->getUser();
            $customer = new Customer();
            $customer = $user->getCustomer();            
            $customerID = $customer->getId(); 

            //let's get the posted data
            $allContent = $request->getContent();
            $allContent = json_decode($allContent,true);
            $postContent = json_decode($allContent["postContent"],true);
            //here they are
            $dimension = $postContent[0]; 
            $tree = $postContent[1];
            //var_dump($tree);
            
            //let's get some utils functions
            $arrayFunctions = $this->get('arrayFunctions');
            $arrayFunctionsTreeSaving = $this->get('arrayFunctionsTreeSaving');
            $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
                                 
            // let's rebuild the tree so that each element has now a parent_id property
            $arrayFunctionsTreeSaving->rebuildTreeWithParentId($tree,"nodes");
            //var_dump($tree);                        

            $resultFlatTree = $arrayFunctionsTreeSaving->rebuildTreeAsFlatArray($tree[0],"nodes");
            //\Doctrine\Common\Util\Debug::dump($resultFlatTree);
            
            $originalFlatTree = $setUpForDimension->getFlatList($dimension,$customer); 
            //\Doctrine\Common\Util\Debug::dump($originalFlatTree);
            
            $failed_lines = [];
            $updated_lines = [];
            
            // STEP 1: check if there are some new accounts 
            // if yes we create them and get the new ID
            // Check if they have children and then change their parent ID
            //var_dump($resultFlatTree);
            foreach($resultFlatTree as &$element)
            {
                if(isset($element["action"]) && $element["action"] == "new")
                {
                    try
                    {                
                        //echo "new";
                        if($element['name'] =="throwError"){ throw new \Exception;}
                        //let's save the original ID so we can find the children
                        $originalID = $element["id"];
                        //also let's find its parent so we can affect it directly
                       
                        $parent = $originalFlatTree[$arrayFunctions->findIndexOfAPropertyByIdInArrayOfObject($originalFlatTree,$element["parentid"])];
                        //\Doctrine\Common\Util\Debug::dump($parent);
                        //now we get the object
                        $newObject = $setUpForDimension->createAnObject($customer,$dimension,$element,$parent);
                        //\Doctrine\Common\Util\Debug::dump($newObject); 
                        //$repo->persistAsFirstChildOf($newObject,$parent);
                        $em->persist($newObject);
                        $em->flush();
                        //\Doctrine\Common\Util\Debug::dump($newObject);                                            
                        $element['id'] = $newObject->getId();

                        //and let's not forget to change the parent_id of its children
                        $arrayFunctions->arrayChangingValues($resultFlatTree,"parentid",$element['id'],$originalID);

                        //and now we change the tree of object with this new element created
                        array_push($originalFlatTree,$newObject);
                        
                        //and save the line as updated
                        array_push($updated_lines,$element);
                    }
                    catch(\Exception $e)
                    {
                        echo $e;
                        array_push($failed_lines,$element);
                    }

                                                                                 
                }                
            }
            unset($element);
            //\Doctrine\Common\Util\Debug::dump($originalFlatTree);
    
            //STEP 2:  ok let's check the accounts we have to do
            foreach($resultFlatTree as $element)
            {
                //echo "second loop";
                //let's check that we didn't have a new element that was failed in step 1 otherwise it will generate an error
                if(!$arrayFunctions->elementIsInArray($failed_lines,$element))
                {
                    //let's get the object equivalent to the element
                    //echo "foreach start:";
                    //echo $element["id"];
                    $currentObject = $originalFlatTree[$arrayFunctions->findIndexOfAPropertyByIdInArrayOfObject($originalFlatTree,$element["id"])];
                    
                    //and let's get the object of its parent (as it was modified in the front, not in the DB)
                    $newParent = $originalFlatTree[$arrayFunctions->findIndexOfAPropertyByIdInArrayOfObject($originalFlatTree,$element["parentid"])];

                    if(isset($element["to_delete"]) && $element["to_delete"] == "yes")
                    {

                        echo "deleting";
                        try
                        {    
                            if($element["name"] == "throwError"){ throw new \Exception;}
                            $em->remove($currentObject);
                            array_push($updated_lines,$element);
                        }
                        catch(\Exception $e)
                        {
                            array_push($failed_lines,$element);
                        }
                    }
                    elseif(isset($element["action"]) && $element["action"] == "modified")
                    {
                       //echo "modifying";
                       $currentObject = $setUpForDimension->updateAnObject($currentObject,$element,$dimension); 

                       //var_dump($currentObject);


                    }
                    else
                    {
                        //echo "nothing to do";
                        //no specific action identified:                                    
                    }

                    //let's check if the account was moved and then move it
                    //\Doctrine\Common\Util\Debug::dump($currentObject);
                    //echo $currentObject->getName();
                    //echo $newParent->getName();
                    if($currentObject->getParent()->getId() != $newParent->getId())
                    {
                        //echo "moving";
                        //echo $newParent->getName();
                        $currentObject->setParent($newParent);                        
                    }  

                    try 
                    {
                        if($element["name"] == "throwError"){ throw new \Exception;}
                        $em->merge($currentObject);
                        array_push($updated_lines,$element);
                    }
                    catch(\Exception $e)
                    {
                        array_push($failed_lines,$element);
                    }
                }
                
            }           
            
            $em->flush();
            
            if(count($failed_lines)>0)
            {
                 $res = json_encode(["status"=>"failed","msg"=>"Some elements failed to be saved","failed"=>$failed_lines,"successed"=>$updated_lines]);                     
                 return new Response($res); 
            }
            else
            {
                $res = json_encode(["status"=>"ok","msg"=>"All changes are saved","failed"=>$failed_lines,"successed"=>$updated_lines]);                     
                return new Response($res); 
            }
            
            
            
            
        } 
        else             
        {
            //un-identified case of action to apply to the element
        }
        
    }
    
    
//end of class controller    
}


?>