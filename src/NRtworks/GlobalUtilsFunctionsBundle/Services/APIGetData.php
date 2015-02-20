<?php

    namespace NRtworks\GlobalUtilsFunctionsBundle\Services;

    use Doctrine\ORM\EntityManager;
    
class APIGetData extends \Symfony\Component\DependencyInjection\ContainerAware{
   
   protected $em;
   protected $error;
   protected $BusinessDimension;
   protected $Subscription;
   
   
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->error = "Unidentified error";
        $this->BusinessDimension = array("ChartOfAccounts","Account","BusinessUnit","FiscalYear","Period","Version","Cycle","Currency","Campaign");
        $this->Subscription = array("Usertype","icousers","Customer");
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
 
    // the following returns the bundle name of a dimension/entity
    public function whichBundle($entity)
    {
        if(in_array($entity,$this->BusinessDimension))
        {
            return "BusinessDimension";
        }
        elseif(in_array($entity,$this->Subscription))
        {
            return "Subscription";
        }
        else
        {
            return "none";
        }
    }
    
    public function buildWhereClause($whereArray)
    {
        $where = "";
        $i = 0;
        //\Doctrine\Common\Util\Debug::dump($whereArray);
        foreach($whereArray as $key=>$value)
        {
            
            if($i != 0){$where = $where.' AND ';}
            if(is_array($value))
            {
                $where = $where . 'e.'.$key.' '.$value["operator"].' \''.$value["value"].'\'';
            }
            else
            {
                $where = $where . 'e.'.$key.' = \''.$value.'\'';
            }
            $i++;
            //echo $where;
        }
        
        return $where;
    }
    
    public function requestSimpleByArray($bundle,$dimension,$array)
    {
        $repo = $this->getRepository($bundle,$dimension);
        $result = $repo->findBy($array);
        return $result;
    }
    
    public function requestAll($bundle,$dimension)
    {
        $repo = $this->getRepository($bundle,$dimension);
        $result = $repo->findAll();
        return $result;
    }
    
    public function requestById($bundle,$dimension,$id)
    {
        $repo = $this->getRepository($bundle,$dimension);
        $result = $repo->find($id);
        //\Doctrine\Common\Util\Debug::dump($result);
        return $result;        
    }
           
    public function requestQuery($bundle,$dimension,$whereArray,$orderBy = "id",$order = "ASC")
    {
       $repo = $this->getRepository($bundle,$dimension);
       
       $whereClause = $this->buildWhereClause($whereArray);

        $query = $this->em->createQuery(
            'SELECT e
            FROM NRtworks'.$bundle.'Bundle:'.$dimension.' e
            WHERE '.$whereClause.'
            ORDER BY e.'.$orderBy.' '.$order.''
        );
        //\Doctrine\Common\Util\Debug::dump($query);              
       return $result = $query->getResult();
    }
    
    public function requestBySQLQuery($bundle,$dimension,$field,$value,$operator,$orderBy = "id",$order = "ASC")
    {
        
    }

    
    //end of class
}

?>
