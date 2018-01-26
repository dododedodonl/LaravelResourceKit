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
     * What form input should an attribute be
     * key => type
     *
     * @var array
     */
    protected $form_types = [];

    /**
     * Only return attributes that are displayable.
     *
     * @param bool  $default
     * @param array $filtered
     *
     * @return Collection
     */

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
     * Returns all fillable attributes with null.
     *
     * @return \Ledenbestand\Model $this
     */
    public function withNulledFillableAttributes()
    {
        if (isset($this->fillable)) {
            foreach($this->fillable as $fillable) {
                $this->$fillable = null;
            }
        }
        return $this;
    }

    /**
     * Returns value for the path attribute.
     *
     * @return mixed defaults to id
     */
    public function pathAttribute()
    {
        return $this->id;
    }

    protected function filteredKeys()
    {
        if (isset($this->filtered)) return $this->filtered;

        return $this->filtered_default;
    }

    public function presentableAttributes($withNulled = false)
    {
        //TODO: add relationships
        $filtered = $this->filteredKeys();

        $attributes = [];
        foreach($this->getAttributes() as $key => $value) {
            if (key_exists($key, $this->getCasts()) && $this->getCasts()[$key] == 'boolean') {
                $value = ($value) ? 'yes' : 'no';
            }
            if ( ! in_array($key, $filtered) && ($withNulled || $value !== null)) {
                $attributes[$key] = $value;
            }
        }
        return $attributes;
    }

    public function adjustableAttributes($withNulled = true)
    {
        //TODO: add relationships?
        $attributes = [];
        foreach($this->fillable as $key) {
            $value = $this->getAttribute($key);

            if($withNulled || $value !== null) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }

    public function attributeInputs($withNulled = true)
    {
        //TODO: add relationships
        $attributes = $this->adjustableAttributes($withNulled);

        $inputs = [];
        foreach($attributes as $key => $value) {
            $input = [
                'type'  => 'text',
                'key'   => $key,
                'value' => $value,
                'title' => $this->keyToTitle($key),
            ];

            if (key_exists($key, $this->form_types) && $this->form_types[$key] == 'textarea') {
                $input['type'] = 'textarea';

            } else if (key_exists($key, $this->form_types) && starts_with($this->form_types[$key], 'enum:')) {
                $enum = explode(',', substr($this->form_types[$key], strlen('enum:')));

                $options = [];
                foreach($enum as $option) {
                    if(($pos = strpos($option, '|')) !== false) {
                        $options[] = [
                            'value' => substr($option, 0, $pos),
                            'title' => substr($option, $pos + 1),
                        ];
                    } else {
                        $options[] = [
                            'value' => $option,
                            'title' => $option,
                        ];
                    }
                }

                $input['type'] = 'radio';
                $input['options'] = $options;

            } else if (key_exists($key, $this->getCasts()) && $this->getCasts()[$key] == 'boolean') {
                $input['type'] = 'radio';
                $input['options'] = [
                    [
                        'value' => '1',
                        'title' => 'yes',
                    ],
                    [
                        'value' => '0',
                        'title' => 'no'
                    ]
                ];
            }

            $inputs[] = $input;
        }

        return $inputs;
    }

    public function keyToTitle($key)
    {
        return ucfirst(strtolower(str_ireplace('_', '', $key)));
    }

    public function linkedResources()
    {
        if (! (isset($this->show_linked_resources) && count($this->show_linked_resources) > 0)) return [];

        return $this->show_linked_resources;
    }
}
