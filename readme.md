# Data Transfer Object

A data transfer object (DTO) is an object that takes information from one place 
and moves it to another.  Frequently, I use these to avoid having to pass 
arrays around because my IDE (and many others) can provide code hinting for 
objects but may struggle to do so for arrays.

## Installation

`composer require dashifen/data-transfer-object`

## Usage

There are two ways to implement these capabilities:

1. `use` the `DataTransferTrait` directly.
2. extend the `AbstractDTO` object.

### Which One Should I Use?

The `AbstractDTO` object uses the trait and implements the `JsonSerializable`
and `IteratorAggregate` interfaces.  The former allows you to pass a DTO 
through the `json_encode` function.  The latter lets you use them in `foreach`
loops without explicitly calling their `toArray` method in your code.  

If you don't need these behaviors, you can just `use` the trait.  I tend to 
extend the object, but that's mostly so that I don't forget to do so at some
point when it matters.

## Opinions

These objects work best, in my opinion, with property promotion:

```php
class Person
{
  public function __construct(
    public  string $name,
    public  string $email,
    public ?string $usaPhone = null,
  ) {}
}
```

Even better: use asymmetric visibility to allow public read access but 
protected writes:

```php
class Person
{
  public function __construct(
    protected(set)  string $name,
    protected(set)  string $email,
    protected(set) ?string $usaPhone = null,
  ) {}
}
```
In this way, you can feel more confident that passing these data from scope to
scope within your code won't alter them by side effect.  You can also use 
property hooks:

```php
class Person
{
  public function __construct(
    protected(set)  string $name,
    protected(set)  string $email {
      set {
        $this->email = !filter_var($value, FILTER_VALIDATE_EMAIL)
          ? throw Exception("Invalid email address: $value")
          : $value;
      }
    },
    
    protected(set) ?string $usaPhone = null {
      set {
        $this->phone = preg_replace('/\D/', '', $value);
      },
      
      get {
        return vsprintf('+1 (%s) %s-%s', [
          substr($this->usaPhone, 0, 3),
          substr($this->usaPhone, 3, 3),
          substr($this->usaPhone, 6)
        ]);
      }
    }
  ) {}
}
```
## Required Properties

If your object has required properties, make sure they don't have a default
value when defining your constructor.  In the examples above, the name and  
email properties would be required, while the USA phone number is optional
because only it has a default value.

Then, whether you instantiate your object with the `new` keyword or the trait's
`fromArray` method, this allows PHP to "double-check" that you've provided all
the necessary data to the object.  Direct instantiation with the `new` keyword
would produce an `ArgumentCountError`.  In the `fromArray` method I've caught
that error and re-emitted a `DTOException` instead with more information.

## Repositories

These are the successors to my so-called [repository](https://github.com/dashifen/repository) object.  With the 
release of 8.4 and both property hooks and asymmetric property visibility, the 
work that my repository objects was doing to produce properties that could be 
publicly read but set only with an object (and, perhaps, only once) is 
unnecessary.  And, while the DTO trait/object still rely on some reflection,
it's far less than the deprecated repository objects did.
