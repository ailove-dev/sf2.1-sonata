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

Let's guess you have project named "project". To create project run the following commands::

    mkdir project
    cd project
    mkdir cache conf data repo tmp logs
    echo -e "DB_HOST = 127.0.0.1\nDB_NAME = sf21sonata\nDB_USER = user\nDB_PASSWORD = " > conf/database
    echo -e "ok_app_id=1\nok_app_secret=1\nok_public_key=1\nvk_app_id=1\nvk_app_secret=1\nfb_app_id=1\nfb_app_secret=1" > conf/social
    chmod 777 *
    git clone https://github.com/ailove-dev/sf2.1-sonata.git repo/dev
    cd repo/dev
    rm -rf .git
    git init
    git add .gitignore *
    git commit -m "Initial commit"
    curl -s http://getcomposer.org/installer | php
    php composer.phar install

About directories structure
~~~~~~~~~~~~~~~~~~~~~~~

* cache - for framework cache
* conf - host independed configuration INI files parsed by app/config/factory.php file
* data - directory for uploaded files. Use directory alias for virtual host Alias /data /path/to/project/data
* repo - this directory is used to store git repo. We have placed it into repo/dev directory. Your virtual host should use repo/dev/htdocs as a document directory in this case
* tmp - use this dir to store tmp files as session and etc.
* logs - store the logs here

.. note::

The ``bin/vendors`` script use composer.phar.

Database initialization
~~~~~~~~~~~~~~~~~~~~~~~

At this point, the ``app/console`` command should start with no issues. However some you need the complete some others step:

* database configuration (create the ``project/conf/database`` file)

Database file example::

    DB_HOST = 127.0.0.1
    DB_NAME = sf21sonata
    DB_USER = user
    DB_PASSWORD =

If DB was not created run the command::

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

Apache config for Liip
~~~~~~~~~~~~~~~~~~~~~~

    RewriteEngine On
    RewriteCond %{REQUEST_URI} ^/data/
    RewriteCond /path/to/project/%{SCRIPT_FILENAME} !-s
    RewriteRule ^/(.*)$ http://%{HTTP_HOST}/app.php/$1 [QSA,L]

Enjoy!
