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
     * @ORM\ManyToOne(targetEntity="NRtworks\SubscriptionBundle\Entity\icousers")     
     **/
    private $manager; 
    
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\SubscriptionBundle\Entity\icousers")     
     **/
    private $substitute;  
    
    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\SubscriptionBundle\Entity\icousers")     
     **/
    private $controller;    
    
    
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
        
    public function setParent(BusinessUnit $parent)
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
     * Set manager
     */
    public function setManager(\NRtworks\SubscriptionBundle\Entity\icousers $manager = null)
    {
        $this->manager = $manager;
    }

    /**
     * Get manager
     */
    public function getManager()
    {
        return $this->manager;
    }    

    /**
     * Set substitute
     */
    public function setSubstitute(\NRtworks\SubscriptionBundle\Entity\icousers $substitute = null)
    {
        $this->substitute = $substitute;
    }

    /**
     * Get substitute
     */
    public function getSubstitute()
    {
        return $this->substitute;
    }        

    /**
     * Set controller
     */
    public function setController(\NRtworks\SubscriptionBundle\Entity\icousers $controller = null)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     */
    public function getController()
    {
        return $this->controller;
    }        
    
    /**
     * Set customer
     */
    public function setCustomer(\NRtworks\SubscriptionBundle\Entity\Customer $customer)
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
    
    public function getFieldsParameters()
    {       
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"name","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"code","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[3] = array("fieldName"=>"country","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"no","fieldFilter"=>"no"));
        $info[4] = array("fieldName"=>"root","toDo"=>"noShow","editType"=>"select","options"=>"none");
        $info[5] = array("fieldName"=>"parentid","toDo"=>"noShow","editType"=>"select","options"=>"none");        
        $info[6] = array("fieldName"=>"nodes","toDo"=>"noShow","editType"=>"select","options"=>"none");        
        $info[7] = array("fieldName"=>"manager","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"icousers","fieldFilter"=>array("enabled"=>"1"),"selectFields"=>array("id","username")));        
        $info[8] = array("fieldName"=>"substitute","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"icousers","fieldFilter"=>array("enabled"=>"1"),"selectFields"=>array("id","username")));        
        $info[9] = array("fieldName"=>"controller","toDo"=>"edit","editType"=>"select","options"=>array("remote"=>"icousers","fieldFilter"=>array("enabled"=>"1","type"=>array("value"=>3,"operator"=>">=")),"orderBy"=>"type","selectFields"=>array("id","username")));        
        return $info;
    }
    
    public function arrayalizeForTreeFlatView()
    {
        if(is_object($this->getManager())){$manager = $this->getManager()->getId();}else{$manager = "Not set";}
        if(is_object($this->getSubstitute())){$substitute = $this->getSubstitute()->getId();}else{$substitute = "Not set";}
        if(is_object($this->getController())){$controller = $this->getController()->getId();}else{$controller = "Not set";}
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'code'=> $this->code,
            'country' => $this->country,
            'root'=>$this->root,
            'parentid'=> $this->parent,
            'nodes'=> [],
            'manager'=> $manager,
            'substitute'=>$substitute,
            'controller'=>$controller,
            
        );
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