<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

use \Illuminate\Console\DetectsApplicationNamespace;

trait HasModel
{
    use DetectsApplicationNamespace;

    /**
     * Returns model name. Set $this->modelName to override autodiscovery.
     *
     * @return string modelname
     */
    final protected function getModelName()
    {
        if (! isset($this->modelName) || $this->modelName === null) {
            //Assume the model's name is in the controller's
            $name = str_ireplace('Controller', '', class_basename($this));
            if (strpos($name, 'With') !== false) {
                // We probably have a linked resource
                $name = substr($name, 0, strpos($name, 'With'));
            }
            $this->modelName = str_singular($name);
        }

        return $this->modelName;
    }

    /**
     * Returns model name in lower case.
     *
     * @return string lower case model name
     */
    final protected function getLowerCaseModelName()
    {
        return strtolower($this->getModelName());
    }

    /**
     * Returns plural model name.
     *
     * @return string plural model name
     */
    final protected function getPluralModelName()
    {
        return lcfirst(str_plural($this->getModelName()));
    }

    /**
     * Returns the model's specified namespace or defaults to the app's namespace.
     * Set $this->modelNameSpace to override autodiscovery.
     *
     * @return string namespace with trailing slash
     */
    final protected function getModelNameSpace()
    {
        if (! isset($this->modelNameSpace) || $this->modelNameSpace === null) {
            //Assume app namespace
            $this->modelNameSpace = $this->getAppNamespace();
        }

        return $this->modelNameSpace;
    }

    /**
     * Returns the model.
     *
     * @return Illuminate\Database\Eloquent\Model model
     */
    protected function getModel()
    {
        $class = $this->getModelNameSpace().$this->getModelName();
        if (! class_exists($class)) {
            throw new \Exception('No model \''.$class.'\' found.');
        }

        return app($class);
    }
}
