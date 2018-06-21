@extends('layouts.theme1')

@section('content')

<div class="container">

	@component('templates.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h1>Edit {{$title}}</h1>

	<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

		<label for="title" class="control-label">Title:</label>
		<input type="text" name="title" class="form-control" value="{{$record->title}}"></input>	
					
		<label for="permalink" class="control-label">Permalink:</label>
		<input type="text" name="permalink" class="form-control" value="{{$record->permalink}}"></input>

		<label for="description" class="control-label">Description:</label>
		<textarea name="description" class="form-control">{{$record->description}}</textarea>
			
		<div class="form-group">
			<input type="checkbox" name="published_flag" id="published_flag" class="form-control-inline" value="{{$record->published_flag }}" {{ ($record->published_flag) ? 'checked' : '' }} />
			<label for="published_flag" class="checkbox-label">Published</label>
		</div>

		<div class="form-group">		
			<div class="radio-group-item">
				<input type="radio" name="radio_sample" value="1" class="form-control-inline" {{$record->published_flag ? 'checked' : '' }} />
				<label for="radio_sample" class="radio-label">Sample Radio Option 1</label>
			</div>
			
			<div class="radio-group-item">
				<input type="radio" name="radio_sample" value="2" class="form-control-inline" {{$record->published_flag ? 'checked' : '' }} />
				<label for="radio_sample" class="radio-label">Sample Radio Option 2</label>			
			</div>	
		</div>
			
		<div class="submit-button">
			<button type="submit" name="update" class="btn btn-primary">Update</button>
		</div>

		{{ csrf_field() }}
		
	</form>

</div>

@stop
