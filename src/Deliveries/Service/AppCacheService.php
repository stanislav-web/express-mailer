<?php
namespace Deliveries\Service;
use Deliveries\Exceptions\CacheException;

/**
 * CacheService class. PHP object / data cache adapter
 *
 * @package Application
 * @subpackage Helpers
 * @since PHP >=5.6
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Service/AppCacheService.php
 */
class AppCacheService {

    /**
     * Check if APC is available
     */
    public function __construct() {
        if(extension_loaded('apc') === false) {
            throw new CacheException('PHP extension APC is not installed');
        }
    }

    /**
     * Retrieves cached information from APC's data store.
     *
     * @param string $type - If $type is "user", information about the user cache will be returned.
     * @param boolean $limited - If $limited is true, the return value will exclude the individual
     *                           list of cache entries. This is useful when trying to optimize calls
     *                           for statistics gathering.
     *
     * @return array of cached data (and meta-data) or false on failure.
     */
    public static function info($type = '', $limited = false)
    {
        try {
            return apc_cache_info($type, $limited);
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * Checks if APC key exists.
     *
     * @param mixed $key - A string, or an array of strings, that contain keys.
     *
     * @return mixed - Returns true if the key exists, otherwise false or if an
     *                 array was passed to keys, then an array is returned that
     *                 contains all existing keys, or an empty array if none exist.
     */
    public static function exists($key = '')
    {
        try {
            return apc_exists($key);
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * Cache a variable in the data store.
     *
     * @param string $key - Store the variable using this name.
     * @param string $data - The variable to store.
     * @param int $ttl - Time To Live; store var in the cache for ttl seconds.
     * @param boolean $overwrite overwrite storage data flag
     *
     * @return boolean - Returns true on success or false on failure.
     */
    public static function store($key, $data, $ttl = 600, $overwrite = false)
    {
        try {
            if ($overwrite) {
                return apc_store($key, json_encode($data), $ttl);
            } else {

                return apc_add($key, json_encode($data), $ttl);
            }
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * Fetch stored value in APC from key.
     *
     * @param string $key - The key used to store the value.
     *
     * @return boolean - The stored variable or array of variables on success; false on failure.
     */
    public static function fetch($key = '')
    {
        try {
            if (self::exists($key)) {
                return json_decode(apc_fetch($key), true);
            } else {
                return false;
            }
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * Removes a stored variable from the cache.
     *
     * @param string $key - The key used to store the value (with apc_store()).
     *
     * @return boolean - Returns true on success or false on failure.
     */
    public static function delete($key = '')
    {
        try {
            return apc_delete($key);
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }

    /**
     * Clears the APC cache.
     *
     * @param string $type - If $type is "user", the user cache will be cleared; otherwise,
     *                       the system cache (cached files) will be cleared.
     *
     * @return boolean - Returns true on success or false on failure.
     */
    public static function clear($type = '') {
        try {
            return apc_clear_cache($type);
        } catch (\RuntimeException $e) {
            throw new CacheException($e->getMessage());
        }
    }
}