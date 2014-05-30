<?php

namespace LineStorm\CommentBundle\Controller;

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
 * Class ProviderController
 *
 * @package LineStorm\CommentBundle\Controller\Api
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class ProviderController extends AbstractApiController
{
    /**
     * Get a comment provider
     *
     * @param string $id
     *
     * @throws NotFoundHttpException
     *
     * @return Response
     */
    public function getProviderAction($id)
    {
        throw $this->createNotFoundException("Not yet implemented");
    }
}
