<?php

namespace LineStorm\CommentBundle\Model;

/**
 * Interface ThreadInterface
 *
 * @package LineStorm\CommentBundle\Model
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
interface ThreadInterface
{

    /**
     * @param boolean $commentsAnonymous
     */
    public function setCommentsAnonymous($commentsAnonymous);

    /**
     * @return boolean
     */
    public function getCommentsAnonymous();

    /**
     * @param boolean $commentsEnabled
     */
    public function setCommentsEnabled($commentsEnabled);

    /**
     * @return boolean
     */
    public function getCommentsEnabled();

} 
