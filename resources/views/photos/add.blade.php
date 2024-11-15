@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('photos.menu-submenu', ['record_id' => $parent_id])
	@endcomponent	

	@if ($type_flag == PHOTO_TYPE_RECEIPT)
		<h1>Upload Receipt Photo</h1>
	@elseif ($type_flag == PHOTO_TYPE_SLIDER)
		<h1>Upload Slider Photo</h1>
	@else
		<h1>Upload Photo to Gallery</h1>
	@endif
               			   
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

			@if ($type_flag != PHOTO_TYPE_RECEIPT)

				<div style="clear:both; margin:20px 0; font-size:20px;" class="">
					<input type="text" name="alt_text" id="alt_text" class="form-control" placeholder="Optional: alt text"/>
				</div>			

				<div style="clear:both; margin:20px 0; font-size:20px;" class="">
					<input type="text" name="location" id="location" class="form-control" value="{{$location}}" placeholder="Required: location"/>
				</div>	

				<div style="clear:both; margin:20px 0; font-size:20px;" class="">
					<a href='#' onclick="javascript:createPhotoName('alt_text', 'location', 'filename')";>
						<span id="" class="glyphCustom glyphicon glyphicon-link" style="font-size:1.3em; margin-left:5px;"></span>
					</a>						

					<button style="height:25px; padding:3px; margin: 0 0 8px 10px; font-size:12px;" type="submit" name="update" class="btn btn-primary">Upload</button>
					
					<input type="text" name="filename" id="filename" class="form-control" placeholder="Optional: new photo name"/>
				</div>			

				@if ($parent_id !== 0)
					<div style="clear: both;" class="">
						<input type="checkbox" name="main_flag" id="main_flag" class="" />
						<label for="main_flag" class="checkbox-big-label">Main Photo</label>
					</div>			
					<div>
						<input type="checkbox" name="gallery_flag" id="gallery_flag" class="" checked />
						<label for="gallery_flag" class="checkbox-big-label">Show in Gallery</label>
					</div>			
				@endif
			
			@endif
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Upload</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>
	
	@if ($type_flag != PHOTO_TYPE_RECEIPT && isset($photos))
		<h3>Photos</h3>
		<div>
		@foreach ($photos as $photo)
			<img width="200" src="{{$path}}/{{$photo->filename}}" title="{{$photo->alt_text}}" />
		@endforeach
		</div>
	@endif
	
</div>
@endsection
