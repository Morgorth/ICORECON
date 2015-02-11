<?php

    namespace NRtworks\GlobalUtilsFunctionsBundle\Services;

    use Doctrine\ORM\EntityManager;
    
class APIGetData extends \Symfony\Component\DependencyInjection\ContainerAware{
   
   protected $em;
   protected $error;
   
   
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->error = "Unidentified error";
    } 
    
    protected function getRepository($bundle = "BusinessDimension",$dimension)
    {
        $string = "NRtworks".$bundle."Bundle:".$dimension;
        Try
        {
            return $this->em->getRepository($string);
        }
        Catch(Exception $e)
        {
            return "Requested entity is unknown";
        }
    }
 
    public function requestSimpleByArray($bundle,$dimension,$array)
    {
        $repo = $this->getRepository($bundle,$dimension);
        $result = $repo->findBy($array);
        return $result;
    }

    
    //end of class
}

?>
