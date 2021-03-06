@extends('layouts.app')

@section('content')

<?php $gallery = isset($gallery) ? $gallery : null; ?>

<div class="page-size container">
               
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
			</ul>
		</div>
		@endif
				
	@endguest
		
	<div class="form-group">
	
		<!------------------------------------>
		<!-- Top Navigation Buttons -->
		<!------------------------------------>
		
		@if (isset($prev) || isset($record->parent_id) || isset($next))
		<div style="margin-top: 10px;">
			@if (isset($prev))
				<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-blog-nav"><span style="margin-right:5px;" class="glyphicon glyphicon-circle-arrow-left"></span>Prev</button></button></a>
			@endif
			@if ($record->type_flag == ENTRY_TYPE_BLOG_ENTRY)
				<a href="/blogs/show/{{$record->parent_id}}"><button type="button" class="btn btn-blog-nav">Back to Blog<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@elseif($record->type_flag == ENTRY_TYPE_ARTICLE)
				<a href="/articles"><button type="button" class="btn btn-blog-nav">Back to Articles<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span></button></a>
			@endif
			@if (isset($next))
				<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-blog-nav">Next<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-right"></span></button></a>
			@endif	

		</div>
		@elseif (isset($backLink) && isset($backLinkText) && !((Auth::user() && (Auth::user()->user_type >= 1000))))
		<div style="margin-top: 10px;">
			<a href="{{$backLink}}">
				<button type="button" class="btn btn-blog-nav">{{$backLinkText}}
					<span style="margin-left:5px;" class="glyphicon glyphicon-circle-arrow-up"></span>
				</button>
			</a>			
		</div>
		@endif
		
		<h1 name="title" class="">{{$record->title }}</h1>
		<strong>

		@if (isset($record->location) && is_string($record->location))
			@if ($record->location_type != LOCATION_TYPE_COUNTRY)
				{{$record->location}}, {{$record->location_parent}}
			@else
				{{$record->location}}
			@endif
		@endif
		
		@if (isset($record->display_date))
			<p><?php $date = date_create($record->display_date); echo date_format($date, "l, F d, Y"); ?></p>
		@endif
		</strong>
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

	@if ($record->photo_gallery !== null)
	<div style="display:default; margin-top:20px;">
		<img src="{{$record->photo_gallery_path}}/{{$record->photo_gallery}}" title="{{$record->photo_gallery_title}}" class="popupPhotos" style="max-width:100%; width:{{ $width }}" />
	</div>		
	@elseif ($record->photo !== null)
	<div style="display:default; margin-top:20px;">
		<img src="{{$record->photo_path}}/{{$record->photo}}" title="{{$record->photo_title}}" class="popupPhotos" style="max-width:100%; width:{{ $width }}" />
	</div>	
	@endif
	
	@if (strlen(trim($record->description_short)) > 0)
	<div class="entry" style="margin-bottom:20px;">
		<h3>More Extra Info</h3>
		<div>{{$record->description_short}}</div>
	</div>
	@endif

	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! $record->description !!}</span>		
		</div>
	</div>

	@if ($record->photo_count > 0 || count($record->photo_gallery_count) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>PHOTOS <!-- a style="font-size:.65em;" href="/photos/slideshow/{{$record->id}}">(Slideshow)</a --></h3>
				
			</div>
		</div>
			
	<div class="text-center" style="display:default; margin-top:5px;">
		@if (isset($photos))
		@foreach($photos as $photo)		
			@if ($photo->main_flag !== 1)
				<?php 
					$title = $photo->filename;  // just in case the others are empty
					
					if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
						$title = $photo->alt_text;
					
					if (isset($photo->location) && strlen($photo->location) > 0)
						$title .= ', ' . $photo->location;
				?>
				
				<span style="cursor:pointer;" onclick="popup({{$record->id}}, '{{$photo->filename}}', {{$photo->id}})">
					<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
					<img class="{{SHOW_NON_XS}} popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
				</span>
			@endif
		@endforeach	
		@endif
		
		@if (isset($gallery))
		@foreach($gallery as $photo)
			<?php 
				$title = $photo->filename;  // just in case the others are empty
				
				if (isset($photo->alt_text) && strlen($photo->alt_text) > 0)
					$title = $photo->alt_text;
				
				if (isset($photo->location) && strlen($photo->location) > 0)
					$title .= ', ' . $photo->location;
			?>
		
			@if ($record->photo_id != $photo->id)
			<span style="cursor:pointer;" onclick="popup({{$photo->parent_id}}, '{{$photo->filename}}', {{$photo->id}})">
				<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" />
				<img class="{{SHOW_NON_XS}} popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" />
			</span>
			@endif
		@endforeach
		@endif
	</div>

	@endif

		<!------------------------------------>
		<!-- Bottom Navigation Buttons -->
		<!------------------------------------>
	
		<div class="trim-text " style="max-width:100%; margin-top: 30px;">
			@if (isset($prev))
				<div class="" style="float:left; margin: 0 5px 5px 0;" >
					<a href="/entries/{{$prev->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-left"></span>{{$prev->title}}</button></a>
				</div>
			@endif
			@if (isset($next))
				<div style="float:left;">
				<span class="{{SHOW_NON_XS}}">
					<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-nav-bottom">{{$next->title}}<span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span></button></a>
				</span>
				<span class="{{SHOW_XS_ONLY}}">
					<a href="/entries/{{$next->permalink}}"><button type="button" class="btn btn-nav-bottom"><span class="glyph-nav-bottom glyphicon glyphicon-circle-arrow-right"></span>{{$next->title}}</button></a>
				</span>
				</div>
			@endif			
		</div>
	
@if (count($photos) > 0 || count($gallery) > 0)
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
