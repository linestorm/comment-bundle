<?php

namespace LineStorm\CommentBundle\Comment\Exception;
use Exception;

/**
 * Class CommentConfigNotFoundException
 *
 * @package LineStorm\CommentBundle\Comment\Exception
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentConfigNotFoundException extends \Exception
{
    public function __construct($name, Exception $previous = null)
    {
        parent::__construct("Config not found: {$name}", null, $previous);
    }
} 
