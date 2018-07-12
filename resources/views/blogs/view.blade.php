@extends('layouts.app')

@section('content')

<?php 
$main_photo = null;
$regular_photos = 0;
if (isset($photos))
{
foreach($photos as $photo)
{
	if ($photo->main_flag === 1)
		$main_photo = $photo;
	else
		$regular_photos++;
}
}
else
	$photos = null;
?>

<div class="page-size container">
               
	@guest
	@else
	
		@component('menu-submenu-entries', ['record_id' => $record->id, 'record_permalink' => $record->permalink])@endcomponent
		
		@if (Auth::user() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
			@if ($record->published_flag === 0)
				<div><a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Private</button></a></div>
			@elseif ($record->approved_flag === 0)
				<div><a href="/entries/publish/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">Pending Approval</button></a></div>
			@endif
		@endif
				
	@endguest
		
	<div class="form-group">
		<h1 name="title" class="">{{$record->title }}</h1>
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
		
		if ($record->type_flag == ENTRY_TYPE_BLOG)
		{
			//$width = '500px';
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
		<img src="/img/entries/{{$record->id}}/{{$main_photo->filename}}" title="{{$title}}" class="popupPhotos" style="max-width:100%; width:{{ $width }}" />
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
			
				<span style="cursor:pointer;" onclick="popup({{$record->id}}, '{{$photo->filename}}', {{$photo->id}})">
					<img class="{{SHOW_XS_ONLY}}" id="{{$photo->id}}" style="width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
					<img class="{{SHOW_NON_XS}} popupPhotos" style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$title}}" src="/img/entries/{{$record->id}}/{{$photo->filename}}" />
				</span>

				
			@endif
		@endforeach	
	</div>

	@endif
	
	<div style="margin:20px 0">
		
		<h3>
			<span class="middle" style="margin: 0 10px 0 0;">Blog Posts ({{ count($records) }})</span>
			@if (Auth::user() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
				<a href="/blogs/addpost/{{$record->id}}"><button type="button" class="btn btn-action">Add New Post</button></a>	
			@endif
		</h3>
	
		<!---------------------------------->
		<!-- The Blog Entry list          -->
		<!---------------------------------->
		<table id="blogEntryTable" class="table table-striped">
			<tbody>
			<?php $count = 0; ?>
			@foreach($records as $record)
				<tr style="display:{{$count++ < 10 ? 'default' : 'none'}};">
					<td>
						<a href="{{ route('entry.permalink', [$record->permalink]) }}">{{$record->title}} ({{$record->display_date}})</a>
						
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						<?php endif; ?>
						
						@if (!isset($record->permalink) || strlen($record->permalink) === 0)
							<div><a href="/entries/edit/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">No Permalink</button></a></div>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>

		<!-- the Show All button -->
		<span id="showAllButton" style="cursor:pointer;" onclick="showAllRows('blogEntryTable', 'showAllButton')">
			<button style="margin-bottom:10px;" type="button" class="btn btn-blog-nav">Show All Posts&nbsp;
				<span style="background-color: white; color: #5CB85C;" class="badge">{{count($records)}}</span>
			</button>
		</span>
		
	</div>
	
<!-- photo view popup -->
<div id="myModal" onclick="nextPhoto(false)" class="modal-popup text-center">

	<div  style="cursor:pointer;" class="modal-content">
		<span onclick="popdown()" id="modalSpan" class="close-popup">&times;</span>
		<img id="popupImg" style="max-width:900px;" width="100%" src="" />
	</div>

</div>

<script>

// Get the modal
var modal = document.getElementById('myModal');

// Get the <span> element that closes the modal
var span = document.getElementById("modalSpan");

function popup(id, filename)
{
	//alert(filename);
	
	var popupDiv = document.getElementById("myModal");
	popupDiv.style.display = "block";
	
	var popupImg = document.getElementById("popupImg");
	popupImg.src = "/img/entries/" + id + "/" + filename;
}

function nextPhoto(found)
{	
	var popupImg = null;
	var photos = document.getElementsByClassName("popupPhotos");
	var popupImg = document.getElementById("popupImg");
	for(var i = 0; i < photos.length; i++)
	{
		if (found)
		{
			popupImg.src = photos.item(i).src;
			return;
		}

		// if it's the current photo and then set the found flag to stop at the 
		// next photo at the top of the next iterartion
		var count = i + 1; // if it's the last item don't consider it found so we can wrap to the first item
		if (count < photos.length && popupImg.src == photos.item(i).src)
		{
			found = true;
		}
	}	
	
	if (!found)
	{
		// show the first photo
		nextPhoto(true);
	}
}

function popdown()
{	
	var popupDiv = document.getElementById("myModal");
	popupDiv.style.display = "none";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	alert(2);
    modal.style.display = "none";
}

</script>		

@endsection
