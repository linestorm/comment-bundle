<?php

namespace LineStorm\CommentBundle;

use LineStorm\CmsBundle\DependencyInjection\ContainerBuilder\DoctrineOrmCompilerPass;
use LineStorm\CommentBundle\DependencyInjection\ContainerBuilder\CommentFormExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class LineStormCommentBundle
 *
 * @package LineStorm\CommentBundle
 */
class LineStormCommentBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $modelDir = realpath(__DIR__.'/Resources/config/model/doctrine');
        $mappings = array( $modelDir => 'LineStorm\CommentBundle\Model' );
        $container->addCompilerPass(DoctrineOrmCompilerPass::getMappingsPass($mappings));

        $container->addCompilerPass(new CommentFormExtensionCompilerPass());
    }
}
