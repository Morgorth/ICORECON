<?php

namespace NRtworks\ChartOfAccountsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class accountFullEdit extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('code','text',array('attr'=>array('class'=>'treeView_fullEdit_formElement')));
        $builder->add('name','text',array('attr'=>array('class'=>'treeView_fullEdit_formElement')));
        $builder->add('sense','select',array('attr'=>array('class'=>'treeView_fullEdit_formElement')));
        
    }

    public function getName()
    {
        return 'accountFullEditForm';
    }

    /*public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'NRtworks\ChartOfAccountsBundle\Entity\Accounttree',
        );
    }*/
}
?>
