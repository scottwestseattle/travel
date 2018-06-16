@extends('layouts.app')

@section('content')

<?php 
$main_photo = null;
$regular_photos = 0;
foreach($photos as $photo)
{
	if ($photo->main_flag === 1)
		$main_photo = $photo;
	else
		$regular_photos++;
}
?>

<div class="page-size container">
               
	@guest
	@else
	
		@component('menu-submenu-entries', ['record_id' => $record->id, 'record_permalink' => $record->permalink])@endcomponent
		
		@if (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id)
		<div class="publish-pills">
			<ul class="nav nav-pills">
				@if ($record->published_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Private</a></li>
				@elseif ($record->approved_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Pending Approval</a></li>
				@endif
			</ul>
		</div>
		@endif
				
	@endguest
		
	<div class="form-group">
	
		<!------------------------------------>
		<!-- Top Navigation Buttons -->
		<!------------------------------------>
		
		<div style="margin-top: 10px;">
			@if (isset($prev))
				<a href="/entries/show/{{$prev->id}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>Prev</button></button></a>
			@endif
			@if (isset($record->parent_id))
				<a href="/blogs/show/{{$record->parent_id}}"><button type="button" class="btn btn-blog-nav">Back to Blog<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@endif
			@if (isset($next))
				<a href="/entries/show/{{$next->id}}"><button type="button" class="btn btn-blog-nav">Next<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
			@endif			
			</ul>		
		</div>
		
		<h1 name="title" class="">{{$record->title }}</h1>
		@if (isset($record->display_date))
			<p style="font-weight: bold;"><?php $date = date_create($record->display_date); echo date_format($date, "l, F d, Y"); ?></p>
		@endif
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
			
		//dd(getcwd());
					
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

	@if ($main_photo !== null)
	<div style="display:default; margin-top:20px;">
		<?php 
			$title = $main_photo->alt_text;
			if (strlen($main_photo->location) > 0)
				$title .= ', ' . $main_photo->location;
		?>
		<img src="/img/entries/{{$record->id}}/{{$main_photo->filename}}" title="{{$title}}" style="max-width:100%; width:{{ $width }}" />
	</div>	
	@endif
	
	@if (strlen(trim($record->highlights)) > 0)
	<div class="entry" style="margin-bottom:20px;">
		<h3>Highlights</h3>
		<div>{{$record->highlights}}</div>
	</div>
	@endif

	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>				
		</div>
	</div>

	@if ($regular_photos > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>PHOTOS</h3>
			</div>
		</div>
	@endif
	
	@if ($regular_photos > 0)
			
	<div class="text-center" style="display:default; margin-top:5px;">	
		@foreach($photos as $photo)		
			@if ($photo->main_flag !== 1)
				<?php 
					$title = $photo->filename;  // just in case the others are empty
					
					if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
						$title = $photo->alt_text;
					
					if (isset($photo->location) && strlen($photo->location) > 0)
						$title .= ', ' . $photo->location;
				?>

				<span class="{{SHOW_XS_ONLY}}"><!-- xs only -->
					<img style="width:100%; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
				</span>
				<span class="{{SHOW_NON_XS}}" ><!-- all other sizes -->
					<span style="cursor:pointer;" onclick="popup({{$record->id}}, '{{$photo->filename}}', '{{$title}}')">
						<img class="popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
					</span>
				</span>									
			@endif
		@endforeach	
	</div>

	@endif

		<!------------------------------------>
		<!-- Bottom Navigation Buttons -->
		<!------------------------------------>
	
		<div class="trim-text " style="max-width:100%; margin-top: 30px;">
			@if (isset($prev))
				<div class="" style="float:left; margin: 0 5px 5px 0;" >
					<a href="/entries/show/{{$prev->id}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-left"></span>{{$prev->title}}</button></a>
				</div>
			@endif
			@if (isset($next))
				<div style="float:left;">
				<span class="{{SHOW_NON_XS}}">
					<a href="/entries/show/{{$next->id}}"><button type="button" class="btn btn-nav-bottom">{{$next->title}}<span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span></button></a>
				</span>
				<span class="{{SHOW_XS_ONLY}}">
					<a href="/entries/show/{{$next->id}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span>{{$next->title}}</button></a>
				</span>
				</div>
			@endif			
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
