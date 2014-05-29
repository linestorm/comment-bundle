<?php

namespace LineStorm\CommentBundle\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This class builds the comment form extensions by tags
 *
 * Class ComponentCompilerPass
 *
 * @package LineStorm\CommentComponentBundle\DependencyInjection\ContainerBuilder
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentFormExtensionCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $taggedClasses = $container->findTaggedServiceIds('linestorm.comment.form_extension');

        if(!count($taggedClasses))
        {
            return;
        }

        $moduleDefinition = $container->getDefinition('linestorm.cms.module.comment.manager');

        // inject the component reference array into each module
        foreach($taggedClasses as $mId => $mAttributes)
        {
            $moduleDefinition->addMethodCall(
                'addFormExtension',
                array(new Reference($mId))
            );
        }
    }
} 
