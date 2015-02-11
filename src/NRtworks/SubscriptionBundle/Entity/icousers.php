<?php

namespace NRtworks\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

use FOS\UserBundle\Model\User;


/**
 * @ORM\Entity
 * @ORM\Table(name="icousers")
 * @ExclusionPolicy("all") 
 */
class icousers extends User
{
    
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    
    protected $id;
    
    /**
     * @ManytoOne(targetEntity="Usertype")
     * @Expose
     */
        
    protected $type;
 
    /**
     * @ManytoOne(targetEntity="Customer",inversedBy="users")
     * @Expose
     */
    
    protected $customer;
       
    public function getId()
    {
        return $this->id;
    } 
     
    public function getType()
    {
        return $this->type;
    }          
    public function getCustomer()
    {
        return $this->customer;
    }        
    
    public function setId($x)
    {
        return $this->id=$x;
    } 
    public function setType($x)
    {
        return $this->type=$x;
    } 
     public function setCustomer($x)
    {
        return $this->customer=$x;
    } 
        
    
}

?>