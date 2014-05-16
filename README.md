Comment Module for LineStormCMS
===============================

Comment Module for LineStormCME.

Installation
============
This module will provide functionality to post blog type content to the LineStorm CMS.

1. Download bundle using composer
2. Enable the Bundle
3. Configure the Bundle
4. Installing Assets
5. Configuring Assets

Step 1: Download bundle using composer
--------------------------------------

Add `linestorm/comment-bundle` to your `composer.json` file, or download it by running the command:

```bash
$ php composer.phar require linestorm/comment-bundle
```

Step 2: Enable the bundle
-------------------------

Enable the media bundle in the `app/AppKernel.php`:

```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new LineStorm\CommentBundle\LineStormCommentBundle(),
    );
}
```

Step 3: Configure the Bundle
----------------------------

Add the default media provider in the linestorm_cms_media namespace inside the `app/config/config.yml` file. The default
is local_storeage

```yml
line_storm_comment: ~
```

Step 4: Installing Assets
-------------------------

###Bower
Add [.bower.json](.bower.json) to the dependencies

###Manual
Download the modules in [.bower.json](.bower.json) to your assets folder



Step 5: Configuring Assets
-------------------------

You will need to add these dependency paths to your requirejs config:

```js
requirejs.config({
    paths: {
        // ...

        // cms comment library
        cms_comment:        '/path/to/bundles/linestormcomment/js/comment',
    }
});
```
