<?php

namespace LineStorm\CommentBundle\Controller\Api;

use Doctrine\ORM\Query;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use LineStorm\CmsBundle\Controller\Api\AbstractApiController;
use LineStorm\CommentBundle\Model\CommentInterface;

/**
 * Class CommentController
 *
 * @package LineStorm\CommentBundle\Controller\Api
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class CommentController extends AbstractApiController implements ClassResourceInterface
{

    /**
     * Get a comment
     *
     * @param $id
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction($id)
    {
        // get the providers
        $modelManager = $this->get('linestorm.cms.model_manager');
        $comment      = $modelManager->get('comment')->find($id);

        if(!($comment instanceof CommentInterface))
        {
            throw $this->createNotFoundException('Comment Not Found');
        }

        $view = View::create($comment);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

}
