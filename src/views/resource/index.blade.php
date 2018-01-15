@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h3 class="heading-title">
                <a href="{{ $html->get('backRoute') }}">
                    {{ $html->get('titleString') }}
                </a>
            </h3>
        </div>
        <div class="col-md-4">
            <div class="pull-right">
                @if ($html->has('addRoute'))
                    <a href="{{ $html->get('addRoute') }}" class="btn btn-default"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span></a>
                @endif
            </div>
        </div>
    </div>
    @include('flash::message')
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-condensed table-striped table-hover show-all-resources">
                    <thead>
                        <tr>
                            @if ($html->has('displayId'))
                                <td>Id</td>
                            @endif
                            @foreach($resources->first()->getFilteredAttributes() as $key => $value)
                                <td>{{ ucfirst(strtolower(str_ireplace('_', ' ', $key))) }}</td>
                            @endforeach
                            @if ($html->has('relationships'))
                                @foreach ($html->get('relationships') as $relationship)
                                    <td>{{ $relationship['title'] }}</td>
                                @endforeach
                            @endif
                            @if ($html->has('actions') && is_array($html->get('actions')))
                                <td class="col-md-1">Actions</td>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resources as $resource)
                            @if ($html->has('show'))
                                <tr onclick="javascript:window.location='{{ route($html->get('show')->get('route'), ($html->get('show')->has('routeParam') ? [$html->get('show')->get('routeParam') => $resource->id] : [])) }}';">
                            @else
                                <tr>
                            @endif
                            @if ($html->has('displayId'))
                                <td class="col-sm-1">{{ $resource->id }}</td>
                            @endif
                            @foreach($resource->getFilteredAttributes() as $key => $value)
                                @php
                                    if(is_bool($value)) {
                                        $value = $value ? 'yes' : 'no';
                                    }
                                @endphp
                                <td>{{ $value }}</td>
                            @endforeach
                            @if ($html->has('relationships'))
                                @foreach ($html->get('relationships') as $relationship)
                                    <td>{{ $resource->{$relationship['link']}->{$relationship['key']} }}</td>
                                @endforeach
                            @endif
                            @if ($html->has('actions') && is_array($html->get('actions')))
                                <td>
                                    @foreach ($html->get('actions') as $action)
                                        <a href="{{ route($action->get('route'), ($action->has('routeParam') ? [$action->get('routeParam') => $resource->id] : [])) }}" class="btn btn-link btn-sm"{!! $action->has('method') ? ' data-method="'.$action->get('method').'"' : '' !!}>
                                            <span class="glyphicon glyphicon-{{ $action->get('glyphicon') }}" aria-hidden="true"></span>
                                        </a>
                                    @endforeach
                                </td>
                            @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
