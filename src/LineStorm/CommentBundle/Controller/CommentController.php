<?php

namespace LineStorm\CommentBundle\Controller;

use Doctrine\ORM\Query;
use FOS\RestBundle\View\View;
use LineStorm\CmsBundle\Controller\Api\AbstractApiController;
use LineStorm\CommentBundle\Model\Comment;
use LineStorm\CommentBundle\Model\CommentInterface;
use LineStorm\CommentBundle\Model\ThreadInterface;
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
class CommentController extends AbstractApiController
{
    /**
     * Get a comment for a thread
     *
     * @param string $provider
     * @param int    $thread
     * @param int    $id
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function getCommentAction($provider, $thread, $id)
    {
        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
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
                    'provider' => $provider,
                    'comment'  => $comment,
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

    /**
     * Get all comments in a thread
     *
     * @param string $provider
     * @param int    $thread
     *
     * @return Response
     */
    public function getCommentsAction($provider, $thread)
    {

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $threadClass   = $commentModule->getThreadClass($provider);
        $em            = $this->getDoctrine()->getManager();

        $threadEntity = $em->getRepository($threadClass)->find($thread);

        $dql = "
            SELECT
              partial c.{id,body,createdOn,editedOn,name},
              partial a.{id, username}
            FROM
              {$commentClass} c
              JOIN c.thread t
              LEFT JOIN c.author a
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
                if($threadEntity->getCommentsEnabled())
                {
                    return $this->render('LineStormCommentBundle:Comment:list.html.twig', array(
                        'provider' => $provider,
                        'comments' => $comments,
                    ));
                }
                else
                {
                    return $this->render('LineStormCommentBundle:Comment:disabled.html.twig');
                }

                break;

            case 'json':
            default:
                $comments = $query->getArrayResult();
                $view     = View::create($comments);
                $view->setFormat('json');

                return $this->get('fos_rest.view_handler')->handle($view);
                break;
        }
    }

    /**
     * Create a new comment
     *
     * @param string $provider
     * @param int    $thread
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function postCommentAction($provider, $thread)
    {
        return $this->postComment($provider, $thread);
    }

    public function postCommentReplyAction($provider, $thread, $id)
    {
        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $em            = $this->getDoctrine()->getManager();

        $comment = $em->getRepository($commentClass)->find($id);

        return $this->postComment($provider, $thread, $comment);
    }

    /**
     * Update a comment
     *
     * @param string $provider
     * @param int    $thread
     * @param int    $id
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function putCommentAction($provider, $thread, $id)
    {

        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $em            = $this->getDoctrine()->getManager();

        $comment = $em->getRepository($commentClass)->find($id);
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

            $view = $this->createResponse(array('location' => $this->generateUrl('linestorm_comment_api_get_provider_thread_comment', array(
                    'provider' => $provider,
                    'thread'   => $thread,
                    'id'       => $form->getData()->getId()
                ))), 200);
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
     * @param string $provider
     * @param int    $thread
     * @param int    $id
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function deleteCommentAction($provider, $thread, $id)
    {

        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $em            = $this->getDoctrine()->getManager();

        $comment = $em->getRepository($commentClass)->find($id);
        if(!($comment instanceof Comment))
        {
            throw $this->createNotFoundException("Comment not found");
        }

        $em->remove($comment);
        $em->flush();

        $view = View::create(array(
            'message'  => 'Comment has been deleted',
            'location' => $this->generateUrl('linestorm_comment_api_get_provider_thread_comments', array(
                    'provider' => $provider,
                    'thread'   => $thread,
                )),
        ));

        return $this->get('fos_rest.view_handler')->handle($view);

    }

    /**
     * Get a new comment form
     *
     * @param string $provider
     * @param int    $thread
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function newCommentAction($provider, $thread)
    {
        return $this->newComment($provider, $thread);
    }

    /**
     * We don't need to worry about adding a parent here yet, so just return the form
     *
     * @param string $provider
     * @param int    $thread
     * @param int    $id
     *
     * @return Response
     */
    public function newCommentReplyAction($provider, $thread, $id)
    {
        return $this->newComment($provider, $thread, $id);
    }

    /**
     * Get an edit form
     *
     * @param string $provider
     * @param int    $thread
     * @param int    $id
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function editCommentAction($provider, $thread, $id)
    {
        $user = $this->getUser();
        if(!($user instanceof UserInterface) || !($user->hasGroup('admin')))
        {
            throw new AccessDeniedException();
        }

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $em            = $this->getDoctrine()->getManager();

        $comment = $em->getRepository($commentClass)->find($id);
        $form    = $this->getForm($comment, array(
            'action' => $this->generateUrl('linestorm_comment_api_put_provider_thread_comment', array(
                    'provider' => $provider,
                    'thread'   => $thread,
                    'id'       => $comment->getId(),
                )),
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

    /**
     * @param string $provider
     * @param int    $thread
     * @param null   $parent
     *
     * @return Response
     * @throws AccessDeniedException
     */
    private function postComment($provider, $thread, $parent = null)
    {
        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $threadClass   = $commentModule->getThreadClass($provider);
        $em            = $this->getDoctrine()->getManager();

        /** @var ThreadInterface $threadEntity */
        $threadEntity = $em->getRepository($threadClass)->find($thread);

        $user = $this->getUser();

        if(!$threadEntity->getCommentsEnabled() || !$threadEntity->getCommentsAnonymous() && !$user)
        {
            throw new AccessDeniedException();
        }

        $formType = $this->getCommentForm($threadEntity);
        $request  = $this->getRequest();
        $form     = $this->createForm($formType);

        $formValues = json_decode($request->getContent(), true);

        $form->submit($formValues[$formType]);

        if($form->isValid())
        {
            $now = new \DateTime();

            $threadEntity = $em->getRepository($threadClass)->find($thread);

            /** @var Comment $comment */
            $data    = $form->getData();
            $comment = new $commentClass();
            $comment->setBody($data['body']);
            $comment->setThread($threadEntity);
            $comment->setCreatedOn($now);
            $comment->setIp($request->getClientIp());
            $comment->setAgent($request->headers->get('User-Agent'));

            if(array_key_exists('name', $data))
                $comment->setName($data['name']);

            if($user instanceof UserInterface)
                $comment->setAuthor($user);

            if($parent instanceof Comment)
                $comment->setParent($parent);


            $em->persist($comment);
            $em->flush();

            $tpl          = $this->get('templating');
            $locationPage = array(
                'html'     => $tpl->render('LineStormCommentBundle:Comment:view.html.twig', array(
                        'provider' => $provider,
                        'comment'  => $comment,
                        'thread'   => $thread,
                    )),
                'location' => $this->generateUrl('linestorm_comment_api_get_provider_thread_comment', array(
                        'provider' => $provider,
                        'thread'   => $thread,
                        'id'       => $comment->getId(),
                    ))
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
     * @param string $provider
     * @param int    $thread
     * @param null   $id
     *
     * @return Response
     */
    private function newComment($provider, $thread, $id = null)
    {

        $commentModule = $this->get('linestorm.cms.module.comment.manager');
        $commentClass  = $commentModule->getCommentClass($provider);
        $threadClass   = $commentModule->getThreadClass($provider);
        $em            = $this->getDoctrine()->getManager();

        /** @var ThreadInterface $threadEntity */
        $threadEntity = $em->getRepository($threadClass)->find($thread);

        // check if we have a parent comment id
        $parent = (int)$this->getRequest()->query->get('parent', null);

        $form = '';

        if($threadEntity->getCommentsEnabled())
        {
            $formType = $this->getCommentForm($threadEntity);

            /** @var CommentInterface $comment */
            $comment = new $commentClass();

            if($parent)
            {
                $parentEntity = $em->getRepository($commentClass)->find($parent);
                $comment->setParent($parentEntity);
            }


            $postUrl = $id ? 'linestorm_comment_api_post_provider_thread_comment_reply' : 'linestorm_comment_api_post_provider_thread_comment';

            $form = $this->createForm($formType, $comment, array(
                'action' => $this->generateUrl($postUrl, array(
                        'provider' => $provider,
                        'thread'   => $threadEntity->getId(),
                        'id'       => $id,
                    )),
                'method' => 'POST',
            ));

            $view = $form->createView();

            $tpl  = $this->get('templating');
            $form = $tpl->render('LineStormCommentBundle:Comment:form.html.twig', array(
                'form' => $view
            ));
        }

        $rView = View::create(array(
            'form' => $form
        ));

        return $this->get('fos_rest.view_handler')->handle($rView);
    }

    /**
     * Get the comment form type
     *
     * @param ThreadInterface $thread
     *
     * @return string
     */
    private function getCommentForm(ThreadInterface $thread)
    {
        if($thread->getCommentsAnonymous() && !$this->getUser())
        {
            return 'linestorm_cms_form_comment_anonymous';
        }
        else
        {
            return 'linestorm_cms_form_comment';
        }
    }

}
