@extends('layouts.app')

@section('content')

<div class="container page-size">

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
						
			<div style="margin:20px 0;">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
						
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
