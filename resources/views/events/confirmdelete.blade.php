@extends('layouts.theme1')

@section('content')

<?php
	$type = '';
	if ($record->type_flag == 1) $type = 'Info';
	if ($record->type_flag == 2) $type = 'Warning';
	if ($record->type_flag == 3) $type = 'Error';
	if ($record->type_flag == 4) $type = 'Exception';
	if ($record->type_flag == 5) $type = 'Other';
?>

<div class="container page-size">
	
	<h1>@LANG('ui.Delete') @LANG('content.Event')</h1>

	<form method="POST" action="/{{$prefix}}/delete/{{ $record->id }}">
			 
		<div class="form-group"><button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button></div>		
	
		<h3>{{$type}}</h3>
		<p>{{$record->created_at}}</p>
		<p>{{$record->model_flag}} / {{$record->action_flag}}</p>
		<p>{{$record->title }}</p>
		<p>{{$record->description}}</p>
		
		<div class="submit-button">
			<button type="submit" class="btn btn-primary">@LANG('ui.Confirm Delete')</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>
@endsection
