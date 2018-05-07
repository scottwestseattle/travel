@extends('layouts.app')

@section('content')

<div class="page-size container">

	<h1>Add Photos</h1>
               			   
	<form method="POST" action="/photos/create/{{$id}}" enctype="multipart/form-data">
		<div class="form-control-big">	

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="file" name="image" id="image" class="" />
			</div>

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="name" class="form-control" placeholder="Photo Name (optional: uploaded file name)"/>
			</div>			

			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Upload</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>
@endsection
