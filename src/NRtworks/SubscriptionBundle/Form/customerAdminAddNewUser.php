<?php

namespace NRtworks\SubscriptionBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class customerAdminAddNewUser extends BaseType
{

     public function __construct()
    {
        
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('type','choice',array(
            'choices' => array('ROLE_BASIC_USER'=>'User','ROLE_BASIC_CONTROLLER'=>'Controller','ROLE_ACCOUNT_Manager'=>'Account Manager'),'required' => true,
            
        ));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NRtworks\SubscriptionBundle\Entity\icousers',
        ));
    }
    
    public function getName()
    {
        return 'customerAdmin_addNewUser';
    }
}



?>
