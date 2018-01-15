<?php

namespace Dododedodonl\LaravelResourceKit;

use Dododedodonl\LaravelResourceKit\Traits\HasResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToResource;
use Dododedodonl\LaravelResourceKit\Traits\ValidatedResourceRequest;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * ResourceController.
 */
class ResourceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use HasResource,
        RedirectsToResource,
        ValidatedResourceRequest;

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (! isset($this->authenticated) || $this->authenticated != false) {
            $this->middleware('auth');
        }
    }

    /**
     * Is this resource deletable.
     *
     * @return bool deletable
     */
    protected function isDeletable()
    {
        return true;
    }

    /**
     * Is this resource editable.
     *
     * @return bool editable
     */
    protected function isEditable()
    {
        return true;
    }

    /**
     * Is this resource creatable.
     *
     * @return bool creatable
     */
    protected function isCreatable()
    {
        return true;
    }

    /**
     * Get html variable with defaults.
     *
     * @param array $additionalElement additional elements to be added
     *
     * @return Collection laravel collection
     */
    protected function viewVariables($resource, $additionalElement = [])
    {
        $defaults = [
            'backRoute'          => $this->resourceRoute($resource, 'show'),
            'titleString'        => ucfirst($this->getLowerCaseModelName()).': '.$resource->titleDescription(),
            'deletable'          => $this->isDeletable(),
            'editable'           => $this->isEditable(),
            'creatable'          => $this->isCreatable(),
        ];
        if ($this->isDeletable()) {
            $defaults['actions'][] = collect(['route' => $this->resourceBase('confirm'), 'routeParam' => $this->getLowerCaseModelName(), 'glyphicon' => 'trash', 'method' => 'delete']);
        }
        if ($this->isEditable()) {
            $defaults['actions'][] = collect(['route' => $this->resourceBase('edit'), 'routeParam' => $this->getLowerCaseModelName(), 'glyphicon' => 'pencil']);
        }
        if ($this->isCreatable()) {
            $defaults['addRoute'] = route($this->resourceBase('create'));
        }
        return collect($additionalElement + $defaults);
    }


    /**
     * Return all resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->getAllResources();

        if ($resources->count() <= 0) {
            return $this->noResourcesPresent();
        }

        $html = $this->viewVariables($resources->first(), [
            'backRoute'   => route($this->resourceBase('index')),
            'titleString' => ucfirst($this->getPluralModelName()),
            'displayId'   => true,
            'show'        => collect([
                'route'      => $this->resourceBase('show'),
                'routeParam' => $this->getLowerCaseModelName(),
            ]),
        ]);

        if (isset($this->relationships)) {
            $html->put('relationships', $this->relationships);
        }

        return view('ResourceKit::resource.index', compact('resources', 'html'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resource = $this->getNewResource();

        $html = $this->viewVariables($resource, [
            'method'      => 'POST',
            'formRoute'   => [$this->resourceBase('store')],
            'buttonText'  => 'Add',
        ]);

        $view = 'ResourceKit::resource.form';
        if (view()->exists($this->resourceBase('form'))) {
            $view = $this->resourceBase('form');
        }

        if (isset($this->relationships)) {
            foreach ($this->relationships as $relationship) {
                $values = resolve($relationship['model'])->all()->mapWithKeys(function ($i) use ($relationship) {
                    return [$i->id => $i->{$relationship['key']}];
                });
                $html->put('relationship_'.$relationship['link'], $values->toArray());
            }
        }

        return view($view, compact('resource', 'html'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $request = $this->validatedRequest();

        $resource = $this->getNewResource();
        $this->saveResource($resource, $request);

        return $this->redirectToResource($resource);
    }

    /**
     * Display resource, or ask confirmation for deletion.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function show($resourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);

        $view = 'ResourceKit::resource.show';
        if (view()->exists($this->resourceBase('show'))) {
            $view = $this->resourceBase('show');
        }

        $html = $this->viewVariables($resource, [
            'backRoute'   => route($this->resourceBase('index')),
            'titleString' => ucfirst($this->getPluralModelName()),
        ]);

        if (isset($this->relationships)) {
            $html->put('relationships', $this->relationships);
        }

        return view($view, compact('resource', 'html') + [$this->getLowerCaseModelName() => $resource]);
    }

    /**
     * Show confirmation screen for deletion.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm($resourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);

        $html = $this->viewVariables($resource, [
            'deleteRoute'    => $this->resourceRoute($resource, 'destroy'),
            'resourceTitle'  => $this->getLowerCaseModelName(),
        ]);

        return view('ResourceKit::resource.delete', compact('resource', 'html'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($resourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);

        $html = $this->viewVariables($resource, [
            'method'      => 'PATCH',
            'formRoute'   => [$this->resourceBase('update'), $resourceId],
            'buttonText'  => 'Edit',
        ]);

        $view = 'ResourceKit::resource.form';
        if (view()->exists($this->resourceBase('form'))) {
            $view = $this->resourceBase('form');
        }

        if (isset($this->relationships)) {
            foreach ($this->relationships as $relationship) {
                $values = resolve($relationship['model'])->all()->mapWithKeys(function ($i) use ($relationship) {
                    return [$i->id => $i->{$relationship['key']}];
                });
                $html->put('relationship_'.$relationship['link'], $values->toArray());
            }
        }

        return view($view, compact('resource', 'html') + [$this->getLowerCaseModelName() => $resource]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function update($resourceId)
    {
        $request = $this->validatedRequest();

        $resource = $this->getResourceOrFail($resourceId);

        $this->updateResource($resource, $request);

        return $this->redirectToResource($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($resourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);

        $this->deleteResource($resource);

        return $this->redirectToIndex();
    }

    /**
     * Update resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model      $resource
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function updateResource($resource, $request)
    {
        $resource->fill($request->only($resource->getFillable()));

        $resource->save();

        flash($this->getModelName().' has been updated.')->success();
    }


    /**
     * Create resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model      $resource
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function saveResource($resource, $request)
    {
        $resource->fill($request->only($resource->getFillable()));

        $resource->save();

        flash($this->getModelName().' has been added.')->success();
    }

    /*
     * Delete resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model      $resource
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function deleteResource($resource)
    {
        $resource->delete();

        flash($this->getModelName().' has been deleted.')->success();
    }
}
