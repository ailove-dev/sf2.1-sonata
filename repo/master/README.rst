Ailove Standard Edition
=======================

What's inside?
--------------

Ailove Standard Edition comes pre-configured with the following bundles:

Symfony Standard Edition
~~~~~~~~~~~~~~~~~~~~~~~~

* FrameworkBundle
* SensioFrameworkExtraBundle
* DoctrineBundle
* TwigBundle
* SwiftmailerBundle
* MonologBundle
* AsseticBundle
* JMSSecurityExtraBundle
* WebProfilerBundle (in dev/test env)
* SensioDistributionBundle (in dev/test env)
* SensioGeneratorBundle (in dev/test env)

Sonata Bundles
~~~~~~~~~~~~~~

* SonataAdminBundle - The missing Symfony2 Admin Generator
* SonataMediaBundle
* SonataUserBundle
* SonataEasyExtendsBundle
* SonataIntlBundle
* SonatajQueryBundle

FOS Bundles
~~~~~~~~~~~

* FOSUserBundle

Behat Bundles
~~~~~~~~~~~~~

* MinkBundle
* BehatBundle

Installation
------------

Run the following commands::

    git clone https://github.com/ailove-dev/sf2.1-sonata.git project
    cd project
    rm -rf .git
    php bin/vendors install
    git init
    git add .
    git commit -m "Initial commit"

.. note::

The ``bin/vendors`` script use composer.phar.

Database initialization
~~~~~~~~~~~~~~~~~~~~~~~

At this point, the ``app/console`` command should start with no issues. However some you need the complete some others step:

* database configuration (edit the ``../../conf/database`` file)

If DB not create run the command::

    app/console doctrine:database:create

For create schema run the command::

    app/console doctrine:schema:create

Add admin user
~~~~~~~~~~~~~~

Run the command::

    app/console fos:user:create admin admin@ailove.ru admin --super-admin

Login to Sonata Admin
~~~~~~~~~~~~~~~~~~~~~

Open http://project-url.lo/admin/login in your browser and fill the authorization form


Enjoy!
