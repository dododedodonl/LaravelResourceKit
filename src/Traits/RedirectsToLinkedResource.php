<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait RedirectsToLinkedResource
{
    /**
     * Returns resource route.
     *
     * @param Illuminate\Database\Eloquent\Model $resource
     * @param Illuminate\Database\Eloquent\Model $linkedResource
     * @param string              $route
     * @param bool                $url            Wether to return an url or a collection. Default: true
     *
     * @return string|\Illuminate\Support\Collection route helper compatible array
     */
    protected function linkedResourceRoute($resource, $linkedResource = false, $route, $url = true)
    {
        $base = $this->linkedResourceBase($route);
        $keys = [
            $this->getLowerCaseModelName()       => $resource->getRouteKey(),
            $this->getLowerCaseLinkedModelName() => $linkedResource->getRouteKey(),

        ];

        if (! $url) {
            return collect(['base' => $base, 'keys' => $keys]);
        }
        return route($base, $keys);
    }

    /**
     * Resource base.
     *
     * @param string $additional this is added with a dot in front of it
     *
     * @return string route name
     */
    protected function linkedResourceBase($additional = '')
    {
        if (strlen($additional) > 0 && substr($additional, 0, 1) != '.') {
            $additional = '.'.$additional;
        }
        return str_plural($this->getLowerCaseModelName()).'.'.lcfirst($this->getLinkedModelName()).$additional;
    }
}
