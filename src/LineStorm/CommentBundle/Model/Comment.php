<?php

namespace LineStorm\CommentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @var ThreadInterface
     */
    protected $thread;

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
    protected $editedOn;

    /**
     * @var UserInterface
     */
    protected $editedBy;

    /**
     * @var UserInterface
     */
    protected $deletedBy;

    /**
     * @var \DateTime
     */
    protected $deletedOn;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var Comment
     */
    protected $parent;

    /**
     * @var Comment[]
     */
    protected $children;

    function __construct()
    {
        $this->children = new ArrayCollection();
        $this->deleted = false;
        $this->createdOn = new \DateTime();
    }


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
     * @param ThreadInterface $thread
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param Comment $child
     */
    public function addChild(Comment $child)
    {
        $this->children[] = $child;
    }

    /**
     * @param Comment $child
     */
    public function removeChild(Comment $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @return Comment[]
     */
    public function getChildren()
    {
        return $this->children;
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
     * @param \DateTime $editedOn
     */
    public function setEditedOn(\DateTime $editedOn)
    {
        $this->editedOn = $editedOn;
    }

    /**
     * @return \DateTime
     */
    public function getEditedOn()
    {
        return $this->editedOn;
    }

    /**
     * @param UserInterface $deletedBy
     */
    public function setDeletedBy(UserInterface $deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * @return UserInterface
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param \DateTime $deletedOn
     */
    public function setDeletedOn(\DateTime $deletedOn)
    {
        $this->deletedOn = $deletedOn;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }


}
