<div class="row">
    <div class="col-sm-8">
        <h3 class="heading-title">
            @if ($presenter->has('backPath'))
                <a href="{{ $presenter->get('backPath') }}">
                    {{ $presenter->get('backTitle') }}
                </a>
            @else
                {{ $presenter->get('title') }}
            @endif
        </h3>
    </div>

    @if ($presenter->has('createAction'))
    <div class="col-sm-4">
        <div class="pull-right">
            <a href="{{ $presenter->get('createAction')->url() }}" class="btn btn-default">
                @if (isset($presenter->get('createAction')->glyphicon))
                    <span class="glyphicon glyphicon-{{ $presenter->get('createAction')->glyphicon }}" aria-hidden="true"></span>
                @endif
                @if (isset($presenter->get('createAction')->title))
                    {{ $presenter->get('createAction')->title }}
                @endif
            </a>
        </div>
    </div>

    @elseif ($presenter->has('actions'))
    <div class="col-sm-4">
        <div class="pull-right">
            @foreach ($presenter->get('actions') AS $action)
                <a href="{{ $action->url($resource->pathAttribute()) }}" class="btn btn-default">
                    @if (isset($action->glyphicon))
                        <span class="glyphicon glyphicon-{{ $action->glyphicon }}" aria-hidden="true"></span>
                    @endif
                    @if (isset($action->title))
                        {{ $action->title }}
                    @endif
                </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
