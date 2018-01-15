<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait IsNotDeletable
{
    /**
     * {@inheritdoc}
     */
    final protected function isDeletable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    final public function confirm($id)
    {
        // make it impossible to confirm using this controller
        abort(403, $this->getPluralModelName().' are not deletable.');
    }

    /**
     * {@inheritdoc}
     */
    final public function destroy($id)
    {
        // make it impossible to destroy using this controller
        abort(403, $this->getPluralModelName().' are not deletable.');
    }

    /**
     * {@inheritdoc}
     */
    final protected function deleteResource($resource)
    {
        throw new \LogicException($this->getPluralModelName().' are not deletable.'); // FIXME custom exception
    }

    /**
     * {@inheritdoc}
     */
    final protected function deleteLinkedResource($resource, $linkedResource)
    {
        throw new \LogicException($this->getPluralModelName().' with '.$this->getLinkedModelName().' are not deletable.'); // FIXME custom exception
    }
}
