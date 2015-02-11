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
 * @ORM\Table(name="Usertype")
 * @ExclusionPolicy("all") 
 */
class Usertype
{
    
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Expose
     */
   
    protected $name;  
    
       /**
     * @ORM\Column(type="string", length=100)
     * @Expose
     */
    
    protected $symfonyRole;
    
    
    
    protected $users;
        
    //getter
    public function getId()
    {
        return $this->id;
    } 
        public function getName()
    {
        return $this->name;
    }  
        public function getSymfonyRole()
    {
        return $this->symfonyRole;
    } 
    
    // setter
      public function setId($x)
    {
        return $this->id=$x;
    } 
        public function setName($x)
    {
        return $this->name=$x;
    }  
        public function setSymfonyRole($x)
    {
        return $this->symfonyRole=$x;
    }      
  

}

?>