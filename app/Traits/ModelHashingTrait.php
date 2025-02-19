<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ModelHashingTrait
{
    /**
     *  The default hash size length to use.
     *
     * @var int
     */
    protected static $default_hash_size = 24;

    /**
     *  The default route key to use.
     *
     * @var int
     */
    protected static $default_route_key = 'hash';

    /**
     * Automatically add a hash when a new instance of the calling class is made.
     *
     * @param array [$attributes=[]]
     *
     * @return $this
     */
    protected static function bootModelHashingTrait()
    {
        parent::creating(function($model) {
            do {
                $hash = self::generateHash();
            } while (parent::where(self::$default_route_key, $hash)->exists());

            $model->{self::$default_route_key} = $hash;
        });
    }

    /**
     * Gets the model associated to the hash.
     *
     * @param string $hash
     *
     * @return static
     */
    public static function findByHash(string $hash)
    {
        return static::query()->firstWhere('hash', $hash);
    }

    /**
     * Gets the model associated to the hash and fails otherwise.
     *
     * @param string $hash
     *
     * @return static
     */
    public static function findByHashOrFail(string $hash)
    {
        $model = static::findByHash($hash);
        if (!$model) {
            throw new ModelNotFoundException();
        }
        return $model;
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return self::$default_route_key;
    }

    /**
     * Save a new model and return the instance. Allow mass-assignment.
     *
     * @param array [$attributes=[]]
     *
     * @return static
     */
    public static function forceCreate(array $attributes = [])
    {
        if (!isset($attributes['hash'])) {
            $attributes['hash'] = self::generateHash();
        }
        $model = static::query()->forceCreate($attributes);
        return $model;
    }

    /**
     * Create and return an un-saved model instance.
     *
     * @param array [$attributes=[]]
     *
     * @return static
     */
    public static function make(array $attributes = [])
    {
        if (!isset($attributes['hash'])) {
            $attributes['hash'] = self::generateHash();
        }
        $model = static::query()->make($attributes);
        return $model;
    }

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param array|null [$except=null]
     *
     * @return static
     */
    public function replicate(array $except = null)
    {
        $new = parent::replicate($except);
        $new->hash = self::generateHash();
        return $new;
    }

    /**
     * Generates a hash from the given attributes.
     *
     * @return string
     */
    public static function generateHash()
    {
        $model = get_called_class();
        $hashSize = property_exists($model, 'hashSize') ? static::$default_hash_size : config('modelhashing.hash_size', static::$default_hash_size);
        return bin2hex(openssl_random_pseudo_bytes($hashSize, $crypto));
    }
}
