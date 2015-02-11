<?php

    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;
    use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Account")
 * @ORM\Entity
 */

    class Account implements JsonSerializable
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
     * @ORM\Column(type="string", length=2)
     */
    
    protected $sense;
    
     /**
     * @ORM\Column(name="root", type="integer",nullable=true)
     */
    protected $root;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Account", mappedBy="parent")
     */
    protected $children;


    /**
     * @ORM\ManyToOne(targetEntity="NRtworks\BusinessDimensionBundle\Entity\ChartOfAccounts", inversedBy="accounts")
     * @ORM\JoinColumn(name="chartofaccounts_id", referencedColumnName="id")
     */
     
    private $chartofaccount;

    /**
     * Constructor
     */
    public function __construct($id = NULL)
    {
        //the basic constructor is set to create a default object as we want it when creating a new one
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->id = $id;
        $this->name = "New account";
        $this->code = "xxxxxx";
        $this->sense = "DR";   
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
        $result['sense'] = $this->sense;
        $result["action"] = "new";
        $result["root"] = 0;
        $result["nodes"] = [];
        return $result;
    }
    
    //this method return the list of the field's name to be edited in the tree editing
    public function fieldsToEditinTreeEdit()
    {
        $toEdit[0] = array(0=>"id",1=>0,2=>"text");
        $toEdit[1] = array(0=>"name",1=>1,2=>"text");
        $toEdit[2] = array(0=>"code",1=>1,2=>"text");
        $toEdit[3] = array(0=>"sense",1=>1,2=>"select",3=>array(0=>array("value"=>"DR","text"=>"DR"),1=>array("value"=>"CR","text"=>"CR")));        
        $toEdit[4] = array(0=>"root",1=>0,2=>"text");
        $toEdit[5] = array(0=>"nodes",1=>0,2=>"text");                
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

   /**
     * Set sense
     */
    public function setSense($sense)
    {
        $this->sense = $sense;
    }

    /**
     * Get sense
     */
    public function getSense()
    {
        return $this->sense;
    }
    
    public function setParent(Account $parent=null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
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
     * @param \NRtworks\BusinessDimensionBundle\Entity\Account $children
     * @return Account
     */
    public function addChild(\NRtworks\BusinessDimensionBundle\Entity\Account $children)
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * Remove children
     *
     * @param \NRtworks\BusinessDimensionBundle\Entity\Account $children
     */
    public function removeChild(\NRtworks\BusinessDimensionBundle\Entity\Account $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set COA
     */
    public function setChartOfAccount(\NRtworks\BusinessDimensionBundle\Entity\ChartOfAccounts $chartofaccount = null)
    {
        $this->chartofaccount = $chartofaccount;
    }

    /**
     * Get COA
     */
    public function getChartOfAccount()
    {
        return $this->chartofaccount;
    }
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'sense' =>$this->sense,
            'nodes'=> [],
            'parentid'=> $this->parent,
            'root'=>$this->root
        );
    }
    
    // ALL METHODS BELOW ARE OPTIONS FOR THE TREE EDITING 
        
}
?>