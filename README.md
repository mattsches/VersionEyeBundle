VersionEye Bundle
====================

Installation
------------

Suggested installation method is through [composer](http://getcomposer.org/):

```php
php composer.phar require mattsches/version-eye-bundle:dev-master
```

Setup
-----

Add the following to your `app/config/config_dev.yml` (you only want to use this in the dev environment)

```yml
mattsches_version_eye:
    base_url: "https://www.versioneye.com/api/v1"
    api_key: YOUR_VERSION_EYE_API_KEY_HERE
```
