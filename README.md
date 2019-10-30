# php-jira-domain-models
Domain models for entities in Jira

## usage

```php
<?php

use Technodelight\Jira\Domain\Field;

$field = Field::fromArray([
  'id' => 1,
  'key' => 'description',
  'name' => 'Description',
  'custom' => false,
  'clauseNames' => [],
  'schema' => []
]);
```
The above example is purely fictious, though it's working this way, it's not really intended 
to be used as per above in real life.

This Domain model library contains domain models for most of the entities used in JIRA API (in user scenarios).
It does not contains models for admin-related entities.

## license

GNU GPLv3
Copyright (c) 2015-2019 Zsolt Gál
See LICENSE.
