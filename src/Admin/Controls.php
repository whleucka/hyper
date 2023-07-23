<?php

namespace Nebula\Admin;

class Controls
{
  public static function readonly($title, $value): string
  {
    return<<<EOT
    <label for="div">$title</label>
    <div id="div" class="form-control">$value</div>
    EOT;
  }

  public static function input($column, $title, $value, $type = 'text'): string
  {
    return<<<EOT
    <label for="formControlInput" class="form-label ps-1">$title</label>
    <input type="$type" name="$column" class="form-control" id="formControlInput" placeholder="$title" value="$value">
    <span id="$column-errors"></span>
    EOT;
  }

  public static function number($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'number');
  }

  public static function email($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'email');
  }

  public static function button($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'button');
  }

  public static function checkbox($column, $title, $value): string
  {
    $checked = $value ? 'checked' : '';
    return<<<EOT
    <div class="form-check my-2">
      <input name="$column" class="form-check-input" type="checkbox" value="1" id="formCheckChecked" $checked>
      <label class="form-check-label" for="formCheckChecked">$title</label>
      <span id="$column-errors"></span>
    </div>
    EOT;
  }

  public static function color($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'color');
  }

  public static function date($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'date');
  }

  public static function datetime_local($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'datetime-local');
  }

  public static function file($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'file');
  }

  public static function hidden($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'hidden');
  }

  public static function image($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'image');
  }

  public static function month($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'month');
  }

  public static function password($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'password');
  }

  public static function range($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'range');
  }

  public static function reset($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'reset');
  }

  public static function search($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'search');
  }

  public static function submit($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'sumbit');
  }

  public static function tel($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'tel');
  }

  public static function text($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'text');
  }

  public static function time($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'time');
  }

  public static function url($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'url');
  }

  public static function week($column, $title, $value): string
  {
    return self::input($column, $title, $value, 'week');
  }

  public static function floatingInput($column, $title, $value): string
  {
    return<<<EOT
    <div class="form-floating">
      <input
        name="$column"
        type="input"
        class="form-control"
        id="floatingInput"
        value="$value"
        placeholder=" "
      />
      <label for="floatingInput">$title</label>
      <span id="$column-errors"></span>
    </div>
    EOT;
  }

  public static function textarea($column, $title, $value): string
  {
    return<<<EOT
    <div class="my-2">
      <label for="floatingTextarea">$title</label>
      <textarea name="$column" class="form-control" placeholder="$title" style="height: 100px">$value</textarea>
      <span id="$column-errors"></span>
    </div>
    EOT;
  }

  public static function floatingTextarea($column, $title, $value): string
  {
    return<<<EOT
    <div class="form-floating my-2">
      <textarea name="$column" class="form-control" placeholder=" " id="floatingTextarea" style="height: 100px">$value</textarea>
      <label for="floatingTextarea">$title</label>
      <span id="$column-errors"></span>
    </div>
    EOT;
  }
}
