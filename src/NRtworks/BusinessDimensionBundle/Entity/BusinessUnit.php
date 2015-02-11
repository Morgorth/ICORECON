<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use Gedmo\Mapping\Annotation as Gedmo;
    use JsonSerializable;
    use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="BusinessUnit")
 * @ORM\Entity
 */

    class BusinessUnit implements JsonSerializable
    {
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
   
    protected $name;  

     /**
     * @ORM\Column(type="string", length=50)
     */
    
    
    protected $code;
    
    /**
     * @ORM\Column(type="string", length=50)
     */
    
    protected $country;
    
     /**
     * @ORM\Column(name="root", type="integer")
     */
    protected $root;

    /**
     * @ORM\ManyToOne(targetEntity="BusinessUnit", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="BusinessUnit", mappedBy="parent")
     */
    protected $children;
    
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
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->id = $id;
        $this->name = "New Entity";
        $this->code = "xxxxxx";
        $this->country = "";
        $this->root = 0;
        

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


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['name'] = $this->name;
        $result['code'] = $this->code;
        $result['country'] = "";
        $result["action"] = "new";
        $result["nodes"] = [];
        return $result;
    }
    
    //this method return the list of the field's name to be edited in the tree editing
    public function fieldsToEditinTreeEdit()
    {
        //the array model is [0=>"name of the property",1=>is not editable in the tree 1 is, 2=> type of input,3=>more parameter for the input, example for a select]
        $toEdit[0] = array(0=>"id",1=>0,2=>"text");
        $toEdit[1] = array(0=>"name",1=>1,2=>"text");
        $toEdit[2] = array(0=>"code",1=>1,2=>"text");
        $toEdit[3] = array(0=>"country",1=>1,2=>"text");
        $toEdit[5] = array(0=>"root",1=>0,2=>"text");
        $toEdit[6] = array(0=>"nodes",1=>0,2=>"text");                
        return $toEdit;
    }
    
        /**
         * Get id
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
        public function setCode($code)
        {
            $this->code = $code;
        }

        /**
         * Get code
         */
        public function getCode()
        {
            return $this->code;
        }
        
        public function setParent(BusinessUnit $parent=null)
        {
            $this->parent = $parent;
        }

        public function getParent()
        {
            return $this->parent;
        }

   
    /**
     * Set sense
     */
    public function setCountry($country)
    {
        $this->sense = $country;
    }

    /**
     * Get sense
     */
    public function getCountry()
    {
        return $this->country;
    }


    /**
     * Set root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Get root
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Add children
     *
     * @param \NRtworks\BusinessDimensionBundle\Entity\BusinessUnit $children
     */
    public function addChild(\NRtworks\BusinessDimensionBundle\Entity\BusinessUnit $children)
    {
        $this->children[] = $children;
    }

    /**
     * Remove children
     *
     * @param \NRtworks\BusinessDimensionBundle\Entity\BusinessUnit $children
     */
    public function removeChild(\NRtworks\BusinessDimensionBundle\Entity\BusinessUnit $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set customer
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
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'nodes'=> [],
            'parentid'=> $this->parent,
            'root'=>$this->root
        );
    }
    

        
}
?>