<?php

namespace NRtworks\TreeViewBundle\Model;

use Doctrine\ORM\EntityManager;
use NRtworks\GlobalUtilsFunctionsBundle\Services\arrayFunctions;

class treeBuilder extends \Symfony\Component\DependencyInjection\ContainerAware{
    
    protected $em;
    protected $arrayFunctions;


    public function __construct(EntityManager $em,arrayFunctions $arrayFunctions)
    {
        $this->em = $em;
        $this->arrayFunctions = $arrayFunctions;

    } 
      
    public function buildBasicTree($allElements)
    {
        $allElementsArray = $this->rebuildObjectsAsArrays($allElements);

        //$root = $this->arrayFunctions->arrayReturnElementContainingAPropertyByValue($allElementsArray,"root",1);
        return $result = $this->theRecursion($allElementsArray,NULL);
    }
    
    public function theRecursion(array &$elements, $parentId = NULL) 
    {
        $branch = array();
        foreach ($elements as $element) 
        {
            
            if ($element['parentid'] == $parentId) 
            {
                $children = $this->theRecursion($elements, $element['id']);
                if (is_array($children)) 
                {
                    foreach($children as $child)
                    {
                        array_push($element['nodes'],$child);
                    }
                }
                //var_dump($element);
                array_push($branch,$element);
                //unset($elements[$element['id']]);
            }
        }
        return $branch;
    }
    
    public function rebuildObjectsAsArrays($allElements)
    {
        $result = [];
        $i = 0;
        foreach($allElements as $element)
        {
            $result[$i] = $element->jsonSerialize();
            if(isset($result[$i]["root"]))
                {
                if($result[$i]["root"] != 1)
                {
                    $result[$i]["parentid"] = $element->getParent()->getId();
                }
                else
                {
                    
                }
            }
            
            $i++;
        }

        return $result;
    }
      
}

?>
