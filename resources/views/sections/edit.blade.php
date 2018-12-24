@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('sections.menu-submenu', ['record' => $record])@endcomponent	

<div style="display:{{App::getLocale() == 'en' ? 'default' : 'none' }}">
	<h1>Edit Section</h1>

	<form method="POST" action="/sections/update/{{$record->id}}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="type_flag" value="{{$record->type_flag}}" />
			<input type="hidden" name="site_id" value="{{$record->site_id}}">
			<input type="hidden" name="approved_flag" value="1">
			<input type="hidden" name="referer" value="{{$referer}}">

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
			
			<div class="entry-description-div">
				<textarea name="description_short" rows="6" class="form-control"  placeholder="Optional: More Extra Information" >{{$record->description_short}}</textarea>	
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

<div style="display:{{App::getLocale() != 'en' ? 'default' : 'none' }}">
	<h1>Edit Section for locale: {{App::getLocale()}}</h1>
	
	<form method="POST" action="/sections/updatetrx/{{$record->id}}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="type_flag" value="{{$record->type_flag}}" />
			<input type="hidden" name="site_id" value="{{$record->site_id}}">
			<input type="hidden" name="approved_flag" value="1">
			<input type="hidden" name="referer" value="{{$referer}}">
			<input type="hidden" name="language" value="{{App::getLocale()}}">

			<input type="text" id="medium_col1" name="medium_col1" class="form-control" value="{{ $record->medium_col1 }}"  placeholder="Translated Title" />
		
			<div class="entry-title-div">
				<input type="text" id="medium_col2" name="medium_col2" value="{{$record->medium_col2}}" placeholder="Permalink: section-name" class="form-control" />
			</div>
			
			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			<div class="entry-description-div">
				<textarea name="large_col1" rows="12" class="form-control" placeholder="Optional: Extra Information" >{{ $record->large_col1 }}</textarea>
			</div>
			
			<div class="entry-description-div">
				<textarea name="large_col2" rows="6" class="form-control"  placeholder="Optional: More Extra Information" >{{$record->large_col2}}</textarea>	
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