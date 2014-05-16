<?php

namespace LineStorm\CommentBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class Comment
 *
 * @package LineStorm\CommentBundle\Model
 * @author  Andy Thorne <contrabandvr@gmail.com>
 */
abstract class Comment implements CommentInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var UserInterface
     */
    protected $author;

    /**
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @var \DateTime
     */
    protected $editiedOn;

    /**
     * @var UserInterface
     */
    protected $editedBy;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var Comment
     */
    protected $parent;

    /**
     * @var Comment
     */
    protected $child;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    /**
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param Comment $child
     */
    public function setChild(Comment $child)
    {
        $this->child = $child;
    }

    /**
     * @return Comment|null
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * @param Comment $parent
     */
    public function setParent(Comment $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return Comment|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \DateTime $createdOn
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param UserInterface $editedBy
     */
    public function setEditedBy(UserInterface $editedBy)
    {
        $this->editedBy = $editedBy;
    }

    /**
     * @return UserInterface
     */
    public function getEditedBy()
    {
        return $this->editedBy;
    }

    /**
     * @param \DateTime $editiedOn
     */
    public function setEditiedOn(\DateTime $editiedOn)
    {
        $this->editiedOn = $editiedOn;
    }

    /**
     * @return \DateTime
     */
    public function getEditiedOn()
    {
        return $this->editiedOn;
    }



}
