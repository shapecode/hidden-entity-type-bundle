# EntityHidden form type

Hidden entity type for Symfony2 forms.

## What is it?

This is a Symfony2 form type that allows you to add an entity in your form that would be displayed as a hidden input.

## Installation

### Step 1: Download EntityHiddenTypeBundle using composer
```
$ php composer.phar require glifery/entity-hidden-type-bundle
```
Composer will install the bundle to your project's vendor/glifery directory.

### Step 2: Enable the bundle
Enable the bundle in the kernel:
```
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Glifery\EntityHiddenTypeBundle\GliferyEntityHiddenTypeBundle(),
        // ...
    );
}
```

## Usage

### Simple usage:
You can use the `entity_hidden` type in your forms just like this:
```php
<?php
    // ...
    $builder->add('entity', 'entity_hidden', array(
        'class' => 'YourBundle:Entity' // That's all !
    ));
```
You can also use the `document_hidden` type:
```php
<?php
    // ...
    $builder->add('document', 'document_hidden', array(
        'class' => 'YourBundle:Document' // That's all !
    ));
```
There is only one required option "class". You must specify entity class in Symfony format that you want to be used in your form.

### Advanced usage:
You can use the `entity_hidden` or `document_hidden` type in your forms this way:
```php
<?php
    // ...
    $builder->add('entity', 'entity_hidden', array(
        'class' => 'YourBundle:Entity'
        'property' => 'entity_id', // Mapped property name (default is 'id')
        'data' => $entity, // Field value by default
        'invalid_message' => 'The entity does not exist.', // Message that would be shown if no entity found
        'em' => 'common_em' // You can use specified entity manager for use with entity_hidden
        'dm' => 'common_dm' // You can use specified document manager for use with document_hidden
    ));
```

## Reporting an issue or a feature request
Feel free to report any issues. If you have an idea to make it better go ahead and modify and submit pull requests.
