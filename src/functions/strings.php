<?php
namespace Getrix\Box\Functions\Strings;

/**
 * String sanitizing and safe-symbol filtering.
 *
 * @param string $str - source string
 * @param string $dataType - type of processing
 *
 * @return int|null|string
 */
function sanitize($str, string $dataType = 'safeString') {

  if (isset($str)) {

    switch ($dataType) {
      case 'user':
        $str = (int)$str;
        if($str <= 0) {
          $str = null;
        }
        break;

      case 'safeString':
        $str = strip_tags($str);
        $str = htmlspecialchars($str, ENT_QUOTES);
        $str = addslashes($str);
        break;

      case 'integer':
        $str = intval($str);
        break;

      case 'alphanum':
        // @todo implement alpa-numeric filter
        break;

      case 'url':
        $str = filter_var($str, \FILTER_SANITIZE_URL);
        break;

      case 'mobile':
        $number = (int) filter_var($str, FILTER_SANITIZE_NUMBER_INT);
        $str = null;

        if($number === 0 ||
          strlen($number) < 10 ||
          strlen($number) > 11) {
          break;
        }

        // accepting 9001234567 format:
        if(strlen($number) === 10) {

          // Fail if first digit is not 9
          if(9 !== intval(substr($number, 0, 1))) {
            break;
          }

          // Otherwise append 7 and continue
          $number = intval("7".$number);
        }

        // Must contain exactly 11 digits:
        if(strlen($number) === 11) {
          // Replace leading 8 if it present
          if(8 !== intval(substr($number, 0, 1))) {
            $number = intval("7".substr($number, 1, 10));
          }

          // Fail if number starts not with 79
          if(79 !== intval(substr($number, 0, 2))) {
            break;
          }

          // Finally, number is ok.
          $str = $number;
        }

        break;
      case 'boolean':
        if (in_array($str, [true, "true", "yes", "Y", 1, "1", "TRUE"])) {
          $str = true;
        } else {
          $str = false;
        }
        break;

      default:
        throw new \RuntimeException("sanitizeString error: unknown data type");
        break;
    }

  }
  return $str;
}


/**
 * Check if a string is ends with a given substring.
 *
 * @param $haystack
 * @param $needle
 *
 * @return bool
 */
function isEndsWith(string $haystack, string $needle): bool {
  return mb_substr($haystack, -mb_strlen($needle)) === $needle;
}


/**
 * Check if a string is starts with a given substring.
 *
 * @param $haystack
 * @param $needle
 *
 * @return bool
 */
function isStartsWith(string $haystack, string $needle): bool {
  return mb_strpos($haystack, $needle) === 0;
}


/**
 * Counts length of the string.
 *
 * @param $str - initial string
 *
 * @return int - symbolic length of string
 */
function lengthOf($str): int {
  return mb_strlen($str);
}


/**
 * Capitalizes the string.
 * Uses multibyte extension and works with any type of characters.
 *
 * @param string $string - source string
 *
 * @return string - capitalized string
 */
function capitalize(string $string): string {
  $strlen = mb_strlen($string);
  $firstChar = mb_substr($string, 0, 1);
  $then = mb_substr($string, 1, $strlen - 1);
  return mb_strtoupper($firstChar) . $then;
}


/**
 * Lowercase the whole string
 *
 * @param string $string - source string
 *
 * @return string - lowercased string
 */
function lowercase(string $string): string {
  return mb_strtolower($string);
}


/**
 * Returns the first string there is between the strings from the parameter start and end.
 *
 * @param $haystack
 * @param $start
 * @param $end
 *
 * @return bool|string
 *
 * @example
 * firstWrappedString('This is a [custom] string', '[', ']'); // custom
 */
function firstWrappedString($haystack, $start, $end): string
{
  $char = mb_strpos($haystack, $start);
  if ($char === false) {
    return '';
  }

  $char += mb_strlen($start);
  $len = mb_strpos($haystack, $end, $char) - $char;

  return mb_substr($haystack, $char, $len);
}
