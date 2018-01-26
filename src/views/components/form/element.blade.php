<div class="form-group{{ $errors->has($input['key']) ? ' has-error' : '' }}">
    <label for="{{ $input['key'] }}" class="control-label col-sm-4">{{ $input['title'] }}</label>

    <div class="col-sm-8">
        @yield('field')
    </div>

    @if ($errors->has($input['key']))
        <div class="col-sm-8 col-sm-offset-4">
            <span class="help-block">
            @foreach ($errors->get($input['key']) as $error)
                @if ( ! $loop->first)
                    <br / />
                @endif
                {{ $error }}
            @endforeach
            </span>
        </div>
    @endif
</div>
