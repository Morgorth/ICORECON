<?php


    namespace NRtworks\BusinessDimensionBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use Doctrine\Common\Collections\ArrayCollection;
    use JsonSerializable;


    class Version implements JsonSerializable
    {
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */

    protected $id;

    /**
     * @ORM\Column(type="integer", length=3)
     */
   
    protected $number;  


    /**
     * Constructor
     */
    public function __construct($id = NULL)
    {
        //the basic constructor is set to create a default object as we want it when creating a new one
        $this->id = $id;
        $this->number = 1;
    }


    //this method returns an array with default values
    public function getDefaultObject()
    {
        $result = Array();
        $result['id'] = $this->id;
        $result['number'] = $this->number;
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

    public function getFieldsParameters()
    {
        $info[0] = array("fieldName"=>"id","toDo"=>"noShow","editType"=>"text","options"=>"none");
        $info[1] = array("fieldName"=>"number","toDo"=>"show","editType"=>"text","options"=>"none");
        return $info;
    }
    
    
    public function jsonSerialize()
    {
        return array(
            'id' =>  $this->id,
            'number' => $this->name,
        );
    }
  
}
?>