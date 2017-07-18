# Eloquent Activatable

Creating (de-) activatable Eloquent Models made easy.

[![Build Status](https://travis-ci.org/niclasleonbock/eloquent-activatable.svg?branch=master)](https://travis-ci.org/niclasleonbock/eloquent-activatable) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/niclasleonbock/eloquent-activatable/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/niclasleonbock/eloquent-activatable/?branch=develop)

## Installation
First, you'll need to add the package to your `composer.json` and run `composer update`.

```json
{
    "require": {
        "niclasleonbock/eloquent-activatable": "~5.0"
    },
}
```

> Please require version 4.0 when using with Laravel 4.x.

Now, simply add a datetime column called `activated_at` to your table and use the `ActivatableTrait` (`niclasleonbock\Eloquent\ActivatableTrait`) in your Eloquent model.

### Migration
```php
<?php
$table->datetime('activated_at')->nullable();
```

### Your Model
```php
<?php
use niclasleonbock\Eloquent\ActivatableTrait;

class Topic extends Eloquent
{
    use ActivatableTrait;

    // ...
}

```

And you're done!

## Use
### withDeactivated()
By default all database queries will be filtered so that only activated data sets are shown. To also show deactivated data sets you may use the `withDeactivated` method on the query builder.
```php
<?php
$allTopics = Topic::withDeactivated()->get();
```
### onlyDeactivated()
To get **only** deactivated data sets use the `onlyDeactivated` method.
```php
<?php
$onlyDeactivatedTopics = Topic::onlyDeactivated()->get();
```

### activated()
To check whether a data set is deactivated you may use the `activated` method.
```php
<?php
echo 'My topic is ' . ($topic->activated() ? 'activated' : 'deactivated');
```

### activate()
To **activate** a data set use the `activate` method.
```php
<?php
$topic->activate();
$topic->save();

echo 'My topic is now ' . ($topic->activated() ? 'activated' : 'deactivated');
```

### deactivate()
To **deactivate** a data set use the `deactivate` method.
```php
<?php
$topic->deactivate();
$topic->save();

echo 'My topic is now ' . ($topic->activated() ? 'activated' : 'deactivated');
```

### Customization
Sometimes the column name `activated_at` may not fit even though the functionality does. To change the name you can easily override the protected `$activatedAtColumn` variable or the public `getActivatedAtColumn` method.

```php
protected $activatedAtColumn = 'my_column_name';

// or

public getActivatedAtColumn()
{
    return 'my_column_name';
}

```

