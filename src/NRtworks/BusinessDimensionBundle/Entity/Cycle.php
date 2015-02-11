<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="Cycle")
 */

    class Cycle implements JsonSerializable
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
        $this->id = $id;
        $this->name = "New cycle";
        $this->description = "new cycle";
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
    
    public function getFieldsParameters()
    {
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"name","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"description","toDo"=>"edit","editType"=>"text","options"=>"none");
        return $info;
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