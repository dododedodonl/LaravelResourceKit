<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait IsNotEditable
{
    /**
     * {@inheritdoc}
     */
    final protected function isEditable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    final public function edit($id)
    {
        // make it impossible to edit using this controller
        abort(403, $this->getPluralModelName().' are not editable.');
    }

    /**
     * {@inheritdoc}
     */
    final public function update($id)
    {
        // make it impossible to update using this controller
        abort(403, $this->getPluralModelName().' are not editable.');
    }

    /**
     * {@inheritdoc}
     */
    final protected function updateResource($resource, $request)
    {
        throw new \LogicException($this->getPluralModelName().' are not editable.'); // FIXME custom exception
    }

    /**
     * {@inheritdoc}
     */
    final protected function updateLinkedResource($resource, $linkedResource, $request)
    {
        throw new \LogicException($this->getPluralModelName().' with '.$this->getLinkedModelName().' are not editable.'); // FIXME custom exception
    }
}
