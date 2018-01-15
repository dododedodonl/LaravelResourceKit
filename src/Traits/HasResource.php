<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait HasResource
{
    use HasModel;

    /**
     * Returns all resources.
     *
     * @return collection
     */
    protected function getAllResources()
    {
        return $this->getModel()->all();
    }

    /**
     * No resource present action to take.
     *
     * @return Response
     */
    protected function noResourcesPresent()
    {
        flash('No '.$this->getPluralModelName().' in database yet.')->error();
        return redirect()->route($this->resourceBase('create'));
    }

    /**
     * Return model of $resourceId or fail with an error.
     *
     * @param int $resourceId
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getResourceOrFail($resourceId)
    {
        return $this->getModel()->findOrFail($resourceId);
    }

    /**
     * Return empty model of resource.
     *
     * @param \Illuminate\Http\Request|false $request when false, set attributes to null values
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getNewResource()
    {
        return $this->getModel()->withNulledFillableAttributes();
    }
}
