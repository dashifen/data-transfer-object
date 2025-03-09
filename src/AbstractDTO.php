<?php

namespace Dashifen\DTO;

abstract class AbstractDTO implements DTOInterface
{
  /**
   * Given an associative array of data, constructs our DTO by confirming which
   * properties match array indices within those data and setting their values
   * to those found in the array.
   *
   * @param array $data
   */
  public function __construct(array $data) {
    foreach ($data as $field => $value) {
      if (property_exists(static::class, $field)) {
        $this->$field = $value;
      }
    }
  }
  
  /**
   * Returns the json-serializable version of this object which is almost
   * always going to be the same as the results of its toArray method.
   *
   * @return array
   */
  public function jsonSerialize(): array
  {
    return $this->toArray();
  }
  
  /**
   * Returns the data within this object as an array.
   *
   * @return array
   */
  abstract public function toArray(): array;
}
