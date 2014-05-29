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
class CommentController extends AbstractApiController implements ClassResourceInterface
{
    /**
     * Creates a Posy type form
     *
     * @param null|CommentInterface $entity
     * @param array                 $options
     *
     * @return Form
     */
    private function getForm($entity = null, array $options = array())
    {
        return $this->createForm('linestorm_cms_form_comment', $entity, $options);
    }

    /**
     * Get a comment
     *
     * @param $id
     *
     * @throws NotFoundHttpException
     * @return Response
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

        switch($this->getRequest()->getRequestFormat())
        {
            case 'html':
                return $this->render('LineStormCommentBundle:Comment:view.html.twig', array(
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

    public function getThreadAction($name, $thread)
    {

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($name);
        $em            = $this->getDoctrine()->getManager();

        $dql = "
            SELECT
              partial c.{id,body,createdOn,editedOn},
              partial a.{id, username}
            FROM
              {$commentClass} c
              JOIN c.author a
              JOIN c.thread t
            WHERE
              t.id = ?1
              AND c.deleted = 0
            ORDER BY
              c.createdOn ASC
        ";

        $query = $em->createQuery($dql)->setParameter(1, $thread);

        switch($this->getRequest()->getRequestFormat())
        {
            case 'html':
                $comments = $query->getResult();
                return $this->render('LineStormCommentBundle:Comment:list.html.twig', array(
                    'comments' => $comments,
                ));
                break;

            case 'json':
            default:
                $comments = $query->getArrayResult();
                $view = View::create($comments);
                $view->setFormat('json');
                return $this->get('fos_rest.view_handler')->handle($view);
                break;
        }
    }


    /**
     * Get all comments
     *
     * @return Response
     */
    public function cgetAction()
    {
        // get the providers
        $modelManager = $this->get('linestorm.cms.model_manager');
        $comments     = $modelManager->get('comment')->findAll();

        $view = View::create($comments);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Create a new comment
     *
     * @param $name
     * @param $thread
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function postThreadAction($name, $thread)
    {

        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();

        $request = $this->getRequest();
        $form    = $this->getForm();

        $formValues = json_decode($request->getContent(), true);

        $form->submit($formValues['linestorm_cms_form_comment']);

        if($form->isValid())
        {

            $em  = $modelManager->getManager();
            $now = new \DateTime();

            $commentModule = $this->get('linestorm.cms.module.comment.manager');
            $commentClass  = $commentModule->getCommentClass($name);
            $threadClass   = $commentModule->getThreadClass($name);
            $threadEntity  = $em->getRepository($threadClass)->find($thread);

            /** @var Comment $comment */
            $data    = $form->getData();
            $comment = new $commentClass();
            $comment->setBody($data['body']);
            $comment->setThread($threadEntity);
            $comment->setAuthor($user);
            $comment->setCreatedOn($now);

            $em->persist($comment);
            $em->flush();

            $tpl = $this->get('templating');
            $locationPage = array(
                'html'     => $tpl->render('LineStormCommentBundle:Comment:view.html.twig', array('comment' => $comment)),
                'location' => $this->generateUrl('linestorm_cms_module_comment_api_get_comment', array('id' => $comment->getId()))
            );
            $view         = View::create($locationPage, 201);
        }
        else
        {
            $view = View::create($form);
        }

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Update a comment
     *
     * @param $id
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function putAction($id)
    {

        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();

        $comment = $modelManager->get('comment')->find($id);
        if(!($comment instanceof Comment))
        {
            throw $this->createNotFoundException("Comment not found");
        }

        $request = $this->getRequest();
        $form    = $this->getForm($comment);

        $formValues = json_decode($request->getContent(), true);

        $form->submit($formValues['linestorm_cms_form_comment']);

        if($form->isValid())
        {
            $em  = $modelManager->getManager();
            $now = new \DateTime();

            /** @var Comment $updatedComment */
            $updatedComment = $form->getData();
            $updatedComment->setEditedBy($user);
            $updatedComment->setEditedOn($now);

            $em->persist($updatedComment);
            $em->flush();

            // update the search provider!
            $searchManager         = $this->get('linestorm.cms.module.search_manager');
            $commentSearchProvider = $searchManager->get('comment');
            $commentSearchProvider->index($updatedComment);

            $view = $this->createResponse(array('location' => $this->generateUrl('linestorm_cms_module_comment_api_get_comment', array('id' => $form->getData()->getId()))), 200);
        }
        else
        {
            $view = View::create($form);
        }

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Delete a comment
     *
     * @param $id
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function deleteAction($id)
    {

        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();

        $comment = $modelManager->get('comment')->find($id);
        if(!($comment instanceof Comment))
        {
            throw $this->createNotFoundException("Comment not found");
        }

        $em = $modelManager->getManager();

        // remove indexes
        $searchManager         = $this->get('linestorm.cms.module.search_manager');
        $commentSearchProvider = $searchManager->get('comment');
        $commentSearchProvider->remove($comment);

        $em->remove($comment);
        $em->flush();

        $view = View::create(array(
            'message'  => 'Comment has been deleted',
            'location' => $this->generateUrl('linestorm_cms_module_comment_api_get_comments'),
        ));

        return $this->get('fos_rest.view_handler')->handle($view);

    }

    /**
     * Get a new comment form
     *
     * @param $name
     * @param $thread
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function newThreadAction($name, $thread)
    {
        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $commentModule = $this->get('linestorm.cms.module.comment.manager');

        $commentClass = $commentModule->getCommentClass($name);
        $threadClass  = $commentModule->getThreadClass($name);

        $em           = $this->getDoctrine()->getManager();
        $threadEntity = $em->getRepository($threadClass)->find($thread);

        $comment = new $commentClass();
        $form    = $this->getForm($comment, array(
            'action' => $this->generateUrl('linestorm_cms_module_comment_api_post_comment_thread', array(
                    'name'   => $name,
                    'thread' => $threadEntity->getId(),
                )),
            'method' => 'POST',
        ));

        $view = $form->createView();

        $tpl  = $this->get('templating');
        $form = $tpl->render('LineStormCommentBundle:Comment:form.html.twig', array(
            'form' => $view
        ));

        $rView = View::create(array(
            'form' => $form
        ));

        return $this->get('fos_rest.view_handler')->handle($rView);

    }

    /**
     * @param $id
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function editAction($id)
    {
        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();
        $comment      = $modelManager->find($id);
        $form         = $this->getForm($comment, array(
            'action' => $this->generateUrl('linestorm_cms_module_comment_api_put_comment'),
            'method' => 'PUT',
        ));

        $view = $form->createView();

        /** @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper $tpl */
        $tpl  = $this->get('templating.helper.form');
        $form = $tpl->form($view);

        $rView = View::create(array(
            'form' => $form
        ));

        return $this->get('fos_rest.view_handler')->handle($rView);

    }

}
