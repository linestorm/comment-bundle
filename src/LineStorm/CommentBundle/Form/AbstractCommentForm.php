<?php

namespace LineStorm\CommentBundle\Form;

use LineStorm\CommentBundle\Comment\CommentManager;
use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractCommentForm
 *
 * @package LineStorm\CommentBundle\Form
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
abstract class AbstractCommentForm extends AbstractType
{

    /**
     * @var CommentManager
     */
    protected $commentManager;

    /**
     * @param $commentManager
     */
    function __construct($commentManager)
    {
        $this->commentManager = $commentManager;
    }

} 
