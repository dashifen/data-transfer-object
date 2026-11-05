<?php

namespace Dashifen\DTO;

use Dashifen\Exception\Exception;

class DTOException extends Exception
{
  // typically, I'd start an exception's list of error codes at positive one
  // and count upwards.  but, since I imagine that when extending this
  // exception to include other types of errors that specific DTOs might
  // produce, that I'll want to start those error codes at one.  therefore,
  // we'll use negative numbers herein.
  
  public const int UNMET_REQUIREMENT = -1;
}
