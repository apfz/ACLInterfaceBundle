<?php

namespace Ifgm\ACLInterfaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AclFormType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('users', 'collection', array(
                'type' => new UserAclFormType($options['acls'], $options['userAcls'])
            ))
            ->add('submit', 'submit', array('label' => 'Save'));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'acls' => array(),
            'users' => array(),
            'userAcls' => array()
        ));
    }

    public function getName()
    {
        return 'ifgm_acl';
    }
}
