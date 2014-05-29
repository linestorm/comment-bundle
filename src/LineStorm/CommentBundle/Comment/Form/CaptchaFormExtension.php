<?php

namespace LineStorm\CommentBundle\Comment\Form;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Provides a form extension for google's recaptcha
 *
 * @see     https://www.google.com/recaptcha/intro/index.html
 *
 * Class CaptchaFormExtension
 *
 * @package LineStorm\CommentBundle\Comment\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CaptchaFormExtension extends AbstractFormExtension implements FormExtensionInterface
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder->add('captcha', 'captcha');
    }

} 
