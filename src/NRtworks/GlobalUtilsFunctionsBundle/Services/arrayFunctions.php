<?php

    namespace NRtworks\GlobalUtilsFunctionsBundle\Services;
    
class arrayFunctions {
   
    
    
    // --------------------------------- FUNCTIONS FOR FLAT ARRAYS ----------------------
       
    
    //the following function returns the index of a property of a target value in an array of array
    public function arrayFindIndexOfAPropertyByValue($array,$propertyName,$target)
    {
        $i = 0;
        foreach($array as $element)
        {
            if($element[$propertyName] === $target)
            {            
                return $i;
            }
            else
            {

            }

            $i++;
        }
    }
    
    public function findIndexOfAPropertyByIdInArrayOfObject($array,$target)
    {
        $i = 0;
        foreach($array as $element)
        {
            
            if($element->getId() == $target)
            {     
                return $i;
            }
            else
            {

            }

            $i++;
        }  
        return false;
    }
    
    public function findIndexOfAPropertyByParentIdInArrayOfObject($array,$target)
    {
        $i = 0;
        foreach($array as $element)
        {
            
            if($element->getParent() == $target)
            {     
                
                return $i;
            }
            else
            {

            }

            $i++;
        }  
        return false;
    }
    
    //the following function returns the element containing a property of a target value in an array
    // it is designed to get out element of tree hierarchy
    function arrayReturnElementContainingAPropertyByValue($array,$propertyName,$target)
    {
        $i = 0;
        foreach($array as $element)
        {
            if($element[$propertyName]== $target)
            {            
                return $element;
            }
            else
            {

            }

            $i++;
        }
    }
    
     //the following function returns the object containing a target property of a target value
    // it is designed to get out element of array of objects
    function arrayReturnObjectContainingAPropertyByValue($array,$propertyName,$target)
    {
        $i = 0;
        foreach($array as $element)
        {
            if($element->{'get'.$propertyName}() == $target)
            {            
                return $element;
            }
            else
            {

            }

            $i++;
        }
    }  
    
    //the following function replaces the oldValue of a propertyName by a new value in the array given
    function arrayChangingValues(&$array,$propertyName,$newValue,$oldValue)
    {
        foreach($array as &$element)
        {
            if(isset($element[$propertyName]) && $element[$propertyName] == $oldValue)
            {
                $element[$propertyName] = $newValue;                
            }
        }
        
        
    }
    
    //the following function search for a targetElement in a targetArray and return true if yes
    function elementIsInArray($targetArray,$targetElement)
    {
        if(empty($targetArray))
        {
            return false;
        }
        else
        {
            foreach($targetArray as $element)
            {
                if($element = $targetElement)
                {
                    return true;
                }
            }
            return false;
        }
    }
    
    
    
    // -------------------------------------------------------------------- FUNCTIONS for specific use -------------------------------------------
    
    // the following functions is set to rebuild an associative array into an array with just the values (to pass as array to JS)
    public function rebuildANonAssociativeArray($array)
    {
        $result = [];
        $subarray = [];
        
        foreach($array as $key=>$value)
        {
            if(is_array($array[$key]))
            {
                $subarray = $this->rebuildANonAssociativeArray($array[$key]);
                array_push($result,$subarray);
            }
            else
            {   
                array_push($result,$array[$key]);
            }

        }
        
        return $result;
        
    }
    
    

    // ---------------------------------------------------------------------- Functions for Tree hierarchy ------------------------------------------         
    
    //the following returns the highest ID of a Tree hierarchy
    function arrayGetHighestIdFromTree($tree,$childrenProperty)
    {    
        //var_dump($tree);
        function recursion($subtree,&$highest,$childrenProperty)
        {
            foreach($subtree as $object)
            {
                //var_dump($object);
                
                if($object['id'] > $highest)
                {
                    $highest = $object['id'] ;            
                }   
                if($object[$childrenProperty])
                {
                   recursion($object[$childrenProperty],$highest,$childrenProperty); 
                }
            }
        }
        $highest = 0;
        recursion($tree,$highest,$childrenProperty);
        return $highest;    
    }
    
    //the following function is used to rename a property inside an array
    // it is designed for ordered array -> if the property you want to replace is array it's recursive
    public function arrayRenameAProperty($array,$oldProperty,$newProperty)
    {
        //var_dump ($array);
        foreach($array as &$element)
        {
            //var_dump($element);
            if(isset($element[$oldProperty]))
            {
               if(is_array($element[$oldProperty])) 
               {
                    $element[$oldProperty] = $this->arrayRenameAProperty($element[$oldProperty],$oldProperty,$newProperty);          
               }
               
               $element[$newProperty] = $element[$oldProperty];
               unset($element[$oldProperty]); 
            }
            else
            {               
            }

        }
        return $array;
    } 
 
    
    //end of class
}

?>
