<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait HasLinkedResource
{
    use HasLinkedModel;

    /**
     * Returns link on resource.
     *
     * @param Illuminate\Database\Eloquent\Model $resource
     *
     * @return mixed
     */
    final protected function getLinkOnResource($resource)
    {
        if (! isset($this->linkName) || $this->linkName === null) {
            $this->linkName = $this->getRelationshipName();
        }
        return $link = call_user_func([$resource, $this->linkName]);
    }

    /**
     * Returns all resources.
     *
     * @param Illuminate\Database\Eloquent\Model $resource
     *
     * @return Collection
     */
    protected function getAllLinkedResources($resource)
    {
        return $this->getLinkOnResource($resource)->get();
    }

    /**
     * Return linked model of $resourceId or fail with an error.
     *
     * @param Illuminate\Database\Eloquent\Model $resource
     * @param int                 $linkedResourceId
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getLinkedResourceOrFail($resource, $linkedResourceId)
    {
        return $this->getLinkOnResource($resource)->findOrFail($linkedResourceId);
    }

    /**
     * Return empty model of linked resource.
     *
     * @param \Illuminate\Http\Request|false $request when false, set attributes to null values
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function getNewLinkedResource()
    {
        return $this->getLinkedModel()->withNulledFillableAttributes();
    }
}
