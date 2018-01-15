<?php

namespace Dododedodonl\LaravelResourceKit;

use Dododedodonl\LaravelResourceKit\Traits\HasResource;
use Dododedodonl\LaravelResourceKit\Traits\HasLinkedResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToResource;
use Dododedodonl\LaravelResourceKit\Traits\RedirectsToLinkedResource;
use Dododedodonl\LaravelResourceKit\Traits\ValidatedLinkedResourceRequest;

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



    /**
     * Show overview of linked resources.
     *
     * @param int $resourceId
     *
     * @return \Illuminate\Http\Response
     */
    public function index($resourceId) {
        return 'Not implemented yet.';
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

        $html = $this->viewVariables($resource, [
            'method'      => 'POST',
            'formRoute'   => [$this->linkedResourceBase('store'), $resourceId],
            'buttonText'  => 'Add',
        ]);

        $view = 'ResourceKit::linkedresource.form';
        if (view()->exists($this->linkedResourceBase('form'))) {
            $view = $this->linkedResourceBase('form');
        }

        return view($view, compact('resource', 'linkedResource', 'html'));
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
        //NOTE add? show linked resource form instead of redirection
        $resource = $this->getResourceOrFail($resourceId);

        flash('Redirected from linked resource (#'.$linkedResourceId.') (not implemented yet).');
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

        $html = $this->viewVariables($resource, [
            'deleteRoute'    => $this->linkedResourceRoute($resource, $linkedResource, 'destroy'),
            'resourceTitle'  => $this->getLinkedModelName(),
        ]);

        return view('ResourceKit::linkedresource.delete', compact('resource', 'linkedResource', 'html'));
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

        $html = $this->viewVariables($resource, [
            'method'      => 'PATCH',
            'formRoute'   => [$this->linkedResourceBase('update'), $resourceId, $linkedResourceId],
            'buttonText'  => 'Edit',
        ]);

        $view = 'ResourceKit::linkedresource.form';
        if (view()->exists($this->linkedResourceBase('form'))) {
            $view = $this->linkedResourceBase('form');
        }

        return view($view, compact('resource', 'linkedResource', 'html'));
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
