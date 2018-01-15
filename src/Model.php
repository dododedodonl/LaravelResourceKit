<?php

namespace Dododedodonl\LaravelResourceKit;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    /**
     * Values that are filtered by default.
     *
     * @var array
     */
    protected $filtered_default = [
        'id',
        'created_at',
        'updated_at',
        'deleted_on',
    ];

    /**
     * Only return attributes that are displayable.
     *
     * @param bool  $default
     * @param array $filtered
     *
     * @return Collection
     */
    public function getFilteredAttributes($default = true, $filtered = [])
    {
        $filter = $this->filtered ? array_merge($this->filtered, $filtered) : $filtered;
        if ($default) {
            $filter = array_merge($filter, $this->filtered_default);
        }

        return collect($this->getAttributes())->reject(function ($value, $key) use ($filter) {
            if (is_null($value) || in_array($key, $filter)) {
                return true;
            }

            return false;
        })->transform(function ($value, $key) {
            return $this->getAttribute($key);
        });
    }

    /**
     * Only return attributes that are displayable.
     *
     * @param bool  $default
     * @param array $filtered
     *
     * @return Collection
     */
    public function getFilteredAndNullAttributes($default = true, $filtered = [])
    {
        $filter = $this->filtered ? array_merge($this->filtered, $filtered) : $filtered;
        if ($default) {
            $filter = array_merge($filter, $this->filtered_default);
        }

        return collect($this->getAttributes())->reject(function ($value, $key) use ($filter) {
            if (in_array($key, $filter)) {
                return true;
            }

            return false;
        })->transform(function ($value, $key) {
            return $this->getAttribute($key);
        });
    }

    /**
     * Only return attributes that are filled or fillable.
     *
     * @param bool  $default
     * @param array $filtered
     *
     * @return Collection
     */
    public function getFilteredAndFillableAttributes($default = true, $filtered = [])
    {
        $filter = isset($this->filtered) ? array_merge($this->filtered, $filtered) : $filtered;
        if ($default) {
            $filter = array_merge($filter, $this->filtered_default);
        }
        $fillable = isset($this->fillable) ? $this->fillable : [];

        return collect($this->getAttributes())->reject(function ($value, $key) use ($filter, $fillable) {
            if (in_array($key, $filter) || (is_null($value) && ! in_array($key, $fillable))) {
                return true;
            }

            return false;
        })->transform(function ($value, $key) {
            return $this->getAttribute($key);
        });
    }

    /**
     * Returns title description.
     *
     * @return string defaults to 'back'
     */
    public function titleDescription()
    {
        return 'back';
    }

    /**
     * Returns the table's name.
     *
     * @return string table name
     */
    public static function getTableName()
    {
        return with(new self)->getTable();
    }


    /**
     * Returns value for the route key.
     *
     * @return mixed defaults to id
     */
    public function getRouteKey()
    {
        return $this->id;
    }

    /**
     * Returns all fillable attributes with null.
     *
     * @return \Dododedodonl\LaravelResourceKit\Model $this
     */
    public function withNulledFillableAttributes()
    {
        if (isset($this->fillable)) {
            $model = $this;
            collect($this->fillable)->each(function ($value, $key) use ($model) {
                $model->$value = null;
            });
        }
        return $this;
    }
}
