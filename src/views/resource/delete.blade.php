@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <h3 class="heading-title">
        <a href="{{ $html->get('backRoute') }}">
            {{ $html->get('titleString') }}
        </a>
    </h3>
    <div class="panel panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Are you sure you want to delete this{{ $html->get('resourceTitle') ? ' '.$html->get('resourceTitle') : '' }}?</h3>
        </div>
        <table class="table table-condensed table-show-info">
            <tbody>
            @foreach ($resource->getFilteredAttributes() as $key => $value)
                <tr>
                    <td class="col-sm-4">{{ ucfirst(strtolower(str_ireplace('_', ' ', $key))) }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="panel-footer">
            <div class="pull-right">
                <a href="{{ $html->get('backRoute') }}" class="btn btn-default">
                    Go back
                </a>
                <form method="POST" action="{{ $html->get('deleteRoute')}}" style="display:inline-block;">
                    <input type="hidden" name="_method" value="delete">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="btn btn-danger">Yes, delete this {{ $html->get('resourceTitle') }}</button>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection
