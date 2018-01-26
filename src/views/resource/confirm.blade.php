@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <h3 class="heading-title">
        <a href="{{ $presenter->get('backPath') }}">
            {{ $presenter->get('backTitle') }}
        </a>
    </h3>

    <div class="panel panel-danger">
        @if (isset($resource))
            <div class="panel-heading">
                <h3 class="panel-title">
                    Are you sure you want to delete this {{ $presenter->get('resourceTitle') ?? '' }}?
                </h3>
            </div>
            <table class="table table-condensed table-show-info">
                <tbody>
                    @foreach($resource->presentableAttributes() as $key => $value)
                        <tr>
                            <td class="col-sm-4">
                                {{ $resource->keyToTitle($key) }}
                            </td>
                            <td>
                                {{ $value }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="panel-body">
                <p>
                    {{ $presenter->get('panelBody') }}
                </p>
            </div>
        @endif
        <div class="panel-footer">
            <div class="pull-right">
                <a href="{{ $presenter->get('backPath') }}" class="btn btn-default">
                    Go back
                </a>

                <form method="post" action="{{ $presenter->get('formAction') }}" style="display: inline-block;">
                    {!! csrf_field() !!}
                    {!! method_field($presenter->get('formMethod')) !!}
                    <button type="submit" class="btn {{ $presenter->get('formButtonClass') ?? 'btn-primary'}}">
                        Yes{{ $presenter->has('formButton') ? ', '.$presenter->get('formButton') : '' }}
                    </button>
                </form>
            </div>
            <div class="clearfix"><div>
        </div>
    </div>
</div>
@endsection
