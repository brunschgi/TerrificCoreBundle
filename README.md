# TerrificCoreBundle

The **TerrificCore** bundle makes it easy to develop frontends based on the [Terrific Concept](http://terrifically.org).
It provides you a good starting point and useful features (eg. additional assetic filters) to streamline your frontend development.

The TerrificCore bundle has no dependencies but works best in combination with the [TerrificComposerBundle](https://github.com/brunschgi/TerrificComposerBundle).
For installation of the TerrificComposerBundle, please follow the instructions [there](https://github.com/brunschgi/TerrificComposerBundle).

## Installation

TerrificCoreBundle can be conveniently installed via Composer. Just add the following to your composer.json file:

    // composer.json
    {
        // ...
        require: {
            // ...
            "brunschgi/terrific-core-bundle": "dev-master"
        }
    }

Note: Please replace dev-master in the snippet above with the latest stable branch, for example 1.0.*. Please check the tags on Github for which versions are available.
Then, you can install the new dependencies by running Composer’s update command from the directory where your composer.json file is located:

    php composer.phar update

Now, Composer will automatically download all required files, and install them for you. All that is left to do is to update your AppKernel.php file, and register the new bundle:

    // in AppKernel::registerBundles()
    public function registerBundles()
    {
        return array(
            // ...
            new Terrific\CoreBundle\TerrificCoreBundle(),
        );
    }

Enable the new terrificrewrite filter in `app/config/config.yml`:

    # app/config/config_dev.yml
    # extend assetic filter configuration (rewrite of the backround image path in your terrific modules)
    assetic:
        …
        filters:
            …
            terrificrewrite:
                resource: %kernel.root_dir%/../vendor/brunschgi/terrific-core-bundle/Terrific/CoreBundle/Resources/config/terrificrewrite.xml


Enable the bundle's configuration in `app/config/config_dev.yml`:

    # app/config/config_dev.yml
    terrific_core:
        copy_images: true # copy your module images on the fly without running `assets:install web`


## Usage

To see the TerrificComposerCore in action, download the [Terrific Composer Distribution](http://terrifically.org/composer)
and play around with the included examples. For more information about the Terrific Concept, please have a look at [http://terrifically.org](http://terrifically.org)

After that, the below should be pretty straight forward ;-)


### Base Layout

The base twig layout provides you everything you need to start with your Terrific project right away.
Simple extend the base layout from your project layout:

``` php

{# eg. src/Terrific/Composition/Resources/views/base.html.twig #}
{% extends 'TerrificCoreBundle::base.html.twig' %}
...
```

The core layout provides you with several twig blocks to extend or overwrite. The most important ones are:

``` php
    {# main content of your page #}
    {% block composition %}here comes the content of the &lt;body&gt; element…{% endblock %}

    {# content of the &lt;title&gt; element #}
    {% block title %}Terrific Composer{% endblock %}

    {# placeholder for your meta tags (charset is always set to utf-8) #}
    {% block meta %}here comes your meta tags…{% endblock %}

    {# styles #}
    {% block styles %}
        {% stylesheets
            '@TerrificComposition/Resources/public/css/reset.less'
            '@TerrificComposition/Resources/public/css/grid.less'
            '@TerrificComposition/Resources/public/css/elements.less'
            output="css/compiled/project.css"
        %}
            <link rel="stylesheet" href="{{ asset_url }}" />
        {% endstylesheets %}

        {# styles from parent (terrific core) layout #}
        {{ parent() }}
    {% endblock %}

    {# scripts #}
    {% block scripts %}
        {% javascripts
            '../src/Terrific/Module/*/Resources/public/js/*.js'
            '../src/Terrific/Module/*/Resources/public/js/skin/*.js'
            output='js/compiled/base.js'
        %}
            <script src="{{ asset_url }}" type="text/javascript"></script>
        {% endjavascripts %}
    {% endblock %}
```

For a full list of available blocks, please have a look at @TerrificCoreBundle::base.html.twig.


### Module Macro

Every [Terrific Module](http://terrifically.org) is a separate bundle. The module macro makes it easy to mix and match them
on your page. It works similar as the built-in twig helpers `include` and `render` and wraps your included / embedded module templates
in the appropriate module `<div>`, eg. `<div class="mod mod-news" data-connectors="update">... your template ...</div>`.

*Including Module Templates*

    {# wrap & include the view template /src/Terrific/Module/Teaser/Resources/views/default.html.twig #}
    {{ tc.module('Teaser', 'default') }}

    {# wrap & include the view template /src/Terrific/Module/Teaser/Resources/views/Concept/reusability.html.twig #}
    {{ tc.module('Teaser', 'Concept/reusability') }}


*Embedding Module Controller*

If you are building not just templates but real applications with Terrific, it might be useful to delegate all the data
stuff to the module itself so that you don't have to repeat yourself.

    {# wrap & embed the module controller /src/Terrific/Module/Navigation/Controller/NavigationController.php -> mainAction #}
    {{ tc.module('Navigation', 'Navigation:main') }}


The module macro can take some more parameters than just the module name and the view.

    {% macro module(name, view, skins, connectors, attrs, data) %}

For more detailed infos, please have a look at Twig/Extension/terrificcore.html.twig.

That's it… Enjoy!