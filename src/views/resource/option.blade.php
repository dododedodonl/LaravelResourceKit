@extends(config('resourcekit.layout'))

@section('heading')
    <h3 class="heading-title">
        <a href="{{ $html->get('backRoute') }}">
            {{ $html->get('titleString') }}
        </a>
    </h3>
@endsection

@section('content')
<div class="container">
    @yield('heading')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
    {!! Form::model($resource, ['route' => $html->get('formRoute'), 'method' => $html->get('method'), 'class' => 'form-horizontal']) !!}
    <div class="panel panel-info">
        <div class="panel-body">
            @foreach ($resourceOptions as $key => $value)
                <div class="form-group{{ $errors->has($key) ? ' has-error' : '' }}">
                    {!! Form::label($key, ucfirst(strtolower(str_ireplace('_', ' ', $key))), ['class' => 'control-label col-sm-4']); !!}
                    <div class="col-sm-8">
                        @if (is_bool($value))
                            <div class="radio">
                                <label>{!! Form::radio($key, '1', ($value == true)) !!} yes</label>
                            </div>
                            <div class="radio">
                                <label>{!! Form::radio($key, '0', ($value == false)) !!} no</label>
                            </div>

                        @elseif ($key == 'comment')
                            {!! Form::textarea($key, null, ['class' => 'form-control' ]) !!}

                        @elseif (is_object($value) && ($value instanceof Carbon\Carbon))
                            {!! Form::date($key, null, ['class' => 'form-control' ]) !!}

                        @else
                            {!! Form::text($key, null, ['class' => 'form-control' ]) !!}

                        @endif

                        @if ($errors->has($key))
                            <span class="help-block">
                            @foreach ($errors->get($key) as $error)
                                @if ( ! $loop->first)
                                    <br>
                                @endif
                                {{ $error }}
                            @endforeach
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <div class="panel-footer">
            <div class="pull-right">
                <a href="{{ $html->get('backRoute') }}" class="btn btn-default">
                    Go back
                </a>
                <button type="submit" class="btn btn-primary">{{ $html->get('buttonText') ?: 'Save' }}</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection
