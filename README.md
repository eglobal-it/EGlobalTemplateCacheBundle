# EGlobalTemplateCacheBundle

[![Build Status](https://travis-ci.org/eglobal-it/EGlobalTemplateCacheBundle.svg?branch=master)](https://travis-ci.org/eglobal-it/EGlobalTemplateCacheBundle)

## Installation

Install this bundle using Composer. Add the following to your composer.json:

```json
{
    "require": {
        "eglobal/template-cache-bundle": "^3.4"
    }
}
```

Register bundle in the app/AppKernel.php:

```php
public function registerBundles()
{
    $bundles = [
        // ...
        new EGlobal\Bundle\TemplateCacheBundle\EGlobalTemplateCacheBundle(),
    ];
}
```

Update config:

```yaml
parameters:
    locales:
        - en
        - es
        - de

# If using Assetic, add the bundle to the config
assetic:
    bundles:
        - EGlobalTemplateCacheBundle

eglobal_template_cache:
    # Locales to be cached
    locales: "%locales%"
    # Cache only exposed routes
    exposed_routes_only: false
    # Public directory to store cached templates
    cache_dir: '%kernel.root_dir%/../web/templates'
    # Public prefix of cached templates
    public_prefix: '/templates'
    # Directories to search cacheable templates in
    root_dirs:
        - "@AcmeFooBundle/Controller"
        - "@AcmeBarBundle/Controller/Cacheable"
```

## Example usage

Mark controller routes as cacheable

```php
<?php

namespace Acme\FooBundle\Controller;

use EGlobal\Bundle\TemplateCacheBundle\Annotation\CacheableTemplate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MyController extends Controller
{
    /**
     * @Method("GET")
     * @Route("/my/foo.html", name="my.template.foo", options={"expose"=true})
     * @CacheableTemplate("AcmeFooBundle:Template:foo.html.twig")
     */
    public function fooAction()
    {
        // Your controller logic
    }
    
    /**
     * @Method("GET")
     * @Route("/my/bar.svg", name="my.template.bar", options={"expose"=true})
     * @CacheableTemplate("AcmeFooBundle:Template:bar.svg.twig")
     */
    public function barAction()
    {
        // Your controller logic
    }
}
```

Dump templates into cache files

```bash
$ php bin/console eglobal:template-cache:dump
```

Add assets to your template

```twig
...
<head>
    <script type="text/javascript" src="{{ asset('bundles/eglobaltemplatecache/js/template-cache.js') }}"></script>
    
    {% if not app.debug %}
        <script type="text/javascript" src="{{ asset(jsTemplateMapFileName(app.request.locale)) }}"></script>
    {% endif %}
</head>
...
```

Use cacheable template paths in your code

```js
// This will return some path like '/templates/en/f9d15a8be554432de01799a8c51d123f.html'
var fooCachedUrl = TemplateCache.get('my.template.foo');

// This will return some path like '/templates/en/f9d15a8be554432de01799a8c51d123f.svg'
var barCachedUrl = TemplateCache.get('my.template.bar');
```
