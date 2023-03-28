<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 50;

    /**
     * @var string
     */
    public const BATCH_USER = 'BATCH';

    /**
     * @var int
     */
    public const API_SEARCHING_LIMIT = 20;

    /**
      * Set the keys for a save update query.
      *
      * @param  \Illuminate\Database\Eloquent\Builder $query
      * @return \Illuminate\Database\Eloquent\Builder
      */
    protected function setKeysForSaveQuery(Builder $query): Builder
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param mixed $keyName
     * @return mixed
     */
    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        if (isset($this->original[$keyName])) {
            return $this->original[$keyName];
        }

        return $this->getAttribute($keyName);
    }

    /**
     * @return array
     */
    public function getWillCastAsBoolean(): array
    {
        return array_keys(array_where($this->getCasts(), function ($value) {
            return $value === 'boolean';
        }));
    }
}
