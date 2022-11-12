<?php

if (!function_exists('ordinal_number')) {
  function ordinal_number(int $number): string|false
  {
    $numberFormatter = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
    return $numberFormatter->format($number);
  }
}
