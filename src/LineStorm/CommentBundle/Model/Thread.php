<?php

namespace LineStorm\CommentBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Thread
 *
 * @package LineStorm\CommentBundle\Model
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
abstract class Thread implements ThreadInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var Comment[]
     */
    protected $comments;

}
