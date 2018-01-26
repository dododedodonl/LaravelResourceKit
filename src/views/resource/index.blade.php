@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    @include('ResourceKit::components.heading')

    @include('flash::message')

    @include('ResourceKit::components.indexTable')
</div>
@endsection
