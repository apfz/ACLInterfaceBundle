<?php

namespace Ifgm\ACLInterfaceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserAclFormType extends AbstractType
{
    /**
     * @param string $userClass The user class name
     * @param array $acls
     * @param array $userAcls
     */
    public function __construct($userClass, array $acls, array $userAcls)
    {
        $this->userClass = $userClass;
        $this->acls      = $acls;
        $this->userAcls  = $userAcls;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('acls', 'choice', array(
                'choices' => $this->acls,
                'expanded' => true,
                'multiple' => true,
                'mapped' => false
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->userClass,
        ));
    }

    public function getName()
    {
        return 'ifgm_user_acls';
    }
}
