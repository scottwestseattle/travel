@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('photos.menu-submenu', ['record_id' => $parent_id])
	@endcomponent	

	<h1>Add {{$type}} Photo</h1>
               			   
	<form method="POST" action="/photos/create" enctype="multipart/form-data">
		<div class="form-control-big">	

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="file" name="image" id="image" class="" />
			</div>

			<input type="hidden" name="type_flag" value={{$type_flag}} />
			
			@if ($parent_id === 0)
				<div style="clear:both; margin:20px 0; font-size:20px;" class="">
					Slider size = 1920 x 934
				</div>
			@endif

			<input type="hidden" name="parent_id" value={{$parent_id}} />

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="filename" class="form-control" placeholder="Optional: new photo name"/>
			</div>			

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="alt_text" class="form-control" placeholder="Optional: alt text"/>
			</div>			

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="location" class="form-control" placeholder="Required: location"/>
			</div>	

			@if ($parent_id !== 0)
				<div style="clear: both;" class="">
					<input type="checkbox" name="main_flag" id="main_flag" class="" />
					<label for="main_flag" class="checkbox-big-label">Main Photo</label>
				</div>			
			@endif
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Upload</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>
	
	@if (isset($photos))
	<h3>Photos</h3>
	<div>
	@foreach ($photos as $photo)
		<img width="200" src="{{$path}}/{{$photo->filename}}" title="{{$photo->alt_text}}" />
	@endforeach
	</div>
	@endif
	
</div>
@endsection
