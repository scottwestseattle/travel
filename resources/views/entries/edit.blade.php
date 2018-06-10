@extends('layouts.app')

@section('content')

<div class="container">

	@component('menu-submenu-entries', ['record_id' => $record->id, 'record_permalink' => $record->permalink])
	@endcomponent	

	<h1>Edit Entry</h1>

	<form method="POST" action="/entries/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="referer" value={{$_SERVER["HTTP_REFERER"]}} />
		
			@component('control-entry-types', ['current_type' => $record->type_flag, 'entryTypes' => $entryTypes])
			@endcomponent			
		
			<input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />

			<input type="text" id="display_date" name="display_date" class="form-control" value="{{ $record->display_date }}"  placeholder="Display Date: YYYY-MM-DD" />
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncodeWithDate('title', 'display_date', 'permalink')";>
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

			<div class="entry-description-div">
				<textarea name="description_short" class="form-control entry-description-text" placeholder="Highlights" >{{ $record->description_short }}</textarea>
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