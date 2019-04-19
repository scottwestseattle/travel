@extends('layouts.app')

@section('content')

<?php 
$main_photo = null;
$regular_photos = 0;
if (isset($photos))
foreach($photos as $photo)
{
	if ($photo->main_flag === 1)
		$main_photo = $photo;
	else
		$regular_photos++;
}
?>

<div class="container" >          
	@guest
	@else
	
		@component('entries.menu-submenu', ['record' => $record])@endcomponent
		
		@if (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id)
		<div class="publish-pills">
			<ul class="nav nav-pills">
				@if ($record->published_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Private</a></li>
				@elseif ($record->approved_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Pending Approval</a></li>
				@endif
				<li>View Count: {{$record->view_count}}</li>
			</ul>
		</div>
		@endif
				
	@endguest
</div>
	
<div class="" style="margin-bottom: 10px;">

	<div class="text-center" style="">
		<a href="/galleries">
			<button type="button" class="btn btn-blog-nav">@LANG('content.Back to Galleries')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button>
		</a>			
	</div>
			
	<?php 
		//
		// show main photo
		//
		$photo_found = false;
		$width = 800;
		$base_folder = 'img/entries/';
		$photo_folder = $base_folder . $record->id . '/';
		$photo = $photo_folder . 'main.jpg';
								
		if (file_exists($photo) === FALSE)
		{
			$photo = '/img/theme1/placeholder.jpg';
			$width = 300;
		}
		else
		{
			$photo = '/' . $photo;
			$photo_found = true;
		}
		
		// map size
		$mapWidth = 500;
	?>
	
	<div class="text-center" style="display:default; margin-top:5px;">	
		<h1 name="title" class="">{{$record->title}} ({{count($photos)}})</h1>
		<!--
		<p><a href="/photos/slideshow/{{$record->id}}">Slide Show</a></p>
		-->
		@foreach($photos as $photo)		
			<?php 
				$title = $photo->filename;  // just in case the others are empty
				
				if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
					$title = $photo->alt_text;
				
				if (isset($photo->location) && strlen($photo->location) > 0)
					$title .= ', ' . $photo->location;
			?>
			
			<span style="">
				<a href="/photos/{{$photo->permalink}}/{{$photo->id}}">
				<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="{{$photo_path}}/tn/{{$photo->filename}}" />
				<img class="{{SHOW_NON_XS}} popupPhotos" style="height:180px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="{{$photo_path}}/tn/{{$photo->filename}}" />
				</a>
			</span>
				
		@endforeach	
	</div>
	
	<div class="text-center" style="margin-top: 10px;">
		<a href="/galleries">
			<button type="button" class="btn btn-blog-nav">@LANG('content.Back to Galleries')<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button>
		</a>			
	</div>

@if (count($photos))
<!-- photo view popup -->
<div id="myModal" onclick="nextPhoto(false)" class="modal-popup text-center">
	<div  style="cursor:pointer;" class="modal-content">
		<span onclick="popdown()" id="modalSpan" class="close-popup">&times;</span>
		<img id="popupImg" style="max-width:900px;" width="100%" src="" />
		<div id="popupImgTitle"></div>
	</div>
</div>

<script>

// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementById("modalSpan");

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	alert(2);
    modal.style.display = "none";
}

</script>		
@endif

@endsection
