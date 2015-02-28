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
 * @ORM\Table(name="CurrencyValuation")
 */

    class CurrencyValuation implements JsonSerializable
    {
     /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    protected $id;

    /**
     * @ORM\Column(type="float", length=8)
     */
   
    protected $value;  
    
    
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Campaign")
     */
    protected $campaignAssigned;  

    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Currency")
     */
    protected $currencyNumerator;  
    
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Currency")
     */
    protected $currencyDenominator;     
            
    /**
     * Constructor
     */
    public function __construct($id = NULL,$number = 0)
    {
        $this->id = $id;
        $this->value = 1;
        $this->campaignAssigned = "not set";
        $this->currencyNumerator = "not set";
        $this->currencyDenominator = "not set";        
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['value'] = $this->value;
        $result['campaignAssigned'] = $this->campaignAssigned;
        $result['currencyNumerator'] = $this->currencyNumerator;
        $result['currencyDenominator'] = $this->currencyDenominator;

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
     * Set value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     */
    public function getValue()
    {
        return $this->value;
    }    

    
     /**
     * set campaignAssigned
     */
    public function setCampaignAssigned($campaignAssigned = 2015)
    {
        $this->campaignAssigned = $campaignAssigned;
    }

    /**
     * Get campaignAssigned
     */
    public function getCampaignAssigned()
    {
        return $this->campaignAssigned;
    }    
    
    /**
     * set currencyNumerator
     */
    public function setCurrencyNumerator($currencyNumerator = 1)
    {
        $this->currencyNumerator = $currencyNumerator;
    }

    /**
     * Get currencyNumerator
     */
    public function getCurrencyNumerator()
    {
        return $this->currencyNumerator;
    }

    
    /**
     * set currencyDenominator
     */
    public function setCurrencyDenominator(\NRtworks\BusinessDimensionBundle\Entity\CurrencyDenominator $currencyDenominator = null)
    {
        $this->currencyDenominator = $currencyDenominator;
    }

    /**
     * Get currencyDenominator
     */
    public function getCurrencyDenominator()
    {
        return $this->currencyDenominator;
    }
    
    public function getFieldsParameters()
    {       
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"value","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"campaignAssigned","toDo"=>"noShow","editType"=>"select","options"=>array("remote"=>"Campaign","fieldFilter"=>"no","selectFields"=>array("id","name")));
        $info[3] = array("fieldName"=>"currencyNumerator","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"Currency","fieldFilter"=>"no","selectFields"=>array("id","name")));
        $info[4] = array("fieldName"=>"currencyDenominator","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"Currency","fieldFilter"=>"no","selectFields"=>array("id","name")));
        
        return $info;
    }
    
     public function arrayalizeForTreeFlatView()
    {
        return array(
            'id' =>  $this->id,
            'value' => $this->value,
            'currencyNumerator'=>$this->currencyNumerator,
            'currencyDenominator' => $this->currencyDenominator,
            
        );
    }
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'value' => $this->value,
            'campaignAssigned' => $this->campaignAssigned,
            'currencyNumerator'=>$this->currencyNumerator,
            'currencyDenominator' => $this->currencyDenominator,
            
        );
    }
  
}
?>