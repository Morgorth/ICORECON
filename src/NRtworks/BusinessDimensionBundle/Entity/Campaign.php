<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;
    use Symfony\Component\Validator\Constraints as Assert;
        
    use JMS\Serializer\Annotation\ExclusionPolicy;
    use JMS\Serializer\Annotation\Expose;
    use JMS\Serializer\Annotation\Groups;
    use JMS\Serializer\Annotation\VirtualProperty;    
   
    
/**
 * @ORM\Entity
 * @ORM\Table(name="Campaign")
 */

    class Campaign implements JsonSerializable
    {
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    protected $id;

    /**
     * @ORM\Column(type="integer", length=4)
     */
   
    protected $number;  
    
    /**
     * @ORM\Column(type="string", length=25)
     */
   
    protected $name; 

    /**
     * @ORM\Column(type="string", length=25)
     */
   
    protected $status;     
    
    /**
     * @ORM\Column(type="integer", length=4)     
     **/

    
    private $fiscalYear;    

     /**
     * @ORM\Column(type="integer",length = 3)     
     **/
    
    private $version;
       
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Cycle")     
     **/
    private $cycle;    
        
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Period")     
     **/
    
    private $period;    
    
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\SubscriptionBundle\Entity\Customer")     
     **/
    
    private $customer;    


    
    /**
     * Constructor
     */
    public function __construct($id = NULL,$number = 0)
    {
        $this->id = $id;
        $this->number = $number;
        $this->name = "New campaign";
        $this->status = "not started";
        $this->fiscalYear = "2015";
        $this->version ="1";
        $this->cycle = "Actuals";
        $this->period = "January";
        
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['number'] = $this->number;
        $result['name'] = $this->name;
        $result['status'] = $this->status;
        $result['fiscalYear'] = $this->fiscalYear;
        $result['version'] = $this->version;
        $result['cycle'] = $this->cycle;
        $result['period'] = $this->period;
        return $result;
    }

   public function fillWithArray($array)
    {
        foreach ($array as $key => $value)
        {
            if ( property_exists ( $this , $key ) ){
                $this->{$key} = $value;
            }
        }
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * Get number
     */
    public function getNumber()
    {
        return $this->number;
    }    

    /**
     * Set name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     */
    public function getName()
    {
        return $this->name;
    } 

    /**
     * Set status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     */
    public function getStatus()
    {
        return $this->status;
    }     
    
     /**
     * set fiscalYear
     */
    public function setFiscalYear($fiscalYear = 2015)
    {
        $this->fiscalYear = $fiscalYear;
    }

    /**
     * Get fiscalYear
     */
    public function getFiscalYear()
    {
        return $this->fiscalYear;
    }    
    
    /**
     * set version
     */
    public function setVersion($version = 1)
    {
        $this->version = $version;
    }

    /**
     * Get version
     */
    public function getVersion()
    {
        return $this->version;
    }

    
    /**
     * set cycle
     */
    public function setCycle(\NRtworks\BusinessDimensionBundle\Entity\Cycle $cycle = null)
    {
        $this->cycle = $cycle;
    }

    /**
     * Get cycle
     */
    public function getCycle()
    {
        return $this->cycle;
    }

    /**
     * set period
     */
    public function setPeriod(\NRtworks\BusinessDimensionBundle\Entity\Period $period = null)
    {
        $this->period = $period;
    }

    /**
     * Get period
     */
    public function getPeriod()
    {
        return $this->period;
    }

        /**
     * Set customer
     *
     * @param \NRtworks\SubscriptionBundle\Entity\Customer $customer
     */
    public function setCustomer(\NRtworks\SubscriptionBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;
    }

    /**
     * Get customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }
    
    public function getFieldsParameters()
    {       
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"number","toDo"=>"show","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"name","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[3] = array("fieldName"=>"status","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"no","fieldFilter"=>"no"));
        $info[4] = array("fieldName"=>"fiscalYear","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"no","fieldFilter"=>"no"));
        $info[5] = array("fieldName"=>"version","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"no","fieldFilter"=>"no"));
        $info[6] = array("fieldName"=>"cycle","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"Cycle","fieldFilter"=>"no","selectFields"=>array("id","name")));
        $info[7] = array("fieldName"=>"period","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"Period","fieldFilter"=>"no","selectFields"=>array("id","name")));        
        return $info;
    }
    
    public function arrayalizeForTreeFlatView()
    {
        return array(
            'id' =>  $this->id,
            'number' => $this->number,
            'name' => $this->name,
            'status' => $this->status,
            'fiscalYear'=>$this->fiscalYear,
            'version' => $this->version,
            'cycle' => $this->cycle->getId(),
            'period' => $this->period->getId(),
            
        );
    }
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'number' => $this->number,
            'name' => $this->name,
            'status' => $this->status,
            'fiscalYear'=>$this->fiscalYear,
            'version' => $this->version,
            'cycle' => $this->cycle->jsonSerialize(),
            'period' => $this->period->jsonSerialize(),
            
        );
    }
  
}
?>