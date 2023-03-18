# Better search management for Laravel

## Introduction

Searching within Laravel models on their own is an easy task but if you need to search for multiple models queries can become pretty slow.

This package allows developers to leverage their searches from everywhere within their application on an easy way.

Flarone is a web development studio based near Amsterdam, The Netherlands. You can learn more about us at [flarone.com](https://flarone.com)

## Table of contents

- [Laravel compatibility](#laravel-compatibility)
- [Features overview](#features-overview)
- [Installation](#installation)

## Laravel compatibility

 Laravel  | Laravel-searchable
:---------|:----------
 8.x  	  | 1.0.x
 9.x  	  | 1.0.x
 10.x  	  | 1.0.x

## Features overview

 - Searchable models will be indexed automatically
 - Customizeable fields you want to be searchable

## Installation

Require through composer

	composer require flarone/laravel-searchable

Or manually edit your composer.json file:

	"require": {
		"flarone/laravel-searchable": "^1.x"
	}

Publish both the configuration file and the migrations:

	php artisan vendor:publish --provider="Flarone\Searchable\SearchableServiceProvider"

Execute the database migrations:

	php artisan migrate

You may check the package's configuration file at:

	config/searchable.php


### Clearing the cache

Available since version 1.0.x, you may clear the translation cache through both an Artisan Command and a Facade. If cache tags are in use, only the translation cache will be cleared. All of your application cache will however be cleared if you cache tags are not available.

Cache flush command:

    php artisan searchable:flush


#### Credits

- Flarone - https://flarone.com

