@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <h3 class="heading-title">
                <a href="{{ $html->get('backRoute') }}">
                    {{ $html->get('titleString') }}
                </a>
            </h3>
        </div>

        <div class="col-sm-4">
            <div class="pull-right">
                @if ($html->has('actions') && is_array($html->get('actions')))
                        @foreach ($html->get('actions') as $action)
                            <a href="{{ route($action->get('route'), ($action->has('routeParam') ? [$action->get('routeParam') => $resource->id] : [])) }}" class="btn btn-default"{!! $action->has('method') ? ' data-method="'.$action->get('method').'"' : '' !!}>
                                <span class="glyphicon glyphicon-{{ $action->get('glyphicon') }}" aria-hidden="true"></span>
                            </a>
                        @endforeach
                @endif
            </div>
        </div>
    </div>
    @include('flash::message')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="table-responsive">
                <table class="table table-condensed table-show-info">
                    <tbody>
                        @foreach ($resource->getFilteredAttributes() as $key => $value)
                            @php
                                if(is_bool($value)) {
                                    $value = $value ? 'yes' : 'no';
                                }
                            @endphp
                            <tr>
                                <td class="col-xs-4">{{ ucfirst(strtolower(str_ireplace('_', ' ', $key))) }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                        @if ($html->has('relationships'))
                            @foreach ($html->get('relationships') as $relationship)
                                <tr>
                                    <td>{{ $relationship['title'] }}</td>
                                    <td>{{ $resource->{$relationship['link']}->{$relationship['key']} }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
