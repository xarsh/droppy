<?php

namespace Droppy\WebApplicationBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CreatorVisibilityFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->setData(true);
    }

    public function getName()
    {
        return 'creator_visible_selector';
    }
    
    public function getParent(array $options)
    {
        return 'choice';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                    'label' => 'schedule.creation.author_display',
                    'choices' => array(true => 'privacy_policy.show',
                    false => 'privacy_policy.hide'),
                    'empty_value' => false, 'expanded' => true,
                    'multiple' => false);
    }
}
