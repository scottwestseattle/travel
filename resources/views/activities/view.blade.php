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
	
		@component('menu-submenu-activities', ['record_id' => $record->id, 'record_title' => $record->title])
		@endcomponent
		
		@if (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id)
		<div class="publish-pills">
			<ul class="nav nav-pills">
				@if ($record->published_flag === 0)
					<li class="active"><a href="/activities/publish/{{$record->id}}">Private</a></li>
				@elseif ($record->approved_flag === 0)
					<li class="active"><a href="/activities/publish/{{$record->id}}">Pending Approval</a></li>
				@endif
				@if (!isset($record->location))
					<li class="active"><a href="/activities/location/{{$record->id}}">No Location</a></a>
				@endif
			</ul>
		</div>
		@endif
				
	@endguest
		
	<!----------------------->
	<!-- show bread crumbs -->
	<!----------------------->
	<div style="margin-top:10px;" class="form-group">
		@foreach($locations as $location)
			@if ($location['breadcrumb_flag'] == 1)
				<span name="" class=""><a href="\locations\activities\{{$location['id']}}">{{$location['name']}}</a>&nbsp;>&nbsp;</span>
			@endif
		@endforeach
	</div>		
		
	<div class="form-group">
		<h1 name="title" class="">{{$record->title }}
			@if (false)
				{{' (' . $main_photo->filename . ')'}}
			@endif
		</h1>
	</div>
	
	<?php 
		//
		// show main photo
		//
		$photo_found = false;
		$width = 800;
		$base_folder = 'img/tours/';
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

	@if ($main_photo !== null)
	<div style="display:default; margin-top:20px;">
		<?php 
			$title = $main_photo->alt_text;
			if (strlen($main_photo->location) > 0)
				$title .= ', ' . $main_photo->location;
		?>
		<img src="/img/tours/{{$record->id}}/{{$main_photo->filename}}" title="{{$title}}" style="max-width:100%; width:{{ $width }}" />
	</div>	
	@endif
	
	@if (strlen(trim($record->highlights)) > 0)
	<div class="entry" style="margin-bottom:20px;">
		<h3>@LANG('ui.Highlights')</h3>
		<div>{{$record->highlights}}</div>
	</div>
	@endif
	
	<div style="clear:both;">
		<div class="row">
			@if (strlen($record->distance) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.DISTANCE')</h3>
							<p>{{$record->distance}}</p>
						</div>
					</div>
			@endif

			@if (strlen($record->difficulty) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.DIFFICULTY')</h3>
							<p>{{$record->difficulty}}</p>
						</div>
					</div>
			@endif

			@if (strlen($record->trail_type) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.TRAIL TYPE')</h3>
							<p>{{$record->trail_type}}</p>
						</div>
					</div>
			@endif

			@if (strlen($record->season) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3></span>@LANG('ui.SEASON')</h3>
							<p>{{$record->season}}</p>
						</div>
					</div>
			@endif

			@if (strlen($record->elevation) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.ELEVATION')</h3>
							<p>{{$record->elevation}}</p>
						</div>
					</div>
			@endif
					
		</div><!-- row -->	
	</div>
					
	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! $record->description !!}</span>	
		</div>
	</div>

	<div class="amenities">

	@if (!empty(trim($record->info_link)))
		<div class="entry-div">
			<div class="entry amenity-item">
				<!-- h3>MORE INFORMATION</h3 -->
				<div id="" style="display:default; margin-top:20px;">				
					<a href="{{ $record->info_link }}">Please click here for more information</a>
				</div>
			</div>
		</div>
	@endif			
	
	@if (!empty(trim($record->map_link)))
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.MAP')</h3>
				<div id="" style="display:default; margin-top:20px; margin-bottom:10px;">				
					<iframe id="xttd-map" src="{{ $record->map_link }}" style="max-width:100%;" width="{{ $mapWidth }}" height="{{ floor($mapWidth * .75) }}"></iframe>
				</div>
				
				<p><a target="_blank" href="{{$record->map_link}}">Open in Google Maps to Navigate</a></p>
				
			</div>
		</div>
	@endif	

	@if (false && strlen($record->map_link) > 0)
		<div class="col-md-4 col-sm-6">
			<div class="amenity-item">
				<h3>@LANG('ui.LOCATION')</h3>
				<p><a target="_blank" href="{{$record->map_link}}">Show Map</a></p>
			</div>
		</div>
	@endif	
	
	@if (strlen($record->parking) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.PARKING')</h3>
				<span name="parking" class="">{{$record->parking}}</span>	
			</div>
		</div>
	@endif

	@if (strlen(trim($record->cost)) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.COST / ENTRY FEE')</h3>
				<span name="cost" class="">{{$record->cost}}</span>	
			</div>
		</div>
	@endif
	
	@if (strlen(trim($record->facilities)) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.FACILITIES')</h3>
				<span name="facilities" class="">{{$record->facilities}}</span>	
			</div>
		</div>
	@endif
			
	@if (strlen(trim($record->public_transportation)) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.PUBLIC TRANSPORTATION')</h3>
				<span name="facilities" class="">{{$record->public_transportation}}</span>	
			</div>
		</div>
	@endif

	@if (strlen(trim($record->wildlife)) > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.WILDLIFE')</h3>
				<span name="facilities" class="">{{$record->wildlife}}</span>	
			</div>
		</div>
	@endif

	@if ($regular_photos > 0)
		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.PHOTOS')</h3>
			</div>
		</div>
	@endif
	
	</div><!-- class="amenities" -->
	
	@if ($regular_photos > 0)
			
	<div class="text-center" style="display:default; margin-top:5px;">	
		@foreach($photos as $photo)		
			@if ($photo->main_flag !== 1)
				<span class="{{SHOW_XS_ONLY}}"><!-- xs only -->
					<img class="popupPhotos" style="width:100%; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/tours/{{$record->id}}/{{$photo->filename}}" />
				</span>
				<span class="{{SHOW_NON_XS}}" ><!-- all other sizes -->
					<span style="cursor:pointer;" onclick="popup({{$record->id}}, '{{$photo->filename}}')"><img style="height:250px; max-width:100%; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/tours/{{$record->id}}/{{$photo->filename}}" /></span>
				</span>									
			@endif
		@endforeach	
	</div>

	@endif
	
	<div class="amenities">
	
	<div class="entry-div">
		<div class="entry amenity-item">
			<h3>@LANG('ui.PARTNERS')</h3>
		</div>
	</div>	
	
@if (true)
	<div style="display:default; float:left; margin:5px 10px 5px 0px">	
		
		<div id="adgshp2008177892"></div>
		<script type="text/javascript" src="//cdn0.agoda.net/images/sherpa/js/sherpa_init1_08.min.js"></script><script type="text/javascript">
		var stg = new Object(); stg.crt="9181526501892";stg.version="1.04"; stg.id=stg.name="adgshp2008177892"; stg.width="300px"; stg.height="250px";stg.ReferenceKey="0xsHqxj9SidZIQaBKVV3aA=="; stg.Layout="OblongStatic"; stg.Language="en-us";stg.Cid="1806200"; stg.OverideConf=false; new AgdSherpa(stg,3).initialize();
		</script>
			
	</div>	
	
	<div style="display:default; float:left; margin:5px 10px 5px 0px">	
	
		<ins class="bookingaff" data-aid="1535322" data-target_aid="1535306" data-prod="banner" data-width="300" data-height="250" data-lang="en-US">
			<!-- Anything inside will go away once widget is loaded. -->
			<a href="//www.booking.com?aid=1535306">Booking.com</a>
		</ins>
		<script type="text/javascript">
			(function(d, sc, u) {
			  var s = d.createElement(sc), p = d.getElementsByTagName(sc)[0];
			  s.type = 'text/javascript';
			  s.async = true;
			  s.src = u + '?v=' + (+new Date());
			  p.parentNode.insertBefore(s,p);
			  })(document, 'script', '//aff.bstatic.com/static/affiliate_base/js/flexiproduct.js');
		</script>
	
	</div>
@endif

	</div><!-- class="amenities" -->

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
	popupImg.src = "/img/tours/" + id + "/" + filename;
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
