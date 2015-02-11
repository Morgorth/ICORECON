<?php

namespace NRtworks\ChartOfAccountsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AccountFasteditForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('Code','text',array('attr'=>array('class'=>'treeEditingFastEditInput')));
        $builder->add('Name','text',array('attr'=>array('class'=>'treeEditingFastEditInput')));
        
    }

    public function getName()
    {
        return 'AccountFasteditForm';
    }

    /*public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'NRtworks\ChartOfAccountsBundle\Entity\Accounttree',
        );
    }*/
}
?>
