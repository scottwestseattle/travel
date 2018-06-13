@extends('layouts.app')

@section('content')

<div class="container page-size">

@component('menu-submenu-sites')@endcomponent

<h1>Edit</h1>

<form method="POST" action="/{{$prefix}}/update/{{$record->id}}">

	<div class="form-control-big">	

		<label for="site_url">Site URL (ex: domainname.com):</label>
		<input type="text" name="site_url" class="form-control" value="{{$record->site_url}}"></input>	
				
		<label for="site_name">Site Name (ex: My Web Site):</label>
		<input type="text" name="site_name" class="form-control" value="{{$record->site_name}}"></input>

		<label for="site_title">Site Title (Title shown in browser tab hover):</label>
		<input type="text" name="site_title" class="form-control" value="{{$record->site_title}}"></input>	

		<label for="main_section_text">Main Section Text:</label>
		<textarea name="main_section_text" class="form-control">{{$record->main_section_text}}</textarea>

		<label for="main_section_subtext">Main Section Sub-Text:</label>
		<textarea name="main_section_subtext" class="form-control">{{$record->main_section_subtext}}</textarea>	

		<label for="email">Email:</label>
		<input type="text" name="email" class="form-control" value="{{$record->email}}" />

		<label for="telephone">Telephone:</label>
		<input type="text" name="telephone" class="form-control" value="{{$record->telephone}}" />
		
		<label for="instagram_link">Instagram Link:</label>
		<input type="text" name="instagram_link" class="form-control" value="{{$record->instagram_link}}" />

		<label for="current_location_title">Current Location Title:</label>
		<input type="text" name="current_location_title" class="form-control" value="{{$record->current_location_title}}" />

		<label for="current_location">Current Location:</label>
		<input type="text" name="current_location" class="form-control" value="{{$record->current_location}}" />
		
		<label for="current_location_map_link">Current Location Map Link (without the iframe):</label>
		<input type="text" name="current_location_map_link" class="form-control" value="{{$record->current_location_map_link}}" />

		<label for="current_location_photo">Current Location Photo (if no map available):</label>
		<input type="text" name="current_location_photo" class="form-control" value="{{$record->current_location_photo}}" />

		<label for="previous_location_title">Previous Location Title:</label>
		<input type="text" name="previous_location_title" class="form-control" value="{{$record->previous_location_title}}" />
		
		<label for="previous_location_list">Previous Locations (separated by new line):</label>
		<textarea name="previous_location_list" class="form-control">{{$record->previous_location_list}}</textarea>	
		
	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
	
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
