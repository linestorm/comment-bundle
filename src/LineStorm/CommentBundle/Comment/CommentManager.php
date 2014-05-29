<?php

namespace LineStorm\CommentBundle\Comment;
use LineStorm\CommentBundle\Comment\Exception\CommentConfigNotFoundException;

/**
 * Class CommentManager
 *
 * @package LineStorm\CommentBundle\Comment
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentManager
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Returns the config for the given config name
     *
     * @param $name
     *
     * @return mixed
     * @throws Exception\CommentConfigNotFoundException
     */
    public function getConfig($name)
    {
        if(!array_key_exists($name, $this->config))
        {
            throw new CommentConfigNotFoundException($name);
        }

        return $this->config[$name];
    }

    /**
     * Returns the class of the thread for the given config name
     *
     * @param $name
     *
     * @return mixed
     * @throws Exception\CommentConfigNotFoundException
     */
    public function getThreadClass($name)
    {
        if(!array_key_exists($name, $this->config))
        {
            throw new CommentConfigNotFoundException($name);
        }

        return $this->config[$name]['thread'];
    }

    /**
     * Returns the class of the comment for the given config name
     *
     * @param $name
     *
     * @return mixed
     * @throws Exception\CommentConfigNotFoundException
     */
    public function getCommentClass($name)
    {
        if(!array_key_exists($name, $this->config))
        {
            throw new CommentConfigNotFoundException($name);
        }

        return $this->config[$name]['comment'];
    }

} 
