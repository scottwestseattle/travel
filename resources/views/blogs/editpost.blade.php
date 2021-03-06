@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('entries.menu-submenu', ['record' => $record-])@endcomponent	

	<h1>Edit</h1>

	<form method="POST" action="/blogs/updatepost/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="referer" value={{array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER["HTTP_REFERER"] : ''}} />
		
			@component('components.control-dropdown-blogs')@endcomponent			
		
			<input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />

			<input type="text" id="display_date" name="display_date" class="form-control" value="{{ $record->display_date }}"  placeholder="Display Date: YYYY-MM-DD" />
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncodeWithDate('title', 'year', 'month', 'day', 'permalink')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" id="permalink" name="permalink" class="form-control" value="{{ $record->permalink }}"  placeholder="Permalink" />
			</div>
			
			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			<div class="entry-description-div">
				<textarea name="description" rows="12" class="form-control" placeholder="Description" >{{ $record->description }}</textarea>
			</div>
			
			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@endsection