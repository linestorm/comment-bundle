<?php

namespace LineStorm\CommentBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AnonymousCommentFormType
 *
 * @package LineStorm\CommentBundle\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class AnonymousCommentFormType extends AbstractCommentForm
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => true,
            ))
            ->add('body', 'textarea', array(
                'required' => true,
            ))
        ;

        $extensions = $this->commentManager->getFormExtensions();
        foreach($extensions as $extension)
        {
            $extension->buildForm($builder, $options);
        }

        $builder->add('submit', 'submit');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'linestorm_cms_form_comment_anonymous';
    }
}
