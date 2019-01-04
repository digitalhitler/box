<?php
namespace Getrix\Box\Functions\Common;


/**
 * Extracts the value of some certain key in each object or array in collection
 * and returns an array containing all extracted values.
 * Works both with sets with objects or arrays.
 *
 * @param array|object $items - Initial collection
 * @param mixed $key - Name of the key to be extracted
 *
 * @return array - Result of execution
 *
 * @example
 * extractFromEach([
 *   ['product_id' => 'prod-100', 'name' => 'Desk'],
 *   ['product_id' => 'prod-200', 'name' => 'Chair'],
 * ], 'name');
 * // ['Desk', 'Chair']
 */
function extractFromEach($items, $key): array {
  return array_map(function ($item) use ($key) {
    return is_object($item) ? $item->$key : $item[ $key ];
  }, $items);
}


/**
 * Iterates through arrays collection with extraction of given field value
 * that will be used as a key for the newly built hashtable with same entries.
 *
 * @todo func desc above - WAT?
 * @example
 * extractKeys([
 *   [ 'id' => 1, 'name' => 'Alice' ],
 *   [ 'id' => 2, 'name' => 'John' ]
 * ], "id");
 * // [1 => [ "id" => 1, "name" => "Alice" ], 2 => [ "id" => 2, "name" => "John" ]]
 *
 * @param $items - source collection
 * @param $key - key field
 *
 * @return array - reformatted collection as hashtable
 */
function extractKeys(array $items, string $key) {
  $result = [];
  if(is_array($items) && sizeof($items) > 0) {
    foreach($items as $item) {
      $id = $item[$key];
      if(!empty($id)) {
        $result[$id] = $item;
      }
    }
  }
  return $result;
}


/**
 * Sorts the collection by the value of some attribute of childs.
 *
 * @param $items - Initial collection
 * @param $attr - Attibute containing values used to sort by
 * @param $order - Order of sorting: "asc" or "desc"
 *
 * @return array
 *
 * @example
 * sortBy([
 *   ['id' => 2, 'name' => 'Joy'],
 *   ['id' => 3, 'name' => 'Khaja'],
 *   ['id' => 1, 'name' => 'Raja']
 * ],
 * 'id',
 * 'desc');
 * // [['id' => 3, 'name' => 'Khaja'], ['id' => 2, 'name' => 'Joy'], ['id' => 1, 'name' => 'Raja']]
 */
function sortBy($items, $attr, $order = 'asc'): array {
  $sortedItems = [];
  foreach ($items as $item) {
    $key = is_object($item) ? $item->{$attr} : $item[$attr];
    $sortedItems[$key] = $item;
  }
  if ($order === 'desc') {
    krsort($sortedItems);
  } else {
    ksort($sortedItems);
  }

  return array_values($sortedItems);
}


/**
 * Easily checks are there duplicates in the given array.
 *
 * @param $items - Source collection, array
 *
 * @return bool
 */
function hasDuplicates($items)
{
  return \count($items) > \count(\array_unique($items));
}


/**
 * Flats an array to one level.
 *
 * @param $items
 *
 * @return array
 */
function flatten($items)
{
  $result = [];
  foreach ($items as $item) {
    if (!\is_array($item)) {
      $result[] = $item;
    } else {
      $result = array_merge($result, array_values($item));
    }
  }

  return $result;
}


/**
 * Returns true if the provided function returns true for at least one element of an array, false otherwise.
 *
 * @param $items
 * @param $func
 *
 * @return bool
 */
function trueAnyOf(array $items, callable $func) {
  return \count(array_filter($items, $func)) > 0;
}


/**
 * Returns true if the provided function returns true for all elements of an array, false otherwise.
 *
 * @param $items
 * @param $func
 *
 * @return bool
 */
function trueEveryOf(array $items, callable $func) {
  return \count(array_filter($items, $func)) === \count($items);
}


/**
 * Returns the median of an array of numbers.
 *
 * @param array $numbers - array with values
 *
 * @return float - median value
 */
function median(array $numbers) {
  sort($numbers);
  $totalNumbers = \count($numbers);
  $mid = floor($totalNumbers / 2);

  return ($totalNumbers % 2) === 0 ? ($numbers[$mid - 1] + $numbers[$mid]) / 2 : $numbers[$mid];
}

/**
 * Sets or removes a cookie
 * @param string      $name - name of the cookie
 * @param string|null $value - its value or null if cookie needs to be removed
 * @param int|null     $lifetime - lifetime in seconds or: 0 will set session cookie, null or any value below zero will set expiration timestamp in the past
 * @param string|null $domain - cookie domain or null to set default domain from app config
 * @param string      $path - cookie path (default is root)
 * @param bool        $secure - secure flag (default true - means that cookies will work only over HTTPS)
 * @param bool        $httpOnly - http only flag (default false)
 *
 * @return bool
 */
function setCookie(string $name,
                   string $value = null,
                   int $lifetime = 2592000,
                   string $domain = null,
                   string $path = "/",
                   bool $secure = true,
                   bool $httpOnly = false) {

  if($value === null) {
    $value = "";
    $lifetime = -1;
  }

  if($lifetime > 0) {
    $expireAt = time() + $lifetime;
  } elseif($lifetime === 0) {
    $expireAt = 0;
  } elseif($lifetime === null || $lifetime < 0) {
    $expireAt = time() - 3600;
  } else {
    throw new \RuntimeException("setCookie error: wrong lifetime");
  }

  if($domain === null) {
    $domain = ".".$_SERVER["HTTP_HOST"];
  }

  return \setcookie(
    $name,
    $value,
    $expireAt,
    $path,
    $domain,
    $secure,
    $httpOnly);
}


function toHashtable(string $primaryKey, array $data = null, $strict = false) {
  $result = [];
  if(is_array($data) && sizeof($data) > 0) {
    foreach($data as $index => $item) {
      if(!empty($item) && is_array($item)) {
        if(isset($item[$primaryKey]) && !empty($item[$primaryKey])) {
          $result[$item[$primaryKey]] = $item;
        } else {
          if ($strict === true) {
            throw new \RuntimeException("Item with index {$index} has no primaryKey value.");
          } else {
            continue;
          }
        }

      }
    }
  }
  return $result;
}

function serializeCacheKey(array $args) {

  if(!is_array($args) || sizeof($args) === 0) {
    throw new \RuntimeException("Not enough arguments to serialize the key");
  }

  $parts = [];
  foreach($args as $arg) {
    $part = null;
    if($arg === true || $arg === false) {
      $part = ($arg === true ? 1 : 0);
    } elseif(is_array($arg)) {
      $part = implode("-", $arg);
    } else {
      $part = (string)$arg;
    }

    if($part !== null) {
      $parts[] = $part;
    }

    if(sizeof($parts) === 0) {
      throw new \RuntimeException("Serialization parts are processed but there are zero compatible items.");
    }
  }

  return implode("_", $parts);
}


function modifyQueryString(array $updateValues = [], string $url = null, $nullIsDelete = true) {
  if($url === null) {
    $url = $_SERVER["REQUEST_URI"];
  }

  $parts = parse_url($url);
  $result = [];

  if(is_array($parts)) {
    if(!isset($parts["query"]) || lengthOf($parts["query"]) === 0) {
//      $query = $updateValues;
    } else {
      $query = explode("&", $parts["query"]);
      if(sizeof($query) > 0) {
        foreach($query as $i => $item) {
          $item = explode("=", $item, 2);
          if(isset($updateValues[$item[0]])) {
            if($nullIsDelete === true && $updateValues[$item[0]] === null) {
              unset($result[$item[0]], $updateValues[$item[0]]);
              continue;
            } else {
              $result[$item[0]] = $updateValues[$item[0]];
              unset($updateValues[$item[0]]);
            }
          } else {
            $result[$item[0]] = $item[1];
          }
        }
      }
    }

    if(sizeof($updateValues) > 0) {
      foreach($updateValues as $k => $v) {
        if($v === null && $nullIsDelete === true) {
          unset($result[$k]);
          continue;
        }
        $result[$k] = $v;
      }
    }

    $resultCompiled = http_build_query($result);
    if(lengthOf($resultCompiled) > 0) {
      $resultCompiled = "?".$resultCompiled;
    }

    return $parts["path"].$resultCompiled;
  } else {
    return false;
  }
}