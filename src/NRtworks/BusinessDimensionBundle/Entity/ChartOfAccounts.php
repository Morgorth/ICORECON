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
 * @ORM\Table(name="ChartOfAccounts")
 * @ExclusionPolicy("all") 
 */

    class ChartOfAccounts implements JsonSerializable
    {
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */

    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Expose
     */
   
    protected $name;  

     /**
     * @ORM\Column(type="string", length=100)
     * @Expose
     */
   
    protected $description;
    
    /**
     * @ORM\OneToMany(targetEntity="NRtworks\BusinessDimensionBundle\Entity\Account", mappedBy="chartofaccount")
     */
    private $accounts;

    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\SubscriptionBundle\Entity\Customer")     
     **/
    private $customer;

    
    /**
     * Constructor
     */
    public function __construct($id = NULL)
    {
        //the basic constructor is set to create a default object as we want it when creating a new one
        $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->id = $id;
        $this->name = "New chart of accounts";
        $this->description = "New chart of accounts";
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['name'] = $this->name;
        $result['description'] = $this->description;
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
     * Set code
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     */
    public function getDescription()
    {
        return $this->description;
    }
    
   
    /**
     * Add children
     */
    public function setAccounts(\NRtworks\BusinessDimensionBundle\Entity\Account $account)
    {
        $this->accounts[] = $account;
    }

    /**
     * Get children
     */
    public function getAccounts()
    {
        return $this->accounts;
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
        $info[1] = array("fieldName"=>"name","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"description","toDo"=>"edit","editType"=>"text","options"=>"none");
        return $info;
    }
    
    public function arrayalizeForTreeFlatView()
    {
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'description' => $this->description
        );        
    }
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'description' => $this->description
        );
    }
  
}
?>