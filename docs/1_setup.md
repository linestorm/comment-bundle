Setting up threads and comments
===============================

The comment bundle consists for two elements: A thread and some comments. A thread is the entity/node on which you are
commenting, and the comments are, well, the comments.

To get started, you will need to add two fields to your entity:
```php

    /**
     * @var boolean
     *
     * @ORM\Column(name="comments_enabled", type="boolean")
     */
    protected $commentsEnabled;

    /**
     * @var boolean
     *
     * @ORM\Column(name="comments_allow_anonymous", type="boolean")
     */
    protected $commentsAnonymous;

    /**
     * @param boolean $commentsAnonymous
     */
    public function setCommentsAnonymous($commentsAnonymous)
    {
        $this->commentsAnonymous = $commentsAnonymous;
    }

    /**
     * @return boolean
     */
    public function getCommentsAnonymous()
    {
        return $this->commentsAnonymous;
    }

    /**
     * @param boolean $commentsEnabled
     */
    public function setCommentsEnabled($commentsEnabled)
    {
        $this->commentsEnabled = $commentsEnabled;
    }

    /**
     * @return boolean
     */
    public function getCommentsEnabled()
    {
        return $this->commentsEnabled;
    }
```

In your content manager, you will then need to create in interface to set/modify these fields. If you are using
LineStormCMS, then you can use the `linestorm/comment-component-bundle`.

Finally, on each page template, you need to include the following script:
```twig
{{ include('LineStormCommentBundle:Comment:async.html.twig', {provider: 'provider_id', id: content.id}) }}
```
