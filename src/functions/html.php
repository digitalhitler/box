<?php
namespace Getrix\Box\Functions\HTML;

function select($name, $items = [], $current = null, $default = null, $id = null, $classes = null) {

  if($current === "") {
    $current = null;
  }

  $html = '<select name="'.$name.'"';
  if($id !== null) {
    $html.= ' id="'.$id.'"';
  }
  if($classes !== null) {
    $html.= ' class="'.$classes.'"';
  }

  $html.='>';

  if(sizeof($items) > 0) {
    foreach($items as $val => $label) {
      $html.='<option value="'.$val.'"';
      if($current == $val || ($current === null && $default == $val)) {
        $html.=' selected';
      }
      $html.='>'.$label.'</option>';
    }
  }

  $html.='</select>';

  return $html;
}

