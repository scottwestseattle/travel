@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>@LANG('ui.Add') {{__($title)}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
							
		<label for="title" class="control-label">@LANG('ui.Name'):</label>
		<input type="text" name="title" class="form-control" />

		<label for="description" class="control-label">@LANG('ui.Comment'):</label>
		<textarea name="description" class="form-control"></textarea>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
		</div>
						
		{{ csrf_field() }}

	</form>

</div>

@endsection
