@extends('layouts.app')

@section('content')

@component('menu-submenu', ['data' => $data])
	@component('menu-icons-start')@endcomponent
@endcomponent

<div class="container">
                <h1>Add</h1>
               
<form method="POST" action="/tasks/create">

    <div class="form-group">
        <input type="text" name="description" class="form-control" placeholder="Description"></input>
    </div>

    <div class="form-group">
        <input type="text" name="link" class="form-control"  placeholder="Link"></input>
    </div>
	

    <div class="form-group">
        <button type="submit" class="btn btn-primary">Add</button>
    </div>
{{ csrf_field() }}
</form>


</div>
@endsection
