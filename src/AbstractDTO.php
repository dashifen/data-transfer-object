<?php

namespace Dashifen\DTO;

use Traversable;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;

/**
 * Abstract Data Transfer Object (DTO)
 *
 * This object uses the DataTransferTrait and implements two PHP interfaces:
 * JsonSerializable and IteratorAggregate.  If your object doesn't need these
 * capabilities, you can use the trait directly.
 */
abstract class AbstractDTO implements JsonSerializable, IteratorAggregate
{
  use DataTransferTrait;
  
  /**
   * Uses toArray, defined in the data transfer trait, to produce an array of
   * initialized properties for this object.  This allows our objects to be
   * passed through json_encode.
   *
   * @return array
   */
  public function jsonSerialize(): array
  {
    return $this->toArray();
  }
  
  /**
   * Returns an iterator allowing this object to be used in a foreach loop.
   * We chose an iterator over a generator because the heavy computational lift
   * is the reflective work in toArray not the memory used for the iterator,
   * especially since these objects tend to encapsulate relatively small
   * datasets.
   *
   * @return Traversable
   */
  public function getIterator(): Traversable
  {
    return new ArrayIterator($this->toArray());
  }
}
