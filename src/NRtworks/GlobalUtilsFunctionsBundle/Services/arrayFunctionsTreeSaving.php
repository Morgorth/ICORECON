<?php

    namespace NRtworks\GlobalUtilsFunctionsBundle\Services;
    
class arrayFunctionsTreeSaving {
   
    
    
   //the following function rebuilds the tree, adding the parent_id of each element
    //it works by reference so it modifies the array passed in argument
   function rebuildTreeWithParentId(&$array,$childrenPropertyName,$parentCaller = NULL)
   {
       
       foreach($array as &$element)
       {

            if($parentCaller == NULL)
            {
                $parentCaller = 1;
            }
            $element["parentid"] = $parentCaller;                                     
            if(is_array($element[$childrenPropertyName]))
            {                               
                $this->rebuildTreeWithParentId($element[$childrenPropertyName],$childrenPropertyName,$element["id"]);
            }                    
       }
       
   }
     
   function rebuildTreeAsFlatArray($theTree,$childrenProperty) 
    {
        function theRecursion($subTree, &$accumulatingArray,$childrenProperty) 
        {
            foreach($subTree[$childrenProperty] as $node) 
            {
                theRecursion($node, $accumulatingArray,$childrenProperty);
                unset($node[$childrenProperty]);
                $accumulatingArray[] = $node;
            }
            
        }

        $accumulatingArray = array();
        theRecursion($theTree, $accumulatingArray,$childrenProperty);
        unset($theTree[$childrenProperty]);
        //array_push($accumulatingArray,$theTree);
        return $accumulatingArray;
    }
    


//class end   
}

?>
