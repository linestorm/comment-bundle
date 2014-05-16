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

        $view = View::create($comment);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function cgetAction()
    {
        // get the providers
        $modelManager = $this->get('linestorm.cms.model_manager');
        $comments      = $modelManager->get('comment')->findAll();

        $view = View::create($comments);

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function postAction()
    {

        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();

        $request = $this->getRequest();
        $form = $this->getForm();

        $formValues = json_decode($request->getContent(), true);

        $form->submit($formValues['linestorm_cms_form_comment']);

        if ($form->isValid()) {

            $em = $modelManager->getManager();
            $now = new \DateTime();

            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthor($user);
            $comment->setCreatedOn($now);

            $em->persist($comment);
            $em->flush();

            /*
            // update the search provider!
            $searchManager = $this->get('linestorm.cms.module.search_manager');
            $commentSearchProvider = $searchManager->get('comment');
            $commentSearchProvider->index($comment);
            */

            $locationPage = array(
                'location' => $this->generateUrl('linestorm_cms_admin_module_comment_edit', array( 'id' => $form->getData()->getId() ))
            );
            $location = array(
                'location' => $this->generateUrl('linestorm_cms_module_comment_api_put_comment', array( 'id' => $form->getData()->getId() ))
            );
            $view = View::create($locationPage, 201, array( 'location' => $location ));
        } else {
            $view = View::create($form);
        }

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param $id
     *
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function putAction($id)
    {

        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();

        $comment = $modelManager->get('comment')->find($id);
        if(!($comment instanceof Comment))
        {
            throw $this->createNotFoundException("Comment not found");
        }

        $request = $this->getRequest();
        $form = $this->getForm($comment);

        $formValues = json_decode($request->getContent(), true);

        $form->submit($formValues['linestorm_cms_form_comment']);

        if ($form->isValid())
        {
            $em = $modelManager->getManager();
            $now = new \DateTime();

            /** @var Comment $updatedComment */
            $updatedComment = $form->getData();
            $updatedComment->setEditedBy($user);
            $updatedComment->setEditedOn($now);

            $em->persist($updatedComment);
            $em->flush();

            // update the search provider!
            $searchManager = $this->get('linestorm.cms.module.search_manager');
            $commentSearchProvider = $searchManager->get('comment');
            $commentSearchProvider->index($updatedComment);

            $view = $this->createResponse(array('location' => $this->generateUrl('linestorm_cms_module_comment_api_get_comment', array( 'id' => $form->getData()->getId()))), 200);
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
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
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
        $searchManager = $this->get('linestorm.cms.module.search_manager');
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
     * @return Response
     * @throws AccessDeniedException
     */
    public function newAction()
    {
        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();
        $comment = $modelManager->create('comment');
        $form = $this->getForm($comment, array(
            'action' => $this->generateUrl('linestorm_cms_module_comment_api_post_comment'),
            'method' => 'POST',
        ));

        $view = $form->createView();

        /** @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper $tpl */
        $tpl = $this->get('templating.helper.form');
        $form = $tpl->form($view);

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
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->getModelManager();
        $comment = $modelManager->find($id);
        $form = $this->getForm($comment, array(
            'action' => $this->generateUrl('linestorm_cms_module_comment_api_put_comment'),
            'method' => 'PUT',
        ));

        $view = $form->createView();

        /** @var \Symfony\Bundle\FrameworkBundle\Templating\Helper\FormHelper $tpl */
        $tpl = $this->get('templating.helper.form');
        $form = $tpl->form($view);

        $rView = View::create(array(
            'form' => $form
        ));

        return $this->get('fos_rest.view_handler')->handle($rView);

    }

}
