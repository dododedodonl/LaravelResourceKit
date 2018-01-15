<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait IsNotCreatable
{

    /**
     * {@inheritdoc}
     */
    final protected function noResourcesPresent()
    {
        //Make sure there is no redirect loop.
        abort(404, 'No '.$this->getPluralModelName().' in database yet.');
    }

    /**
     * {@inheritdoc}
     */
    final protected function isCreatable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    final public function create()
    {
        // make it impossible to edit using this controller
        abort(403, $this->getPluralModelName().' are not creatable.');
    }

    /**
     * {@inheritdoc}
     */
    final public function store()
    {
        // make it impossible to update using this controller
        abort(403, $this->getPluralModelName().' are not creatable.');
    }

    /**
     * {@inheritdoc}
     */
    final protected function saveResource($resource, $request)
    {
        throw new \LogicException($this->getPluralModelName().' are not creatable.'); // FIXME custom exception
    }

    /**
     * {@inheritdoc}
     */
    final protected function saveLinkedResource($resource, $linkedResource, $request)
    {
        throw new \LogicException($this->getPluralModelName().' with '.$this->getLinkedModelName().' are not creatable.'); // FIXME custom exception
    }
}
