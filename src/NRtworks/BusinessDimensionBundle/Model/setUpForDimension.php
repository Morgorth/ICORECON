<?php

namespace NRtworks\BusinessDimensionBundle\Model;

use Doctrine\ORM\EntityManager;
use NRtworks\GlobalUtilsFunctionsBundle\Services\arrayFunctions;

class setUpForDimension extends \Symfony\Component\DependencyInjection\ContainerAware{
    
    protected $em;
    protected $arrayFunctions;
    protected $possible;
    protected $noexist;


    public function __construct(EntityManager $em, arrayFunctions $arrayFunctions)
    {
        $this->em = $em;
        $this->arrayFunctions = $arrayFunctions;
        $this->possible = array("ChartOfAccounts","Account","BusinessUnit","FiscalYear","Period","Version","Cycle","Currency");
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
    
    //the following function returns a tree hierarchy (using DoctrineExtension)
    // old
    public function getChildrenHierarchy($dimension,$customer)
    {
        
        $address = $this->getRepositoryAddress($dimension);                  
        $repo = $this->em->getRepository($address);
        $arrayTree = $repo->childrenHierarchy();            
        return $arrayTree;
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
    public function getDefaultTrueObject($dimension,$highestID = null)
    {
        $address = $this->getAddress($dimension);  
        if($highestID != null)
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
                $address = $this->getAddress($dimension);                  
                $newObject = new $address();
                $newObject->fillWithArray($element);
                $newObject->setCustomer($customer);
                $newObject->setParent($parent);
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
                    if($object != $updatedObject)
                    {
                        $this->em->persist($updatedObject);
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
