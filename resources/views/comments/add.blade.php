@extends('layouts.theme1')

@section('content')

<div class="container">

	@component($prefix . '.menu-submenu', ['prefix' => $prefix])@endcomponent

	<h1>@LANG('ui.Add') {{__($title)}}</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
							
		<label for="name" class="control-label">@LANG('ui.Name'):</label>
		<input type="text" name="name" class="form-control" />
		<input type="hidden" name="parent_id" value="0" />

		<label for="comment" class="control-label">@LANG('ui.Comment'):</label>
		<textarea name="comment" class="form-control"></textarea>

		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Add')</button>
		</div>
						
		{{ csrf_field() }}

	</form>

</div>

@endsection
