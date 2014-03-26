<?php

namespace Silexhibit\Helper;

class Security
{
  public static function antispambot($address, $mailto=0)
  {
    $safe = '';
    srand((float) microtime() * 1000000);
    for ($i = 0; $i < strlen($address); $i++) {
      $j = floor(rand(0, 1 + $mailto));
      switch ($j) {
        case 0:
          $safe .= '&#' . ord(substr($address, $i, 1)) . ';';
          break;
        case 1:
          $safe .= substr($address, $i, 1);
          break;
        case 2:
          $safe .= '%' . sprintf('%0' . 2 . 's', dechex(ord(substr($address, $i, 1))));
          break;
      }
    }
    $safe = str_replace('@', '&#64;', $safe);
    return $safe;
  }

}
