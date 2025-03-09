<?php

namespace Dashifen\DTO;

use JsonSerializable;

interface DTOInterface extends JsonSerializable
{
  /**
   * Returns the data within this object as an array.
   *
   * @return array
   */
  public function toArray(): array;
}
