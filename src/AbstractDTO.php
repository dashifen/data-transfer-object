<?php

namespace Dashifen\DTO;

use JsonSerializable;

abstract class AbstractDTO implements JsonSerializable
{
  protected array $requirements = [];
  
  /**
   * Given an associative array of data, constructs our DTO by confirming which
   * properties match array indices within those data and setting their values
   * to those found in the array.
   *
   * @param array $data
   *
   * @throws DTOException
   */
  public function __construct(array $data) {
    
    // before we loop over our $data and set properties, we can confirm if any
    // requirements will be unmet.  array_diff returns a list of values in the
    // first array that are not found in the second.  so, if we have required
    // properties that are not specified in $data, they will be unmet after our
    // loop.
    
    $unmet = array_diff($this->requirements, array_keys($data));
    
    if (($count = sizeof($unmet)) > 0) {
      $noun = $count === 1 ? 'requirement' : 'requirements';
      throw new DTOException("Unmet $noun: " . join(', ', $unmet),
        DTOException::UNMET_REQUIREMENT);
    }
    
    foreach ($data as $property => $value) {
      if (property_exists(static::class, $property)) {
        $this->$property = $value;
      }
    }
  }
  
  /**
   * Returns the json-serializable version of this object which is almost
   * always going to be the same as the results of its toArray method.  If it's
   * not, extensions can override this method.
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
