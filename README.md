# Silexhibit

[![Dependency Status](https://img.shields.io/david/hlfcoding/silexhibit.svg)](https://david-dm.org/hlfcoding/silexhibit#info=dependencies)
![GitHub License](https://img.shields.io/github/license/hlfcoding/silexhibit.svg)

> :framed_picture: A renovated Indexhibit 1 on Silex.

## Purpose

> [Indexhibit](https://indexhibit.org) is a pioneering CMS web application founded in 2006 which is used to create websites in the established index + exhibit format.

I once built my [site](http://pengxwang.com) on it and customized it to suit my needs, years later leading me to fully rebuild it with [Silex](https://silex.symfony.com) and more common conventions, as well as with backwards compatibility for the database schema. Through the process (and one failed rewrite), I have revised the original design to suit my simple needs. Themes now have full responsibility over presentation and must use Mustache. Most workflow tasks are handled by small shell executables. Only the Site and not the (forthcoming) CMS is deployed, and assets are expected to deploy to S3. Plugin and statistics support are removed. I'm open to revisiting these strict decisions if a community with different needs arises.

## Usage

Familiarity with web technologies and coding is required. PHP 5.6.x is required.

1. Fork this repository, so you can propose upstream contributions when needed.
2. Run `bin/setup`; you'll need `composer`.
3. Create your `config/common.php` off of `config/common.sample.php`.
4. Run `bin/install-theme -t=developer`.
5. Create your theme and run `bin/install-theme` for it. See `theme/pengxwang`.
6. Create your `config/prod.php` off of `config/dev.php`.
7. When you're ready to deploy, run `bin/deploy -b=<bucket> -d=<ssh>:<path> -n=<db_name> -u=<db_user> --go`.

Note: for `bin` scripts that take options, the `--help` (`-h`) argument prints out usage info.

## License

The MIT License (MIT)

Copyright (c) 2013-present Peng Wang
