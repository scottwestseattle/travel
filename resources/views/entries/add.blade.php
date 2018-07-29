@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('entries.menu-submenu')@endcomponent	

	@if (isset($title))
		<h3>{{$title}}</h3>
	@endif
	
	<h1>Add Entry</h1>
               
	<form method="POST" action="/entries/create">
		<div class="form-control-big">	

			@if (isset($type_flag))
				<input type="hidden" name="type_flag" value="{{$type_flag}}">
			@else
				@component('control-entry-types', ['entryTypes' => $entryTypes])@endcomponent	
			@endif
				
			@if (isset($parent_id))
				<input type="hidden" name="parent_id" value="{{$parent_id}}">
			@endif

			@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		
			
			<div class="entry-title-div">
				<input type="text" id="title" name="title" placeholder="Title" class="form-control" />
			</div>
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncodeWithDate('title', 'year', 'month', 'day', 'permalink')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" id="permalink" name="permalink" class="form-control"  placeholder="Permalink" />
			</div>			

			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			<div class="entry-description-div">
				<textarea rows="12" name="description" class="form-control" placeholder="Description"></textarea>	
			</div>
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
