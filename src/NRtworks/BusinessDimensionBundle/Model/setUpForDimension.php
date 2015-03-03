<?php

namespace NRtworks\BusinessDimensionBundle\Model;

use Doctrine\ORM\EntityManager;
use NRtworks\GlobalUtilsFunctionsBundle\Services\arrayFunctions;
use NRtworks\GlobalUtilsFunctionsBundle\Services\APIGetData;

class setUpForDimension extends \Symfony\Component\DependencyInjection\ContainerAware{
    
    protected $em;
    protected $arrayFunctions;
    protected $possible;
    protected $remotePossible;
    protected $noexist;
    protected $API;


    public function __construct(EntityManager $em, arrayFunctions $arrayFunctions, APIGetData $API)
    {
        $this->em = $em;
        $this->arrayFunctions = $arrayFunctions;
        $this->API = $API;
        $this->possible = array("ChartOfAccounts","Account","BusinessUnit","FiscalYear","Period","Version","Cycle","Currency","CurrencyValuation","Campaign");
        $this->remotePossible = array("ChartOfAccounts","Account","BusinessUnit","FiscalYear","Period","Version","Cycle","Currency","Campaign","icousers","Usertype");
        $this->noexist = "Unauthorized dimension";
    } 
      
    // the following function returns the address used to create the object of a related $dimension
    public function getAddress($dimension){ 

        if(in_array($dimension,$this->possible))
        {
            $base = "\NRtworks\BusinessDimensionBundle\Entity\\";
            $result = $base . $dimension;

            return $result;

        }
        else
        {;
           return $this->noexist;
        }
    }
    
   // the following function returns the address used to get the repository of a related $dimension
    public function getRepositoryAddress($dimension){
        
        if(in_array($dimension,$this->possible))
        {
            $base = "NRtworksBusinessDimensionBundle:";
            $result = $base . $dimension;
            return $result;

        }
        else
        {
           return $this->noexist;
        }
    }  

    
    //the following function returns an object of the entity related to the dimension
    public function getObject($dimension)
    {
        $address = $this->getAddress($dimension);
        $element = new $address();
        return $element;
    }
        
    //the following functions returns an array with parameters of the field to edit (name,editable,type of edit)
    public function getDefaultObject($dimension,$highestID = null)
    {        
        $address = $this->getAddress($dimension);                  
        $element = new $address($highestID);
        $result = $element->getDefaultObject();             
        return $result;
    }
    
    //the following function returns an object of the given dimension
    public function getDefaultTrueObject($dimension,$highestID = null, $number = null)
    {
        $address = $this->getAddress($dimension);  
        if($highestID != null && $number != null)
        {
            $element = new $address($highestID,$number);                    
        }
        elseif($highestID != null)
        {
            $element = new $address($highestID); 
        }
        else
        {
            $element = new $address(); 
        }
        return $element;
        
    }
    
    //the following functions returns an array with parameters of the field to edit (name,editable,type of edit)
    public function getFieldsNameToEdit($dimension)
    {
        $address = $this->getAddress($dimension);                  
        $element = new $address(); 
        $result = $element->fieldsToEditinTreeEdit();
        return $result;
    }    
    
    //the following functions gives the "mandatory" selector for a dimension
    public function getBasicDiscriminant($dimension)
    {
        if(in_array($dimension,$this->possible) || in_array($dimension,$this->remotePossible))
        {
            $discrim = [];
            if($dimension == "Account")
            {
                $discrim["toSelect"] = ["ChartOfAccount"];
                $discrim["howToSelect"] = "UserSelection";
            }
            elseif($dimension == "CurrencyValuation")
            {
                $discrim["toSelect"] = ["Campaign"];
                $discrim["howToSelect"] = "UserSelection";
            }  
            elseif($dimension == "Cycle" || $dimension == "Version" || $dimension == "Period" || $dimension == "FiscalYear" || $dimension == "Currency")
            {
                $discrim["toSelect"] =  "none";
            }            
            else
            {
                $discrim["toSelect"]= ["customer"];
            }
            return $discrim;
        }
        else
        {
            return $this->noexist;
        }
    }
    
    //the following function is building an array for a "select" element to be passed to the front
    public function buildSelectElements($dimension,$fieldParameters,$param1)
    {
        foreach($fieldParameters as &$field)
        {
            if($field["toDo"] == "edit" && $field["editType"] == "select")
            {
                //if we are here it means the field is to be edited and is a select, so let's check the options
                if(isset($field["options"]["remote"]) && $field["options"]["remote"] != "no")
                {
                    $remoteDimension = $field["options"]["remote"];
                    //here means that the field is remote so we need to request the data given some parameters
                    if(in_array($remoteDimension,$this->remotePossible))
                    {                        
                        if($remoteDimension == "Account")
                        {
                            $whereArray["chartofaccount"] = 1;
                        }
                        elseif($remoteDimension == "Cycle" || $remoteDimension == "Version" || $remoteDimension == "Period" || $remoteDimension == "FiscalYear" || $remoteDimension == "Currency")
                        {
                            // no need for generic selector here

                        }
                        else 
                        {
                            $whereArray["customer"] = $param1;
                        }
                        
                        
                        if(is_array($field["options"]["fieldFilter"]))
                        {
                            foreach($field["options"]["fieldFilter"] as $key=>$value)
                            {                                
                                $whereArray[$key] = $value;
                            }                            
                        }
                        //\Doctrine\Common\Util\Debug::dump($whereArray);
                        if(isset($whereArray))
                        {
                            $elementList = $this->API->requestQuery($this->API->whichBundle($remoteDimension),$remoteDimension,$whereArray);
                        }
                        else
                        {
                            $elementList = $this->API->requestAll($this->API->whichBundle($remoteDimension),$remoteDimension);
                        }
                        
                        unset($whereArray);
                        
                        //\Doctrine\Common\Util\Debug::dump($elementList);
                        $elementsAsArray = $this->arrayFunctions->rebuildObjectsAsArrays($elementList);
                        //\Doctrine\Common\Util\Debug::dump($elementsAsArray);
                        
                        //ok we got all we need so let's build the array used to build the HTML select element
                        $arrayHTML = [];
                        foreach($elementsAsArray as $element)
                        {
                            $subarray = array("value"=>$element[$field["options"]["selectFields"][0]],"text"=>$element[$field["options"]["selectFields"][1]]);
                            array_push($arrayHTML,$subarray);
                        }
                        
                        $field["options"] = $arrayHTML;
                    }
                    else
                    {
                        return $this->noexist;
                    }
                }
                else
                {
                    //here means that the field must have a "local" parameter, set up below
                    switch($dimension)
                    {
                        case "Campaign":
                            if($field["fieldName"] == "fiscalYear")
                            {
                                $field["options"] = $this->getFiscalYearList("selectArray");
                            }
                            elseif($field["fieldName"] == "version")
                            {
                                $field["options"] = $this->getVersionList("selectArray");
                            }
                            elseif($field["fieldName"] == "status")
                            {
                                $field["options"] = array(0=>["value"=>"not started","text"=>"not started"],1=>["value"=>"in progress","text"=>"in progress"],2=>["value"=>"closed","text"=>"closed"]);
                            }
                            else
                            {
                                $field["options"] = array("value"=>"throwError","text"=>"an error has occured");
                            }
                            break;
                        case "Account":
                            if($field["fieldName"] == "sense")
                            {
                                $field["options"] = array(0=>array("value"=>"DR","text"=>"Debit"),1=>array("value"=>"CR","text"=>"Credit"));
                            }
                            else
                            {
                                $field["options"] = array("value"=>"throwError","text"=>"an error has occured");
                            }
                            break;
                        case "BusinessUnit":
                            if($field["fieldName"] == "country")
                            {
                                $field["options"] = array(0=>array("value"=>"FR","text"=>"France"));
                            }
                            else
                            {
                                $field["options"] = array("value"=>"throwError","text"=>"an error has occured");
                            }
                            break;
                            
                        default: 
                            $field["options"] = array("value"=>"throwError","text"=>"an error has occured");
                            break;
                    }
                }
            }
        }
        
        return $fieldParameters;
    }  
    
    //the following function returns a list of the fiscal years, however you want it
    public function getFiscalYearList($how)
    {
        switch($how)
        {
            case "simpleArray":
                return array(2012,2013,2014,2015,2016,2017,2018,2019,2020);
                break;
            case "associativeArray":
                return array(2012=>2012,2013=>2013,2014=>2014,2015=>2015,2016=>2016,2017=>2017,2018=>2018,2019=>2019,2020=>2020);
                break;
            case "selectArray":  
                $result = [];
                $i = 0;
                foreach($this->getFiscalYearList("simpleArray") as $year)
                {
                    $result[$i] = array("value"=>$year,"text"=>$year);
                    $i++;
                }
                return $result;
                break;
                
            default:
                return "unknown case";
                break;
        }                
    }
    
    //the following function returns a list of the versions, however you want it
    public function getVersionList($how)
    {
        switch($how)
        {
            case "simpleArray":
                return array(1,2,3,4,5);
                break;
            case "associativeArray":
                return array(1=>1,2=>2,3=>3,4=>4,5=>5);
                break;
            case "selectArray":  
                $result = [];
                $i = 0;
                foreach($this->getVersionList("simpleArray") as $version)
                {
                    $result[$i] = array("value"=>$version,"text"=>$version);
                    $i++;
                }
                return $result;
                break;            
                
            default:
                return "unknown case";
                break;
        }                
    }
    
    //the following function returns a list of elements from an entity given some conditions
    public function getFlatList($dimension,$customer = 0,$chartOfAccounts = 0)
    {
        $address = $this->getRepositoryAddress($dimension);                  
        $repo = $this->em->getRepository($address);
        
        if(in_array($dimension,$this->possible))
        {
            if($dimension == "Account")
            {
                return $allaccounts = $repo->findByChartofaccount(1);
            }
            elseif($dimension == "BusinessUnit")
            {
                return $allaccounts = $repo->findByCustomer($customer);
            }

        }
        else
        {
            return $this->noexist;
        }
        
    }
    
    
    //the following function creates a new object with values passed to the function
    public function createAnObject($customer,$dimension,$element,$parent = null)
    {
        if(in_array($dimension,$this->possible))
        {
            if($dimension == "Account")
            {
                
                $newObject = $this->getObject($dimension);
                $newObject->fillWithArray($element);
                $newObject->setChartOfAccount($parent->getChartOfAccount());
                $newObject->setParent($parent);
                return $newObject;
                
            }
            if($dimension == "BusinessUnit")
            {               
                $newObject = $this->getObject($dimension);
                $newObject->setParent($parent);
                $newObject->setCustomer($customer);
                
                $newObject->setName($element["name"]);
                $newObject->setCode($element["code"]);
                $newObject->setCountry($element["country"]);     

                $newObject->setManager($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["manager"]));
                $newObject->setSubstitute($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["substitute"]));
                $newObject->setController($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["controller"]));
                $newObject->setBusinessCurrency($this->API->requestById($this->API->whichBundle("Currency"),"Currency",$element["businessCurrency"]));                
                return $newObject;
            }
            if($dimension == "ChartOfAccounts")
            {
                $address = $this->getAddress($dimension);                  
                $newObject = new $address();
                $newObject->setCustomer($customer);
                $newObject->setName($element[1]);
                $newObject->setDescription($element[2]);
                return $newObject;
            }
            if($dimension == "Campaign")
            {
                $address = $this->getAddress($dimension);                  
                $newObject = new $address();
                $newObject->setCustomer($customer);
                $newObject->setNumber($element[1]);
                $newObject->setName($element[2]);
                $newObject->setStatus($element[3]);
                $newObject->setFiscalYear($element[4]);
                $newObject->setVersion($element[5]);
                $newObject->setCycle($this->API->requestById($this->API->whichBundle("Cycle"),"Cycle",$element[6]));
                $newObject->setPeriod($this->API->requestById($this->API->whichBundle("Period"),"Period",$element[7]));
                
                
                return $newObject;
            }            
        }
        else
        {
            return $this->noexist;
        }
        
    }
    
    //the following function updates an object with values passed to the function
    public function updateAnObject($object,$element,$dimension)
    {
        if(in_array($dimension,$this->possible))
        {
            if($dimension == "Account")
            {                        
                $object->setName($element["name"]);
                $object->setCode($element["code"]);
                $object->setSense($element["sense"]);
                return $object;
                
            }
            if($dimension == "ChartOfAccounts")
            {
                $object->setName($element[1]);
                $object->setDescription($element[2]);
                return $object;
            }
            if($dimension == "Campaign")
            {
                $object->setNumber($element[1]);
                $object->setName($element[2]);
                $object->setStatus($element[3]);
                $object->setFiscalYear($element[4]);
                $object->setVersion($element[5]);
                $object->setCycle($this->API->requestById($this->API->whichBundle("Cycle"),"Cycle",$element[6]));
                $object->setPeriod($this->API->requestById($this->API->whichBundle("Period"),"Period",$element[7]));
                return $object;
            }
            if($dimension == "BusinessUnit")
            {
                $object->setName($element["name"]);
                $object->setCode($element["code"]);
                $object->setCountry($element["country"]);
                $object->setManager($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["manager"]));
                $object->setSubstitute($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["substitute"]));
                $object->setController($this->API->requestById($this->API->whichBundle("icousers"),"icousers",$element["controller"]));
                $object->setBusinessCurrency($this->API->requestById($this->API->whichBundle("Currency"),"Currency",$element["businessCurrency"]));
                return $object;
            }
        }
        else
        {
            return $this->noexist;
        }
        
    }    
    
    //the following function is the model that saves data modified by the user in flat view
    public function saveResultsFromFlatView($result,$elementList,$dimension,$nbFields,$customer)
    {
        $failedLines = [];
        $updatedLines = [];
        
        foreach($result as $element)
        {
            //var_dump($element);
            if(isset($element[$nbFields]) && $element[$nbFields] == "NRtworks_FlatView_T0Cr3at3")
            {
                //a new element
                try
                {
                    $newElement = $this->createAnObject($customer,$dimension,$element);
                    $this->em->persist($newElement);
                    array_push($updatedLines,$element);
                }
                catch(Exception $e)
                {
                    array_push($failedLines,$element);
                }

            }
            elseif(isset($element[$nbFields+1]) && $element[$nbFields+1] == "NRtworks_FlatView_ToD3l3t3")
            {
                //to delete
                try 
                {
                    $object = $elementList[$this->arrayFunctions->findIndexOfAPropertyByIdInArrayOfObject($elementList,$element[0])];
                    $this->em->remove($object);
                    array_push($updatedLines,$element);
                } 
                catch (Exception $e) 
                {
                    array_push($failedLines,$element);
                }

            }
            else
            {
                // element modified or not
                try 
                {
                    $object = $elementList[$this->arrayFunctions->findIndexOfAPropertyByIdInArrayOfObject($elementList,$element[0])];
                    $updatedObject = $this->updateAnObject($object,$element,$dimension);
                    //\Doctrine\Common\Util\Debug::dump($updatedObject);
                    if($object != $updatedObject)
                    {
                        $this->em->merge($updatedObject);
                        array_push($updatedLines,$element);
                    }                    
                } 
                catch (Exception $e) 
                {
                    array_push($failedLines,$element);
                }

            }
            
        }
        
        $this->em->flush();
        $result = array(0=>$failedLines,1=>$updatedLines);
        return $result;
    }    
    
}

?>
