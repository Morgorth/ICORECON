<?php

namespace NRtworks\FlatViewBundle\Controller;

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


class FlatViewController extends Controller
{
    
    
    // ------------------------------------ FlatView with angular xeditable -------------------------------
    
    // home controller
    public function homeAction($dimension)
    {
       $session = new Session();   
     
       
      // $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
       
       $parametersArray = [];
       //$parametersArray["fields"] = $setUpForDimension->getFieldsNameToEdit($dimension);
       $parametersArray["dimension"] = $dimension;
       //$parametersArray["BDSelector"] = ["chartofaccount"=>1];
       
       $session->set("parameters", $parametersArray);
        
       return $this->render(
            'NRtworksFlatViewBundle:FlatView:home.html.twig',array('SectionIdentification'=>'FlatView','dimension'=>$dimension)
        ); 
        
    }
    
    // controller that gives the address of the template to angular
    public function elementListAction()
    {
  
      return $this->render(
        'NRtworksFlatViewBundle:FlatView:elementList.html.twig', array('SectionIdentification' => 'FlatView'));
        
    }    
    
    public function getDataAction(Request $request)
    {
        $API = $this->get('GlobalUtilsFunctions.APIGetData');
        $serializer = $this->get('jms_serializer'); 
        $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
        $treeBuilder = $this->get('TreeView.treeBuilder');
        $arrayFunctions = $this->get('arrayFunctions');
        
        //get the dimension for which we want to see the tree 
        $dimension = $request->getContent();
        $dimension = json_decode($dimension,true);
        $dimension = $dimension["dimensionpassed"];

        $user = $this->getUser();
        $customer = new Customer();
        $customer = $user->getCustomer();  
        $array = ["customer"=>$customer];     
        
        //let's get all the data to be passed to the front
        $elementList = $API->requestSimpleByArray("BusinessDimension",$dimension,$array);
        $elementsAsArray = $treeBuilder->rebuildObjectsAsArrays($elementList);
        $finalarray = $arrayFunctions->rebuildANonAssociativeArray($elementsAsArray);
        
        $defaultTrueObject = $setUpForDimension->getDefaultTrueObject($dimension,end($finalarray)[0]+1);
        $fieldParameters = $defaultTrueObject->getFieldsParameters();
        $defaultObject = $arrayFunctions->rebuildANonAssociativeArray($defaultTrueObject->getDefaultObject());
        $nbFields = count($fieldParameters);
        //\Doctrine\Common\Util\Debug::dump($elementsAsArray);
        
        $parametersArray = [];
        $parametersArray["dimension"]= $dimension;
        $parametersArray["elementList"] = $finalarray;
        $parametersArray["fieldsParameters"] = $fieldParameters;
        $parametersArray["defaultObject"] = $defaultObject;
        $parametersArray["nbFields"] = $nbFields;
 
         // serializing the array                                                                       
        $topass = $serializer->serialize($parametersArray, 'json');
        
        return new Response($topass);
    }
    
    public function saveDataAction(Request $request)
    {

        $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
        $API = $this->get('GlobalUtilsFunctions.APIGetData');
        $serializer = $this->get('jms_serializer'); 
        
        //let's get the posted data
        $allContent = $request->getContent();
        $allContent = json_decode($allContent,true);
        $postContent = json_decode($allContent["postContent"],true);
        //here they are
        $dimension = $postContent[0]; 
        $data = $postContent[1];

        $user = $this->getUser();
        $customer = new Customer();
        $customer = $user->getCustomer();  
        $array = ["customer"=>$customer];     
        
        //let's get the list of objects 
        $elementList = $API->requestSimpleByArray("BusinessDimension",$dimension,$array);
        $fieldsParameters = $elementList[0]->getFieldsParameters();
        $nbFields = count($fieldsParameters);
        $results = $setUpForDimension->saveResultsFromFlatView($data,$elementList,$dimension,$nbFields,$customer);
        
        $failed_lines = $results[0];
        $updated_lines = $results[1];
        
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
    
    
    
//end of class controller    
}


?>