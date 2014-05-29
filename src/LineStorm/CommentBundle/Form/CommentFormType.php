<?php

namespace LineStorm\CommentBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class CommentFormType
 *
 * @package LineStorm\CommentBundle\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentFormType extends AbstractCommentForm
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
        return 'linestorm_cms_form_comment';
    }
}
