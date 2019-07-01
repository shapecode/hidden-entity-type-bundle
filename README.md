Shapecode - Hidden Entity Type Bundle
============

Hidden entity type for Symfony forms.

[![paypal](https://img.shields.io/badge/Donate-Paypal-blue.svg)](http://paypal.me/nloges)

[![PHP Version](https://img.shields.io/packagist/php-v/shapecode/hidden-entity-type-bundle.svg)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![Latest Stable Version](https://img.shields.io/packagist/v/shapecode/hidden-entity-type-bundle.svg?label=stable)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![Latest Unstable Version](https://img.shields.io/packagist/vpre/shapecode/hidden-entity-type-bundle.svg?label=unstable)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/shapecode/hidden-entity-type-bundle.svg)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![Monthly Downloads](https://img.shields.io/packagist/dm/shapecode/hidden-entity-type-bundle.svg)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![Daily Downloads](https://img.shields.io/packagist/dd/shapecode/hidden-entity-type-bundle.svg)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)
[![License](https://img.shields.io/packagist/l/shapecode/hidden-entity-type-bundle.svg)](https://packagist.org/packages/shapecode/hidden-entity-type-bundle)

## What is it?

This is a Symfony form type that allows you to add an entity in your form that would be displayed as a hidden input.

## Installation

### Step 1: Download HiddenEntityTypeBundle using composer
```bash
$ composer require shapecode/hidden-entity-type-bundle
```
Composer will install the bundle to your project's vendor directory.

### Step 2: Enable the bundle
Enable the bundle in the kernel:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Shapecode\Bundle\HiddenEntityTypeBundle\ShapecodeHiddenEntityTypeBundle(),
        // ...
    );
}
```

## Usage

### Simple usage:
You can use the type in your forms just like this:
```php
<?php

use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenEntityType;

// ...
$builder->add('entity', HiddenEntityType::class, array(
    'class' => YourBundleEntity::class
));
```
You can also use the `HiddenDocumentType::class` type:
```php
<?php

use Shapecode\Bundle\HiddenEntityTypeBundle\Form\Type\HiddenDocumentType;

// ...
$builder->add('document', HiddenDocumentType::class, array(
    'class' => YourBundleDocument::class
));
```
There is only one required option "class". You must specify entity class in Symfony format that you want to be used in your form.

### Advanced usage:
You can use the `HiddenEntityType` or `HiddenDocumentType` type in your forms this way:
```php
<?php
// ...
$builder->add('entity', HiddenEntityType::class, array(
    'class' => YourBundleEntity::class, // required
    'property' => 'entity_id', // Mapped property name (default is 'id'), not required
    'multiple' => false, // support for an array of entities, not required
    'data' => $entity, // Field value by default, not required
    'invalid_message' => 'The entity does not exist.', // Message that would be shown if no entity found, not required
));
```

## Upgrade

### From 2.0
The options 'em' and 'dm' are not necessary anymore. The manager will now be load automatically.

## Reporting an issue or a feature request
Feel free to report any issues. If you have an idea to make it better go ahead and modify and submit pull requests.

### Original

The orginal source is from Glifery (https://github.com/Glifery/EntityHiddenTypeBundle) but seems not to be supported anymore.
