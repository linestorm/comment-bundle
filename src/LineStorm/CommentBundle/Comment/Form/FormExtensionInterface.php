<?php

namespace LineStorm\CommentBundle\Comment\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Interface FormExtensionInterface
 *
 * @package LineStorm\CommentBundle\Comment\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
interface FormExtensionInterface
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return mixed
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array());
} 
