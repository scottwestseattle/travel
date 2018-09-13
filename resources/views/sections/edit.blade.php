@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('sections.menu-submenu', ['record' => $record])@endcomponent	

	<h1>Edit Section</h1>

	<form method="POST" action="/sections/update/{{$record->id}}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="type_flag" value="{{$record->type_flag}}" />
			<input type="hidden" name="site_id" value="{{$record->site_id}}">
			<input type="hidden" name="approved_flag" value="1">

			<input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />
			
			<div class="entry-title-div">
				<input type="text" id="permalink" name="permalink" value="{{$record->permalink}}" placeholder="Permalink: section-name" class="form-control" />
			</div>
			
			<!-- div class="form-group">
				<input type="checkbox" name="published_flag" id="published_flag" class="" value="{{$record->published_flag }}" {{ ($record->published_flag) ? 'checked' : '' }} />
				<label for="published_flag" class="checkbox-big-label">Published</label>
			</div -->

			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			<div class="entry-description-div">
				<textarea name="description" rows="12" class="form-control" placeholder="Optional: Extra Information" >{{ $record->description }}</textarea>
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