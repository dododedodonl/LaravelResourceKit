@extends(config('resourcekit.layout'))

@section('content')
<div class="container">
    @include('ResourceKit::components.heading')

    @include('flash::message')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="table-responsive">
                <table class="table table-condensed table-show-info">
                    <tbody>
                        @foreach ($resource->presentableAttributes() as $key => $value)
                            <tr>
                                <td class="col-xs-4">
                                    {{ $resource->keyToTitle($key) }}
                                </td>
                                <td>
                                    {{ $value }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @forelse ($linkedResources as $linkedResource)
        @include('ResourceKit::components.heading', $linkedResource['heading'])
        @include('ResourceKit::components.indexTable', $linkedResource['index'])
    @empty
    @endforelse
</div>

@endsection
