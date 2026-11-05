<?php

namespace Dashifen\DTO;

use ReflectionClass;
use PropertyHookType;
use ArgumentCountError;
use ReflectionProperty;
use ReflectionParameter;

trait DataTransferTrait
{
  /**
   * Given an associative array of data, identifies which values within it
   * shall be passed to this object's constructor.  the suppressed inspection
   * hides a PHPStorm warning caused by this trait's inability to know the
   * number of parameters needed by the constructor within this scope.
   *
   * @param array $data
   *
   * @return static
   * @throws DTOException
   * @noinspection PhpMethodParametersCountMismatchInspection
   */
  public static function fromArray(array $data): static
  {
    // to identify the information within $data that we want to pass to the
    // object's constructor, we use reflection to get a list of that method's
    // parameters.  then, we can use array_map to create a list of their names.
    
    $getNames = fn(ReflectionParameter $parameter) => $parameter->getName();
    $parameters = new ReflectionClass(static::class)->getConstructor()?->getParameters() ?? [];
    $parameters = array_map($getNames, $parameters);
    
    // now, we want to find the intersection of $data's keys and the names of
    // our constructor's parameters.  with that, we can then filter $data to
    // remove any information that our constructor doesn't need.  finally, we
    // use the splat operator to hydrate our object.  PHP will "line up" the
    // values of $data by matching its keys with property names.  required
    // properties that aren't in $data will throw an ArgumentCountError.
    
    $intersectingKeys = array_intersect(array_keys($data), $parameters);
    $filter = fn(string $key) => in_array($key, $intersectingKeys);
    $data = array_filter($data, $filter, ARRAY_FILTER_USE_KEY);
    
    try {
      return new static(...$data);
    } catch (ArgumentCountError $e) {
      
      // if the filtered $data did not have information for all of our object's
      // required parameters, we end up here.  rather than throwing the core
      // PHP error, we're going to produce a hopefully more useful exception
      // here and throw that one.
      
      $filter = fn(ReflectionParameter $parameter) => !$parameter->isOptional();
      $requirements = array_map($getNames, array_filter($parameters, $filter));
      $missing = array_diff($requirements, array_keys($data));
      
      $message = sizeof($missing) > 1
        ? 'Unmet requirements: ' . join(', ', $missing)
        : 'Unmet requirement: ' . join(', ', $missing);
      
      throw new DTOException($message, DTOException::UNMET_REQUIREMENT, $e);
    }
  }
  
  /**
   * Produces an associative array mapping publicly visible property names to
   * their values, including virtual properties.
   *
   * @return array
   */
  public function toArray(): array
  {
    $array = [];
    $reflection = new ReflectionClass($this);
    
    // the ReflectionProperty::IS_PUBLIC flag will cause this method to return
    // fully public properties and all those that have asymmetric, but public,
    // visibility including virtual ones.
    
    $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    foreach ($properties as $property) {
      
      // virtual properties without getters are write-only.  they're rare, but
      // possible, and we want to skip them here along with uninitialized
      // properties.  we could combine these if-conditions, but this is a bit
      // more readable.
      
      if ($property->isVirtual() && !$property->hasHook(PropertyHookType::Get)) {
        continue;
      }
      
      if ($property->isInitialized($this)) {
        $array[$property->getName()] = $property->getValue($this);
      }
    }
  
    return $array;
  }
}
