<?php

namespace LineStorm\CommentBundle\Controller\Api;

use Doctrine\ORM\Query;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use LineStorm\CmsBundle\Controller\Api\AbstractApiController;
use LineStorm\CommentBundle\Model\Comment;
use LineStorm\CommentBundle\Model\CommentInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CommentController
 *
 * @package LineStorm\CommentBundle\Controller\Api
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class ThreadController extends AbstractApiController
{
    /**
     * Get a thread for a provider
     *
     * @param string $type
     * @param int    $id
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function getThreadAction($type, $id)
    {
        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($type);
        $em            = $this->getDoctrine()->getManager();

        $comment = $em->getRepository($commentClass)->find($id);

        if(!($comment instanceof CommentInterface))
        {
            throw $this->createNotFoundException('Comment Not Found');
        }

        switch($this->getRequest()->getRequestFormat())
        {
            case 'html':
                return $this->render('LineStormCommentBundle:Comment:view.html.twig', array(
                    'type'    => $type,
                    'comment' => $comment,
                ));
                break;

            case 'json':
            default:
                $view = View::create($comment);
                $view->setFormat('json');

                return $this->get('fos_rest.view_handler')->handle($view);
                break;
        }
    }
}
