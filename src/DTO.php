<?php

namespace Dashifen\DTO;

use ReflectionClass;
use ReflectionProperty;
use Dashifen\Debugging\DebuggingTrait;

class DTO extends AbstractDTO
{
  use DebuggingTrait;
  
  /**
   * Given an associative array of data, constructs our DTO by confirming which
   * properties match array indices within those data and setting their values
   * to those found in the array.
   *
   * @param array $data
   */
  public function __construct(array $data)
  {
    try {
      parent::__construct($data);
    } catch (DTOException $e) {
      
      // to help make extensions of this object less complicated, we use our
      // catcher method to handle the exception.  by default, it will write the
      // exception to the log in non-debugging environments and dump it to the
      // screen in other ones.
      
      self::catcher($e);
    }
  }
  
  /**
   * Returns the data within this object as an array.
   *
   * @return array
   */
  public function toArray(): array
  {
    $reflection = new ReflectionClass(static::class);
    $properties = array_map(
      fn(ReflectionProperty $prop) => $prop->getName(),
      $reflection->getProperties(
        ReflectionProperty::IS_PUBLIC |
        ReflectionProperty::IS_PROTECTED_SET |
        ReflectionProperty::IS_PRIVATE_SET |
        ReflectionProperty::IS_VIRTUAL
      )
    );
    
    foreach ($properties as $property) {
      $array[$property] = $this->$property;
    }
    
    return $array ?? [];
  }
}
