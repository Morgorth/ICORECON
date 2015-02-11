<?php

namespace NRtworks\SubscriptionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class NewCustomer extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name','text');
        $builder->add('country','country');
    }

    public function getName()
    {
        return 'NewCustomer';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'NRtworks\SubscriptionBundle\Entity\Customer',
        );
    }
}
?>
