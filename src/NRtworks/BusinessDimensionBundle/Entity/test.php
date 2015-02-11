<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;
    use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="test")
 */

    class test implements JsonSerializable
    {
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
   
    protected $name;  

     /**
     * @ORM\Column(type="string", length=100)
     */
    
    
    protected $description;
    

    
    /**
     * Constructor
     */
    public function __construct($id = NULL)
    {
        //the basic constructor is set to create a default object as we want it when creating a new one
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->id = $id;
        $this->name = "Chart Of Accounts";
        $this->description = "Default chart of accounts";
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['name'] = $this->name;
        $result['description'] = $this->code;
        return $result;
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