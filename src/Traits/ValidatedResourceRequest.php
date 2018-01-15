<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait ValidatedResourceRequest
{
    /**
     * Return the validated request.
     *
     * @return \Illuminate\Http\Request $request
     */
    protected function validatedRequest()
    {
        if (! isset($this->resourceRequest) || $this->resourceRequest === null) {
            $class = $this->getAppNamespace().'Http\Requests\Adjust'.$this->getModelName().'Request';
            if (class_exists($class)) {
                $this->resourceRequest = $class;
            } else {
                throw new \Exception('No request class found for '.$this->getModelName().'.');
            }
        }
        return resolve($this->resourceRequest);
    }
}
