<?php

/**
 * This is a file that contains generic application functions
 * Do not add a namespace to this file
 */

function dump(...$args)
{
  $out = array_map(fn($arg) => print_r($arg, true), $args);
  printf("<pre>%s</pre>", implode("\n\n", $out));
}

function dd(...$args)
{
  dump(...$args);
  die;
}
