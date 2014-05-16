<?php

namespace LineStorm\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AdminController
 *
 * @package LineStorm\CommentBundle\Controller
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
class AdminController extends Controller
{
    /**
     * List all the providers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function listAction()
    {
        $user = $this->getUser();
        if (!($user instanceof UserInterface) || !($user->hasGroup('admin'))) {
            throw new AccessDeniedException();
        }

        return $this->render('LineStormCommentBundle:Admin:list.html.twig');
    }

}
