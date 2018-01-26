@extends('ResourceKit::components.form.element')

@section('field')
    @foreach ($input['options'] as $option)
        <div class="radio">
            <label>
                <input
                    type="radio"
                    name="{{ $input['key'] }}"
                    id="{{ $input['key'] }}"
                    value="{{ $option['value'] }}"
                    {!! old($input['key'], $input['value']) == $option['value'] ? 'checked="checked"' : '' !!}
                /> {{ $option['title'] }}
            </label>
        </div>
    @endforeach
@overwrite
