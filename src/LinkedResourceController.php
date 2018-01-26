<?php

namespace Dododedodonl\LaravelResourceKit;

use Dododedodonl\LaravelResourceKit\Traits\HasResource;
use Dododedodonl\LaravelResourceKit\Traits\HasLinkedResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToLinkedResource;
use Dododedodonl\LaravelResourceKit\Traits\ValidatedLinkedResourceRequest;
use Dododedodonl\LaravelResourceKit\Helpers\Action;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LinkedResourceController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    use HasResource,
        RedirectsToResource,
        RedirectsToLinkedResource,
        HasLinkedResource,
        ValidatedLinkedResourceRequest;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Authenticate by default
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
        return collect($additionalElement + [
            'backRoute'   => $this->resourceRoute($resource, 'show'),
            'titleString' => ucfirst($this->getLowerCaseModelname()).': '.$resource->titleDescription(),
            'deletable'   => $this->isDeletable(),
            'editable'    => $this->isEditable(),
            'creatable'   => $this->isCreatable(),
        ]);
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
        if(view()->exists($this->linkedResourceBase($view))) {
            return $this->linkedResourceBase($view);
        }
        return 'ResourceKit::resource.'.$view;
    }


    /**
     * Show overview of linked resources.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($resourceId) {
        $resource = $this->getResourceOrFail($resourceId);

        return $this->redirectToResource($resource);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function create($resourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getNewLinkedResource();

        $presenter = $this->presenter($resource, [
            'formMethod'    => 'POST',
            'formAction'    => route($this->linkedResourceBase('store'), $resourceId),
            'formButton'    => 'Add',
        ]);

        return view($this->defaultOrCustomView('form'), compact('presenter'))
            ->with('resource', $linkedResource);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param int $memberId
     *
     * @return \Illuminate\Http\Response
     */
    public function store($resourceId)
    {
        $request = $this->validatedRequest();

        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getNewLinkedResource();
        $this->saveLinkedResource($resource, $linkedResource, $request);

        return $this->redirectToResource($resource);
    }

    /**
     * Display resource.
     *
     * @param int $resourceId
     * @param int $linkedResourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function show($resourceId, $linkedResourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);

        return $this->redirectToResource($resource);
    }


    /**
     * Show confirmation screen for deletion.
     *
     * @param int $resourceId
     * @param int $linkedResourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm($resourceId, $linkedResourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getLinkedResourceOrFail($resource, $linkedResourceId);

        $presenter = $this->presenter($resource, [
            'formAction'        => $this->linkedResourceRoute($resource, $linkedResource, 'destroy'),
            'formMethod'        => 'delete',
            'formButton'        => 'delete this '.$this->getLowerCaseLinkedModelName(),
            'formButtonClass'   => 'btn-danger',
            'resourceTitle'     => $this->getLinkedModelName(),
        ]);

        return view($this->defaultOrCustomView('confirm'), compact('resource', 'linkedResource', 'presenter'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $resourceId
     * @param int $linkedResourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($resourceId, $linkedResourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getLinkedResourceOrFail($resource, $linkedResourceId);

        $presenter = $this->presenter($resource, [
            'formMethod' => 'PATCH',
            'formAction' => route($this->linkedResourceBase('update'), [$resourceId, $linkedResourceId]),
            'formButton' => 'Edit',
        ]);

        return view($this->defaultOrCustomView('form'), compact('presenter'))
            ->with('resource', $linkedResource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $resourceId
     * @param int $linkedResourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function update($resourceId, $linkedResourceId)
    {
        $request = $this->validatedRequest();

        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getLinkedResourceOrFail($resource, $linkedResourceId);

        $this->updateLinkedResource($resource, $linkedResource, $request);

        return $this->redirectToResource($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $resourceId
     * @param int $linkedResourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($resourceId, $linkedResourceId)
    {
        $resource = $this->getResourceOrFail($resourceId);
        $linkedResource = $this->getLinkedResourceOrFail($resource, $linkedResourceId);

        $this->deleteLinkedResource($resource, $linkedResource);

        return $this->redirectToResource($resource);
    }

    /**
     * Save the linked resource to the resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model      $resource
     * @param \Illuminate\Database\Eloquent\Model      $linkedResource
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function saveLinkedResource($resource, $linkedResource, $request) {
        $linkedResource->fill($request->only($linkedResource->getFillable()));

        $this->getLinkOnResource($resource)->save($linkedResource);

        flash($this->getLinkedModelName().' has been added.')->success();
    }

    /**
     * Update linked resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model      $resource
     * @param \Illuminate\Database\Eloquent\Model      $linkedResource
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function updateLinkedResource($resource, $linkedResource, $request) {
        $linkedResource->fill($request->only($linkedResource->getFillable()));

        $this->getLinkOnResource($resource)->save($linkedResource);

        flash($this->getLinkedModelName().' has been updated.')->success();
    }

    /**
     * Delete linked resource.
     * Send a result message using the flash() method.
     *
     * @param \Illuminate\Database\Eloquent\Model $resource
     * @param \Illuminate\Database\Eloquent\Model $linkedResource
     *
     * @return void
     */
    protected function deleteLinkedResource($resource, $linkedResource) {
        $linkedResource->delete();

        flash($this->getLinkedModelName().' has been deleted.')->success();
    }
}
