@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    <h3 class="heading-title">
        <a href="{{ $presenter->get('backPath') }}">
            {{ $presenter->get('backTitle') }}
        </a>
    </h3>

    @include('flash::message')

    @if (count($resource->attributeInputs()) > 0)
        <form class="form-horizontal" method="post" action="{{ $presenter->get('formAction') }}">
            {!! csrf_field() !!}
            {!! method_field($presenter->get('formMethod')) !!}
            @foreach($resource->attributeInputs() as $key => $input)
                @include('ResourceKit::components.form.'.$input['type'])
            @endforeach

            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-primary">
                        {{ $presenter->get('formButton') }}
                    </button>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-danger">
            <strong>Whoops...</strong> It seems like no fields can be changed.
        </div>
    @endif
</div>
@endsection
