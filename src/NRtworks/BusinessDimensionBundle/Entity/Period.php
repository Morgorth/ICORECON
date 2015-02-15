<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;

/**
 * @ORM\Entity
 * @ORM\Table(name="Period")
 */

    class Period implements JsonSerializable
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
     * @ORM\Column(type="integer", length=3)
     */
   
    protected $number;
    
     /**
     * @ORM\Column(type="string", length=10)
     */
   
    protected $type;    
    
    /**
     * Constructor
     */
    public function __construct($id = NULL)
    {
        //the basic constructor is set to create a default object as we want it when creating a new one
        $this->id = $id;
        $this->name = "New period";
        $this->number = "TBD";
        $this->type = "TBD";
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['name'] = $this->name;
        $result['number'] = $this->number;
        $result['type'] = $this->type;
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
     * Set type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     */
    public function getType()
    {
        return $this->type;
    }
    
    public function getFieldsParameters()
    {
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"name","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[2] = array("fieldName"=>"number","toDo"=>"edit","editType"=>"text","options"=>"none");
        $info[3] = array("fieldName"=>"type","toDo"=>"edit","editType"=>"select","options"=>array(0=>array("value"=>"DAY","text"=>"Day"),1=>array("value"=>"WEEK","text"=>"Week"),2=>array("value"=>"MONTH","text"=>"Month"),3=>array("value"=>"TRIMESTER","text"=>"Trimester"),4=>array("value"=>"SEMESTER","text"=>"Semester"),5=>array("value"=>"YEAR","text"=>"YEAR")));
        return $info;
    }
    
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'type'=>$this->type
        );
    }
  
}
?>