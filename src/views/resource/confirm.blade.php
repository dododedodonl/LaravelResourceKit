@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <h3 class="heading-title">
        <a href="{{ $html->get('backRoute') }}">
            {{ $html->get('titleString') }}
        </a>
    </h3>
    <div class="panel panel-info">
        <div class="panel-body">
            <p>{{ $html->get('panelBody') }}</p>
        </div>
        <div class="panel-footer">
            <div class="pull-right">
            <a href="{{ $html->get('backRoute') }}" class="btn btn-default">
                Go back
            </a>

            <form method="POST" action="{{ $html->get('confirmRoute')}}" style="display:inline-block;">
                <input type="hidden" name="_method" value="patch">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="btn btn-primary">Yes{{ $html->get('confirmText') ? ', '.$html->get('confirmText') : '' }}</button>
            </form>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
@endsection
