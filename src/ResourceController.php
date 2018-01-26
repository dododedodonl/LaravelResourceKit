<?php

namespace Dododedodonl\LaravelResourceKit;

use Dododedodonl\LaravelResourceKit\Traits\HasResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToResource;
use Dododedodonl\LaravelResourceKit\Traits\ValidatedResourceRequest;
use Dododedodonl\LaravelResourceKit\Helpers\Action;

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
     * What linked resources should be shown.
     * Creatable/Editable/Deletable determined by route presence
     *
     * link_on_model
     * link_on_model => route_key
     *
     * @var array
     */
    protected $show_linked_resources = [];

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

    protected function presenter($resource, $additionalElements = [])
    {
        $actions = [];

        if($this->isDeletable())
        {
            $actions[] = new Action(
                $this->resourceBase('confirm'),
                'trash'
            );
        }

        if($this->isEditable())
        {
            $actions[] = new Action(
                $this->resourceBase('edit'),
                'pencil'
            );
        }

        $create = [];
        if($this->isCreatable())
        {
            $create['createAction'] = new Action(
                $this->resourceBase('create'),
                'plus'
            );
        }

        return collect([
            'backPath'  => $this->resourceRoute($resource, 'show'),
            'backTitle' => ucfirst($this->getLowerCaseModelName().': '.$resource->titleDescription()),
            'actions'   => $actions,
        ])->merge($create)->merge($additionalElements);
    }

    protected function defaultOrCustomView($view)
    {
        if(view()->exists($this->resourceBase($view))) {
            return $this->resourceBase($view);
        }
        return 'ResourceKit::resource.'.$view;
    }

    /**
     * Return all resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resources = $this->getAllResources();

        if ($resources->count() <= 0) return $this->noResourcesPresent();

        $presenter = $this->presenter($resources->first(), [
            'backPath'      => route($this->resourceBase('index')),
            'backTitle'     => ucfirst($this->getPluralModelName()),
            'showId'        => true,
            'showAction'    => new Action(
                $this->resourceBase('show')
            ),
        ]);

        // if (isset($this->relationships)) {
        //     $html->put('relationships', $this->relationships);
        // }

        return view($this->defaultOrCustomView('index'), compact('resources', 'presenter'))
            ->with($this->getPluralModelName(), $resources);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $resource = $this->getNewResource();

        $view = $this->defaultOrCustomView('form');

        $presenter = $this->presenter($resource, [
            'formMethod' => 'POST',
            'formAction' => route($this->resourceBase('store')),
            'formButton' => 'Add',
        ]);

        $presenter->forget('createAction');
        $presenter->forget('actions');

        //TODO: move to model
        // if (isset($this->relationships)) {
        //     foreach ($this->relationships as $relationship) {
        //         $values = resolve($relationship['model'])->all()->mapWithKeys(function ($i) use ($relationship) {
        //             return [$i->id => $i->{$relationship['key']}];
        //         });
        //         $html->put('relationship_'.$relationship['link'], $values->toArray());
        //     }
        // }

        return view($view, compact('resource', 'presenter'))
            ->with($this->getLowerCaseModelName(), $resource);
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

        $view = $this->defaultOrCustomView('show');

        $presenter = $this->presenter($resource, [
            'backPath'  => route($this->resourceBase('index')),
            'backTitle' => ucfirst($this->getPluralModelName()),
        ]);

        $presenter->forget('createAction');

        $linkedResources = $this->linkedResources($resource);

        return view($view, compact('resource', 'presenter', 'linkedResources'))
            ->with($this->getLowerCaseModelName(), $resource);
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

        $presenter = $this->presenter($resource, [
            'formAction'        => $this->resourceRoute($resource, 'destroy'),
            'formMethod'        => 'delete',
            'formButton'        => 'delete this '.$this->getLowerCaseModelName(),
            'formButtonClass'   => 'btn-danger',
            'resourceTitle'     => $this->getLowerCaseModelName(),
        ]);

        return view($this->defaultOrCustomView('confirm'), compact('resource', 'presenter'));
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

        $view = $this->defaultOrCustomView('form');

        $presenter = $this->presenter($resource, [
            'formMethod' => 'PATCH',
            'formAction' => route($this->resourceBase('update'), $resourceId),
            'formButton' => 'Edit',
        ]);

        $presenter->forget('createAction');
        $presenter->forget('actions');

        //TODO: move to model
        // if (isset($this->relationships)) {
        //     foreach ($this->relationships as $relationship) {
        //         $values = resolve($relationship['model'])->all()->mapWithKeys(function ($i) use ($relationship) {
        //             return [$i->id => $i->{$relationship['key']}];
        //         });
        //         $html->put('relationship_'.$relationship['link'], $values->toArray());
        //     }
        // }

        return view($view, compact('resource', 'presenter'))
            ->with($this->getLowerCaseModelName(), $resource);
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

    protected function linkedResources($resource)
    {
        $linkedResources = [];
        foreach($this->show_linked_resources as $link => $route_key) {
            if(is_numeric($link)) {
                $link = $route_key;
                $route_key = str_singular($link);
            }

            $base = $this->resourceBase($route_key.'.');
            $actions = [];
            $create = [];
            if(\Route::has($base.'edit')) {
                $actions[] = new Action(
                    $base.'edit',
                    'pencil',
                    null,
                    [$resource->id]
                );
            }
            if(\Route::has($base.'confirm')) {
                $actions[] = new Action(
                    $base.'confirm',
                    'trash',
                    null,
                    [$resource->id]
                );
            }
            if(\Route::has($base.'create')) {
                $create['createAction'] = new Action(
                    $base.'create',
                    'plus',
                    null,
                    [$resource->id]
                );
            }

            $linkedResources[] = [
                'index'     => [
                    'resources' => $resource->{$link},
                    'presenter' => collect([
                        'actions' => $actions
                    ]),
                ],
                'heading'   => [
                    'presenter' => collect([
                        'title'     => ucfirst($link),
                    ])->merge($create),

                ],
            ];
        }

        return $linkedResources;
    }
}
