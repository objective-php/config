# Objective PHP / Config [![Build Status](https://secure.travis-ci.org/objective-php/config.png?branch=master)](http://travis-ci.org/objective-php/config)

## Project introduction

Config provides developer with an easy way to set and load configuration directives in a project.

## What's next?

Some more sanitiy checks will be needed, as well as, probably, some dedicated Mergers. But it's still quite usable as is.

## Installation

### Manual

You can clone our Github repository by running:

```
git clone http://github.com/objective-php/config
```

If you're to proceed this way, you probably don't need more explanation about how to use the library :)

### Composer

The easiest way to install the library and get ready to play with it is by using Composer. Run the following command in an empty folder you just created for Primitives:

```
composer require --dev objective-php/config:dev-master 
```

Then, you can start coding using primitive classes by requiring Composer's `autoload.php` located in its `vendor` directory.

Hmm, before starting coding, please take the time to read this file till the end :)

## How to test the work in progress?

### Run unit tests

First of all, before playing around with our config object, please always run the unit tests suite. Our tests are written using PHPUnit, and can be run as follow:

```
cd [clone directory]/tests
./phpunit .
```

TO BE CONTINUED
