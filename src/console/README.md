# nyx/console
[![Latest Stable Version](https://poser.pugx.org/nyx/console/v/stable.png)](https://packagist.org/packages/nyx/console)
[![Total Downloads](https://poser.pugx.org/nyx/console/downloads.png)](https://packagist.org/packages/nyx/console)
[![Build Status](https://travis-ci.org/unyx/console.png)](https://travis-ci.org/unyx/console)
[![Dependency Status](https://www.versioneye.com/user/projects/52754ded632bac4259000a45/badge.png)](https://www.versioneye.com/user/projects/52754ded632bac4259000a45)

-----

A self-contained framework for building console applications. It is based of Symfony 2's Console component and will
therefore be familiar to those who have already worked with Symfony, but at the same time it differs in some important
aspects.

It employs a hierarchical, infinitely (well, up to your memory limit) extensible structure which allows you to build
applications that in turn can be plugged into other applications by simply registering them as if they were a casual
command, since - technically - they are a command.

Where Symfony 2 uses strings as namespaces, Nyx uses actual objects. And while Symfony's Console component is great,
we felt that it could be improved in some aspects. However, the changes are too big for a project as commonly used as
Symfony and BC would become a serious issue which would make some of the changes close to impossible - thus; Nyx.

> #### Note: In development (read: unusable)
> It is currently in its **design** phase and therefore considered **unusable**. The API will fluctuate, solar flares will
> appear, wormholes will consume your data, gremlins will chase your cat. You've been warned.

-----

### Requirements

- PHP 5.5+

### Installation

The only supported way of installing this package is using [Composer](http://getcomposer.org).

- Add `nyx/console` as a dependency to your project's `composer.json` file.
- Run `php composer.phar update` and you're ready to go.

### Documentation

The code is fully inline documented for the time being. Online documentation will be made available in due time.

### Authors

Since most of the code is **heavily** based on Symfony 2, a lot of it will be attributed to its respective authors.
However, this will be done no sooner than with the first tagged release as changes, especially in the way the code is
organized, are quite common and will definitely be even more prominent in the future.

### License

Nyx is open source software licensed under the [MIT license](http://opensource.org/licenses/MIT).