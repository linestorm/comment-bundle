<?php

namespace LineStorm\CommentBundle\Module;

use LineStorm\CmsBundle\Module\AbstractModule;
use LineStorm\CmsBundle\Module\ModuleInterface;
use Symfony\Component\Config\Loader\Loader;

/**
 * Class CommentModule
 *
 * @package LineStorm\CommentBundle\Module
 */
class CommentModule extends AbstractModule implements ModuleInterface
{
    protected $name = 'Comment';
    protected $id = 'comment';

    /**
     * Returns the navigation array
     *
     * @return array
     */
    public function getNavigation()
    {
        return array(
            'View Comments' => array('linestorm_cms_admin_module_comment_list', array())
        );
    }

    /**
     * The route to load as 'home'
     *
     * @return string
     */
    public function getHome()
    {
        return 'linestorm_cms_admin_module_comment_list';
    }

    /**
     * @inheritdoc
     */
    public function addRoutes(Loader $loader)
    {
        return $loader->import('@LineStormCommentBundle/Resources/config/routing/frontend.yml', 'rest');
    }

    /**
     * @inheritdoc
     */
    public function addAdminRoutes(Loader $loader)
    {
        return $loader->import('@LineStormCommentBundle/Resources/config/routing/admin.yml', 'yaml');
    }
} 
