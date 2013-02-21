VersionEye Bundle
====================

[![Build Status](https://travis-ci.org/mattsches/VersionEyeBundle.png?branch=master)](https://travis-ci.org/mattsches/VersionEyeBundle)

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
    base_url: "https://www.versioneye.com/api/v1"
    filesystem_cache_path: "%kernel.cache_dir%/versioneye"
    api_key: YOUR_VERSION_EYE_API_KEY_HERE
```
