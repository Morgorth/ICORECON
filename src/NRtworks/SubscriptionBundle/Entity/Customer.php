<?php

namespace NRtworks\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * @ORM\Entity
 * @ORM\Table(name="Customer")
 * @ExclusionPolicy("all") 
 */
class Customer
{
    
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=100, unique = true)
     * @Expose
     */
   
    protected $name;  
    
     /**
     * @ORM\Column(type="string", length=50)
     */
    
    protected $country;
    
     /**
     * @ORM\Column(type="datetime", nullable = false)
     */
    
    protected $datecreation;
    
     /**
     * @ORM\OneToMany(targetEntity="icousers", mappedBy="customer")
     */
    
    protected $users;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }
    
    //getter
    public function getId()
    {
        return $this->id;
    } 
        public function getname()
    {
        return $this->name;
    }  
        public function getcountry()
    {
        return $this->country;
    }
        public function getdatecreation()
    {
        return $this->datecreation;
    }
         public function getadmin_user()
    {
        return $this->admin_user;
    }   
    // setter
      public function setidCustomer($x)
    {
        return $this->idCustomer=$x;
    } 
        public function setname($x)
    {
        return $this->name=$x;
    }  
        public function setcountry($x)
    {
        return $this->country=$x;
    }
        public function setdatecreation($x)
    {
        return $this->datecreation=$x;
    }
        public function setadmin_user($x)
    {
        return $this->admin_user=$x;
    }
}

?>