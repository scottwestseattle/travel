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

	<div class="form-group">
		<button type="submit" name="update" class="btn btn-primary">Update</button>
	</div>
	
	</div>
{{ csrf_field() }}
</form>

</div>

@stop
