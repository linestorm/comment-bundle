<?php

namespace LineStorm\CommentBundle\Form;

use LineStorm\CommentBundle\Comment\CommentManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CommentFormType
 *
 * @package LineStorm\CommentBundle\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body', 'textarea', array(
                'required' => true,
                'label'    => false,
            ))
            ->add('submit', 'submit')
        ;

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'linestorm_cms_form_comment';
    }
}
