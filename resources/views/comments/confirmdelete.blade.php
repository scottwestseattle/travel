@extends('layouts.theme1')

@section('content')

<div class="container page-size">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent
	
	<h1>@LANG('ui.Delete') @LANG('content.Comment')</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			   
		<h3 name="title" class="">{{$record->title }}</h3>

		<div class="form-group">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>		
	
		<p>{{$record->created_at}}</p>

		<p>{{$record->name}}</p>

		<p>{{$record->comment}}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
