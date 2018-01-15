<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait HasLinkedModel
{
    /**
     * Get linked model's name. Set $this->linkedModelName to override autodiscovery.
     *
     * @var string linkedModel
     */
    final protected function getLinkedModelName()
    {
        if (! isset($this->linkedModelName) || $this->linkedModelName === null) {
            //Assume the model's name is in the controller's
            $name = str_ireplace('Controller', '', class_basename($this));
            if (strpos($name, 'With') === false) {
                // We probably have a linked resource
                throw new \Exception("No linked model name found for class ".class_basename($this).'.');
            }
            $this->linkedModelName = substr($name, (strpos($name, 'With') + 4));
        }

        return $this->linkedModelName;
    }

    /**
     * Returns linked model name in lower case.
     *
     * @return string lower case linked model name
     */
    final protected function getLowerCaseLinkedModelName()
    {
        return strtolower($this->getLinkedModelName());
    }

    /**
     * Returns plural linked model name .
     *
     * @return string plural linked model name
     */
    final protected function getPluralLinkedModelName()
    {
        return str_plural($this->getLinkedModelName());
    }

    /**
     * Returns relationshipname. Set $this->relationshipName to override autodiscovery.
     *
     * @var string relationshipname
     */
    final protected function getRelationshipName()
    {
        if (isset($this->relationshipName) && $this->relationshipName !== null) {
            return $this->relationshipName;
        }
        return lcfirst($this->getPluralLinkedModelName());
    }

    /**
     * Returns the model.
     *
     * @return Illuminate\Database\Eloquent\Model model
     */
    protected function getLinkedModel()
    {
        $class = $this->getModelNameSpace().$this->getLinkedModelName();
        if (! class_exists($class)) {
            throw new \Exception('No model \''.$class.'\' found.');
        }

        return app($class);
    }
}
