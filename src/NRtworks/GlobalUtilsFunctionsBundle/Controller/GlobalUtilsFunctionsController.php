<?php

namespace NRtworks\GlobalUtilsFunctionsBundle\Controller;


//Symfony elements
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//entity loadings
use NRtworks\SubscriptionBundle\Entity\Customer;


class GlobalUtilsFunctionsController extends Controller
{
    

    
    // BASIC PAGE FUNCTION
    public function checkInDBAction(Request $request)
    {

        if($this->getUser())
        {
            $session = new Session();
            $dimensionIdentification = $session->get('NRtworks_DimensionIdentification');
            
            $em = $this->getDoctrine()->getManager();
            $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
            $validator = $this->get('validator');
            $API = $this->get('GlobalUtilsFunctions_APIGetData');
            //identifying user & customer
            $user = $this->getUser();
            $customer = new Customer();
            $customer = $user->getCustomer();            
            $customerID = $customer->getId(); 

            //let's get the posted data
            $allContent = $request->getContent();
            $allContent = json_decode($allContent,true);
            //here they are
            $dimension = $allContent["dimensionPassed"]; 
            $fieldName = $allContent["fieldPassed"];
            $data = $allContent["dataPassed"]; 
            
            $defaultObject = $setUpForDimension->getDefaultTrueObject($dimension);
            $defaultObject->{"set".ucfirst($fieldName)}($data);
            /*if(is_Array($dimensionIdentification))
            {
                $array = ["customer"=>$customer];   
                $ok = $API->requestSimpleByArray("BusinessDimension","ChartOfAccounts",$array);
                //\Doctrine\Common\Util\Debug::dump($ok[0]);
                $defaultObject->setChartOfAccount($ok[0]);
            }
            else
            {
                $defaultObject->setCustomer($customer);
            }*/
            
            //\Doctrine\Common\Util\Debug::dump($defaultObject);
            
            $i = 0;
            $errorMessage=0;
            $status = 1;
            
            $errors = $validator->validate($defaultObject);
            //var_dump($errors);
            if($errors->count()>0)
            {
                
                $status = 0;
                while($i < $errors->count())
                {
                    $errorMessage =  $errors->get($i)->getMessage();
                    $i++;
                }
            }
            
            
            $res = json_encode(["status"=>$status,"errorMessage"=>$errorMessage]);                     
            return new Response($res); 
            
        }
    }
    
    public function validatorGetConstraintsAction(Request $request)
    {
        if($this->getUser())
        {
            //echo "validatorGetConstraints";
            $em = $this->getDoctrine()->getManager();
            
            //identifying user & customer
            $user = $this->getUser();
            $customer = new Customer();
            $customer = $user->getCustomer();            
            $customerID = $customer->getId(); 

            //let's get the posted data
            $allContent = $request->getContent();
            $allContent = json_decode($allContent,true);
            $dimension = $allContent["dimensionpassed"];
            //echo $dimension;
            
            $propertiesConstraints=[];
            //let's get the validation information
            $validator=$this->get("validator");
            
            //let's get a default object of the current dimension
            $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
            $defaultObject = $setUpForDimension->getDefaultTrueObject($dimension);
            //var_dump($defaultObject);
            
            $metadata=$validator->getMetadataFor($defaultObject);            
            //var_dump($metadata);
            //the following will gives us an array of the constrained properties
            $constrainedProperties=$metadata->getConstrainedProperties();
            //var_dump($metadata->getConstraints());
            //so we loop on it to build an easy array, by property, with their constraints
            //the goal is to obtain an array like this
            // ["constrainedProperty"=>["constraintName"=>[["constraintParameter"],["error message"]]]]
            foreach($constrainedProperties as $constrainedProperty)
            {
                $propertyMetadata=$metadata->getPropertyMetadata($constrainedProperty);                
                $constraints=$propertyMetadata[0]->constraints;
                //var_dump($propertyMetadata[0]);
                $outputConstraintsCollection=[];
                foreach($constraints as $constraint)
                {
                    $class = new \ReflectionObject($constraint);
                    $constraintName=$class->getShortName();
                    $constraintParameter=null;
                    switch ($constraintName) 
                    {
                        case "NotBlank":
                            $param="notBlank";
                            $message = $constraint->message;
                            break;
                        case "Type":
                            $param=$constraint->type;
                            $message = $constraint->message;
                            break;
                        case "Length":
                            $i = 0;
                            $param = [];
                            if(isset($constraint->min)){$param[$i] = $constraint->min; $message[$i] = $constraint->minMessage; $i++;};
                            if(isset($constraint->max)){$param[$i] = $constraint->max; $message[$i] = $constraint->maxMessage;};
                           
                            break;
                        case "Regex":
                            $param = $constraint->pattern;
                            $message = $constraint->message;
                            break;
                            
                    }
                    $outputConstraintsCollection[$constraintName]=[$param,$message];
                    $param = "";
                    $message = "";
                }
                $propertiesConstraints[$constrainedProperty]=$outputConstraintsCollection;
            }
            
            if($propertiesConstraints == []){$propertiesConstraints = "free";};

            //now let's make an array for the constraints attached the entity itself,ex: unique constraints
            $entityConstraints = [];
            
            foreach($metadata->getConstraints() as $constraint)
            {
                    array_push($entityConstraints,$constraint->fields);
            }
            if($entityConstraints == []){$entityConstraints = "free";};
            
            $res = json_encode(["propertiesConstraints"=>$propertiesConstraints,"entityConstraints"=>$entityConstraints]);                     
            return new Response($res); 
            
        }
    }
    
    //the following function set the parameters of the business dimension into a session and returns these parameters as a json response
    public function getSetBusinessDimensionParametersAction(Request $request)
    {
        $session = new Session();  
        
        if($this->getUser())
        {
            //let's get the posted data
            $allContent = $request->getContent();
            $allContent = json_decode($allContent,true);
            $dimension = $allContent["dimensionpassed"];
                           
            $setUpForDimension = $this->get('BusinessDimension.setUpForDimension');
            $API = $this->get('GlobalUtilsFunctions_APIGetData');
            $serializer = $this->get('jms_serializer'); 

            //let's get infos on how to treat this dimension
            $dimensionDiscrim = $setUpForDimension->getBasicDiscriminant($dimension);

            $parametersArray = [];
            $parametersArray = ["BDName"=>$dimension];

            $selectorList = 0;
            //echo $dimensionDiscrim["toSelect"][0];
            if(is_array($dimensionDiscrim) && $dimensionDiscrim["toSelect"] != "none")
            {
                if(isset($dimensionDiscrim["howToSelect"]) && $dimensionDiscrim["howToSelect"] = "UserSelection")
                {
                    //the users need to select a specific dimension to edit on, need to get the list to pass it to a template
                    $whereArray["customer"] = $session->get("CustomerID");
                    $selectorList = $API->requestQuery($API->whichBundle($dimensionDiscrim["toSelect"][0]),$dimensionDiscrim["toSelect"][0],$whereArray);
                    //$selectorList = $serializer->serialize($selectorList, 'json');
                }
                else
                {
                    //this is the easy case, the selector is defined in the model
                    $parametersArray["BDParameters"]=["BDDiscrim"=>$dimensionDiscrim["toSelect"]];
                }
            }
            else
            {
                $parametersArray["BDParameters"]=["BDDiscrim"=>"none"];
            }

            $session->set("BDParameters", $parametersArray);
            //$parametersArray = json_encode($parametersArray);
            $res = json_encode(["BDParameters"=>$parametersArray,"selectorList"=>$selectorList]);                     
            return new Response($res); 
        }
    }
    
    
//end of class controller    
}


?>