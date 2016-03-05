# nyx/system
[![Latest Stable Version](https://poser.pugx.org/nyx/system/v/stable.png)](https://packagist.org/packages/nyx/system)
[![Total Downloads](https://poser.pugx.org/nyx/system/downloads.png)](https://packagist.org/packages/nyx/system)
[![Build Status](https://travis-ci.org/unyx/system.png)](https://travis-ci.org/unyx/system)
[![Dependency Status](https://www.versioneye.com/user/projects/52754de6632bac61f800008a/badge.png)](https://www.versioneye.com/user/projects/52754de6632bac61f800008a)

-----

Contains classes aimed at obtaining information about the operation system, executing processes, managing software
dependencies etc.

> #### Note: In development (read: unusable)
> It is currently in its **design** phase and therefore considered **unusable**. The API will fluctuate, solar flares will
> appear, wormholes will consume your data, gremlins will chase your cat. You've been warned.

-----

### Requirements

- PHP 5.5+
- proc_open() for executing system Calls/Processes

### Installation

The only supported way of installing this package is using [Composer](http://getcomposer.org).

- Add `nyx/system` as a dependency to your project's `composer.json` file.
- Run `php composer.phar update` and you're ready to go.

### Documentation

The code is fully inline documented for the time being. Online documentation will be made available in due time.

### Authors

The Call/Process subcomponent is **heavily** based on Symfony 2, so a lot of the code will be attributed to its
respective authors. However, this will be done no sooner than with the first tagged release as changes, especially
in the way the code is organized, are quite common and will definitely be even more prominent in the future.

### License

Nyx is open source software licensed under the [MIT license](http://opensource.org/licenses/MIT).