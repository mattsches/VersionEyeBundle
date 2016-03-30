VersionEye Bundle
====================

[![Build Status](https://travis-ci.org/mattsches/VersionEyeBundle.png?branch=master)](https://travis-ci.org/mattsches/VersionEyeBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9ce92129-9696-474d-8641-f0645546431a/mini.png)](https://insight.sensiolabs.com/projects/9ce92129-9696-474d-8641-f0645546431a)

Installation
------------

Suggested installation method is through [composer](http://getcomposer.org/):

```php
php composer.phar require mattsches/version-eye-bundle:dev-master
```

Add the bundle to your app/AppKernel.php under the dev environment 
```php
if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            ...
            $bundles[] = new Mattsches\VersionEyeBundle\MattschesVersionEyeBundle();
        }
```

Setup
-----

Add the following to your `app/config/config_dev.yml` (you only want to use this in the dev environment)

```yml
mattsches_version_eye:
    api_key: YOUR_VERSION_EYE_API_KEY_HERE
    # These two are optional
    base_url: "https://www.versioneye.com/api/v2"
    filesystem_cache_path: "%kernel.cache_dir%/versioneye"
    
```

A Word Of Caution
-----------------

Although this plugin caches all requests to the VersionEye servers, it may slow down your project a bit (dev only, of course).
In my project, average response time is up to 400ms slower when there is no cache.


Requirements
------------

This bundle requires Symfony version 2.7 or higher. Sorry.
