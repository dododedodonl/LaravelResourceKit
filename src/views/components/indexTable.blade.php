<div class="row">
    <div class="col-md-12">
    @if ($resources->count() > 0)
        <div class="table-responsive">
            <table class="table table-condensed table-striped table-hover show-all-resources">
                <thead>
                    <tr>
                        @if ($presenter->has('showId') && $presenter->get('showId') == true)
                            <td class="col-sm-1">
                                Id
                            </td>
                        @endif

                        @foreach ($resources->first()->presentableAttributes() as $key => $value)
                            <td>
                                {{ $resources->first()->keyToTitle($key) }}
                            </td>
                        @endforeach

                        @if ($presenter->has('actions') && count($presenter->get('actions')) > 0)
                            <td class="col-sm-1">
                                Actions
                            </td>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resources as $resource)
                        @if ($presenter->has('showAction'))
                            <tr onclick="javascript:window.location='{{ $presenter->get('showAction')->url($resource->pathAttribute()) }}'">
                        @else
                            <tr>
                        @endif

                        @if ($presenter->has('showId') && $presenter->get('showId') == true)
                            <td>
                                {{ $resource->id }}
                            </td>
                        @endif

                        @foreach ($resource->presentableAttributes() as $key => $value)
                            <td>
                                {{ $value }}
                            </td>
                        @endforeach

                        @if ($presenter->has('actions') && count($presenter->get('actions')) > 0)
                            <td>
                                @foreach($presenter->get('actions') AS $action)
                                    <a href="{{ $action->url($resource->pathAttribute()) }}" class="btn btn-link btn-sm">
                                        @if (isset($action->glyphicon))
                                            <span class="glyphicon glyphicon-{{ $action->glyphicon }}" aria-hidden="true"></span>
                                        @endif
                                        @if (isset($action->title))
                                            {{ $action->title }}
                                        @endif
                                    </a>
                                @endforeach
                            </td>
                        @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-danger">
            <strong>Whoops...</strong> Nothing to display.
        </div>
    @endif
    </div>
</div>
