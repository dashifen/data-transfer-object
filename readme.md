# Data Transfer Object

A data transfer object (DTO) is an object that takes information from one place and moves it to another.  Frequently, I use these to avoid having to pass arrays around because my IDE (and many others) can provide code hinting for objects but may struggle to do so for arrays.

## Installation

`composer require dashifen/data-transfer-object`

## Usage

There are two ways to use my DTO objects:

1. Extending the `AbstractDTO` object, or
2. Extending the `DTO` object.

In either case, instantiating a DTO requires an associative array as an 
argument to its constructor.  That array should be a map linking property names
to their values.  For example:

```php
class FooBar extends DTO {
  protected(set) int $foo;
  protected(set) int $bar;
}

$example = new FooBar(['foo' => 1, 'bar' => 2]);
```

The constructor will then assign the value 1 to the foo property and 2 to bar.

### Extending AbstractDTO

The `AbstractDTO` object requires that you implement a `toArray` method for yourself.  It also throws a `DTOException` from its constructor if there are unmet requirements (see below).

### Extending DTO

The `DTO` object is, itself, an extension of the `AbstractDTO`.  It does two things for you:

1. Catches the `DTOException` thrown in the constructor and uses the `catcher` method of the `DebuggingTrait` to handle it.
2. Implements the `toArray` method returning an array of any properties that are publicly available, even if asymmetrically so or [virtual](https://www.php.net/manual/en/language.oop5.property-hooks.php#language.oop5.property-hooks.virtual).

## Asymmetric Visibility

In PHP 8.4, [asymmetric visibility](https://www.php.net/manual/en/language.oop5.visibility.php#language.oop5.visibility-members-aviz) allows us to create properties that can be accessed publicly but set only within the class.  

Both properties with `public` and `protected(set)` visibility can be set via the constructor in the `AbstractDTO` object even within your extensions.  If you want to use `private(set)` visibility, you'll need to override it because private setters are only scoped within their own object and neither in ancestors nor children. 

## Required Properties

If there are properties that should be required, i.e. that must be set in your object's constructor, specify them by name as a protected array of requirements in its definition:

```php
class FooBar extends DTO {
  protected array $requirements = ['foo'];
  protected(set) int $foo;
  protected(set) int $bar;
}
```

## Getters and Setters

I recommend specifying both getters and setters using property hooks:

```php
class Example extends DTO {
  protected(set) string $usPhone {
    set { 
      $this->usPhone = preg_replace('~\D~', '', $value);
    }
    
    get { 
      return vsprintf('+1 (%s) %s-%s', [
        substr($this->usPhone, 0, 3),
        substr($this->usPhone, 3, 3),
        substr($this->usPhone, 6),
      ]); 
    }
  }
}
```

## Repositories

These are the successors to my so-called [repository](https://github.com/dashifen/repository) object.  With the release of 8.4 and both property hooks and asymmetric property visibility, the work that my repository objects was doing to produce properties that could be publicly read but set only with an object (and, perhaps, only once) is unnecessary.
