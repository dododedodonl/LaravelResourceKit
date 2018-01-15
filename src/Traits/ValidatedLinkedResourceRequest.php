<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait ValidatedLinkedResourceRequest
{
    /**
     * Return the validated request.
     *
     * @return \Illuminate\Http\Request $request
     */
    protected function validatedRequest()
    {
        if (! isset($this->resourceRequest) || $this->resourceRequest === null) {
            $class = $this->getAppNamespace().'Http\Requests\Adjust'.$this->getModelName().'With'.$this->getLinkedModelName().'Request';
            if (class_exists($class)) {
                $this->resourceRequest = $class;
            } else {
                throw new \Exception('No request class found for '.$this->getModelName().' with '.$this->getLinkedModelname().'.');
            }
        }
        return resolve($this->resourceRequest);
    }
}
