# Collection

A simple immutable collection library with a fluent API.

[![License: MIT](https://img.shields.io/badge/License-MIT-brightgreen.svg)](https://opensource.org/licenses/MIT)
[![Latest Stable Version](https://poser.pugx.org/pitchart/collection/v/stable)](https://packagist.org/packages/pitchart/collection)
[![Build Status](https://travis-ci.org/pitchart/collection.svg?branch=master)](https://travis-ci.org/pitchart/collection)

## Install

```bash
composer require pitchart/collection
```

## Usage

Use the `Collection` class to manipulate array datas :

```php
use Pitchart\Collection\Collection;

$numbers = Collection::from([1, 2, 3, 4]);
$plusOneNumbers = $numbers->map(function ($item) {
     return $item + 1;
});
$evenNumbers = $plusOneNumbers->filter(function ($item) {
    return $item % 2 == 0;
});
$total = $evenNumbers->reduce(function ($accumulator, $item) {
    return $accumulator + $item;
}, 0);
```
### Collection pipelines

> Collection pipelines are a programming pattern where you organize some computation as a sequence of operations which compose by taking a collection as output of one operation and feeding it into the next.
> __[Martin Fowler](https://martinfowler.com/articles/collection-pipeline/)__

This library is designed for collection pipelines usage as it offers a large catalogue of common collection operations.

The previous example can be rewriten like :

```php
$total = Collection::from([1, 2, 3, 4])
    ->map(function ($item) {
        return $item + 1;
    })
    ->filter(function ($item) {
        return $item % 2 == 0;
    })
    ->reduce(function ($accumulator, $item) {
        return $accumulator + $item;
    }, 0);
```

### Speed up operations thanks to generators

If you need to manipulate heavy number of datas or reduce the memory impact of heavy intermediate transformation, you can use the `GeneratorCollection` class :

```php
use Pitchart\Collection\GeneratorCollection;

$collection = Collection::from($heavyFolderList)
	->map(function ($folder) {
		return loadContentFromFilesInFolder($folder);
	})
	->filter(function ($content) {
		return lotsOfRegexpContentFiltering($content);
	})
	->reduce(function ($accumulator, $content) {
		return $accumulator.retrievePartsToCollect($content);
	}, '');
```

As a generator can be traversed only once, the result can be persisted in a classic Collection to be used as data source for multiples transformations :

```php
/** @var Collection $reusableCollection */
$reusableCollection = $generatorCollection->persist();
```

### Functional helpers

This package provides functional helpers to use collection pipelines focused on operations :

```php
use function Pitchart\Collection\Helpers\map;

map([1, 2, 3, 4], function ($item) {
    return $item + 1;
})
->filter(function ($item) {
    return $item % 2 == 0;
});
```

You can also use them to perform operations on any iterable datas, IE arrays or traversable objects, with a consistent API.

## Test

```bash
make test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.