@extends('layouts.app')

@section('content')

<div class="container">

	@component('menu-submenu-sites')
	@endcomponent	

	<h1>Add Site</h1>
               
	<form method="POST" action="/{{$prefix}}/create">
		<div class="form-control-big">	
							
			<label for="site_url">Site URL (ex: domainname.com):</label>
			<input type="text" name="site_url" class="form-control" />
			
			<label for="site_name">Site Name (ex: My Web Site):</label>
			<input type="text" name="site_name" class="form-control" />

			<label for="site_name">Site Title (Title shown in browser tab hover):</label>
			<input type="text" name="site_title" class="form-control" />

			<label for="main_section_text">Main Section Text:</label>
			<textarea name="main_section_text" class="form-control"></textarea>

			<label for="main_section_subtext">Main Section Sub-Text:</label>
			<textarea name="main_section_subtext" class="form-control"></textarea>
			
			<!-- label for="instagram_link">Instagram Link:</label>
			<input type="text" name="instagram_link" class="form-control" / -->
			
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
