@extends('ResourceKit::components.form.element')

@section('field')
<textarea
    class="form-control"
    name="{{ $input['key'] }}"
    id="{{ $input['key'] }}"
    rows="5"
>{{ old($input['key']) ?? $input['value'] }}</textarea>
@overwrite
