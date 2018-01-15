<?php

namespace Dododedodonl\LaravelResourceKit\Traits;

trait RedirectsToResource
{
    /**
     * Returns resource route.
     *
     * @param Illuminate\Database\Eloquent\Model $resource
     * @param string              $route
     * @param bool                $url      Wether to return an url or a collection. Default: true
     *
     * @return string|\Illuminate\Support\Collection route helper compatible array
     */
    protected function resourceRoute($resource, $route, $url = true)
    {
        $base = $this->resourceBase($route);
        $keys = [$this->getLowerCaseModelName() => $resource->getRouteKey()];

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
    protected function resourceBase($additional = '')
    {
        if (strlen($additional) > 0 && substr($additional, 0, 1) != '.') {
            $additional = '.'.$additional;
        }
        return str_plural($this->getLowerCaseModelName()).$additional;
    }

    /**
     * Redirect to route with status.
     *
     * @param Illuminate\Database\Eloquent\Model $route
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToResource($resource)
    {
        $route = $this->resourceRoute($resource, 'show', false);
        $redirect = redirect()->route($route->get('base'), $route->get('keys'));

        return $redirect;
    }

    /**
     * Redirect to the index of this resource.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToIndex()
    {
        $route = $this->resourceBase('index');
        $redirect = redirect()->route($route);

        return $redirect;
    }
}
