<?php

  namespace FS;
  
  /**
   * Iterates over an associative array and returns
   * the desired location or null if it doesn't exist.
   * This function short circuits, so if ('one','two','three')
   * was asked for and ['one']['two'] didn't exist, it
   * would return null without looking at ['one']['two']['three']
   */
  function getAttrOrNull($data, $attrs) {
    foreach($attrs as $a) {
      if(isset($data[$a])) {
        $data = $data[$a];
      } else {
        return null;
      }
    }
    return $data;
  }

?>