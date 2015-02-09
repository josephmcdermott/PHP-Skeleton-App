![alt text](http://halusanation.com/wp-content/uploads/2014/12/php_skeleton_app.jpg "The PHP Skeleton App Header Image")

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ghalusa/PHP-Skeleton-App/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ghalusa/PHP-Skeleton-App/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/ghalusa/PHP-Skeleton-App/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ghalusa/PHP-Skeleton-App/build-status/master)

# The PHP Skeleton App

The PHP Skeleton App creates an object-oriented PHP MVC environment, tailored for rapid development. The environment makes use of the [Slim PHP micro framework](http://slimframework.com/), [Twig](http://twig.sensiolabs.org/) for templating, and [Twitter Bootstrap 3](http://getbootstrap.com/) for the theme.

Out of the box, it provides the ability to set up and run a public-facing website, authenticate to the administrative side, and create users and groups. All of the baseline database tables are created on first run, along with the first "Universal Administrator".

## This is an Initial Pre-Release of The PHP Skeleton App ##

This is my first go at publishing something extensive on GitHub. I'm far from perfect, and have been in somewhat of a bubble. Regardless, I have decided to get my feet wet so I can collect feedback from the community, and grow as a developer. So, your constructive criticism is encouraged. I'll do my best to learn from any mistakes and deliver the goods.

### Roadmap ###

* While the application works, it admittedly requires some refinements to get it to a level I'm happy with, mainly with the architecture and coding standards.

* The goal is to get the codebase to a level which conforms to the [Framework Interoperability Group's](http://www.php-fig.org/) (PHP-FIG) [PHP Standard Recommendation](http://www.php-fig.org/faq/#what-does-psr-stand-for) (PSR).

* That is to say, the codebase is heavily under development and currently evolving. Use if you wish, but know that there may be some big changes along the way. I will try to keep things backwards-compatible, but there are no guarantees until the codebase meets the aforementioned benchmarks.

Thanks!

-Gor

* * *

## Installing the PHP Skeleton App on DigitalOcean (the complete guide)

![alt text](http://halusanation.com/wp-content/uploads/2014/12/DO_Logo_325x51.png "DigitalOcean logo")

<a href="https://github.com/ghalusa/PHP-Skeleton-App/wiki/Installing-the-PHP-Skeleton-App-on-Digital-Ocean-(the-complete-guide)">View the Guide</a>

* * *

## Features

* **Quick 5-minute installation**

    (Provided the server environment is set up)

* **Simple configuration**

* **Easy templating with custom views using Twig**

* **Twitter Bootstrap 3.3.x**

    "Carousel" template included for the public website

    "Dashboard" template included for the administrative interface

* **Site Module**

    The public site

* **Authenticate Module**

    With local authentication, out-of-the-box. Oauth schemes coming soon (e.g. Twitter, Google, Facebook, Github).

* **User Account Module**

    For user management, complete with a self-registration form and the ability to reset forgotten passwords.

* **Group Module**

    Assign users to groups for greater control over permissions

* **Dashboard Module**

    Default landing page for the administrative side

* **More coming soon...**

* * *

## Requirements (LAMP)

*These requirements are what I have found to be true. It's likely that I may have missed something along the way. If so, please let me know.*

##### Linux
* So far, only tested on Linux Ubuntu 14.04 (trusty) running on an Amazon EC2 instance and another on a DigitalOcean Droplet.

##### Instructions for a Digital Ocean Droplet

[Installing the PHP Skeleton App on Digital Ocean](https://github.com/ghalusa/PHP-Skeleton-App/wiki/Installing-the-PHP-Skeleton-App-on-Digital-Ocean-(the-complete-guide)) (the complete guide)

##### Apache
* Modules: alias, deflate, dir, env, headers, mime, php5, rewrite, setenvif

##### MySQL

##### PHP >= 5.3
* Extensions: FileInfo, mysql, PDO, pdo_mysql, php5-curl, php5-json, php5-mcrypt

##### Git

##### Composer

#### Environment Check

To check to see if you have all of the necessary components in place, you can run the "Environment Check" script:

```bash
http://YOUR_DOMAIN/webapp_installer/library/env.php
```

* * *

## Getting Started

### STEP 1

#### Run Composer (non global installation)

```bash
php composer.phar create-project ghalusa/php-skeleton-app /PATH/TO/YOUR_EMPTY_WEB_ROOT_DIRECTORY/ dev-master
```

#### OR... Run Composer (global installation)

```bash
composer create-project ghalusa/php-skeleton-app /PATH/TO/YOUR_EMPTY_WEB_ROOT_DIRECTORY/ dev-master
```

### STEP 2

#### Make Sure Apache Has Permissions to Do Stuff
*(This can be changed back after the installation is finished.)*

```bash
sudo chown -R www-data:www-data /PATH/TO/YOUR_EMPTY_WEB_ROOT_DIRECTORY/
```

### STEP 3

#### Run the Web App Installer...

##### Point your browser to the root of your web environment...

```bash
http://YOUR_DOMAIN/
```

##### ... And Fill Out the Form

You will need:

* A valid email address for the creation of the administrative account.
* The location, username, and password of a MySQL database.

#### That's It!

At this point, you should have successfully spawned a complete "PHP Skeleton App". Dig in, and start adding your own "Modules"!

* * *

## Documentation

* [PHP Skeleton App Documentation](http://phpskeleton.com/) (coming soon)
* [PHPDocs](http://phpskeleton.com/docs/)
* Until I get the documentation out there, you can look at the anatomy of any of the modules which are included and present by default.
* On deck: I'm going to develop a "Module Creator", which will spawn a bare-bones module at the click of a button, or via command line.

## About the Author

The PHP Skeleton App is created and maintained by [Goran Halusa](http://halusanation.com/).

### Twitter

Follow [@phpskeleton](http://www.twitter.com/phpskeleton) on Twitter to receive the very latest news and updates about The PHP Skeleton App.

### Acknowledgements

* [PHP Weekly](http://phpweekly.com/): Thanks for including The PHP Skeleton App in your [weekly email](http://www.phpweekly.com/archive/2015-01-01.html)!

### Disclaimer

The PHP Skeleton App is in active development, and test coverage is continually improving.

* * *

## Open Source License

The PHP Skeleton App is released under the MIT public license.
