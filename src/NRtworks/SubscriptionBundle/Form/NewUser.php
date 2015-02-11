<?php

namespace NRtworks\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewUser extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username','text');
        $builder->add('email','email');
        $builder->add('plainPassword','password');
    }

    public function getName()
    {
        return 'NewUser';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'NRtworks\SubscriptionBundle\Entity\icousers',
        );
    }
}
?>
