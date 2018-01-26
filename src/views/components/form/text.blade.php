@extends('ResourceKit::components.form.element')

@section('field')
<input
    type="text"
    class="form-control"
    name="{{ $input['key'] }}"
    id="{{ $input['key'] }}"
    value="{{ $input['value'] }}"
/>
@overwrite
