@extends('layouts.app')

@section('content')

<div class="page-size container">
               
	@guest
	@else
	
		@component('menu-submenu-tours', ['record_id' => $record->id, 'record_permalink' => $record->permalink])
		@endcomponent
		
		@if (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id)
		<div class="publish-pills">
			<ul class="nav nav-pills">
				@if ($record->published_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Private</a></li>
				@elseif ($record->approved_flag === 0)
					<li class="active"><a href="/entries/publish/{{$record->id}}">Pending Approval</a></li>
				@endif
				@if (!isset($record->location))
					<li class="active"><a href="/entries/setlocation/{{$record->id}}">No Location</a></a>
				@endif
			</ul>
		</div>
		@endif
				
	@endguest
		
	<!----------------------->
	<!-- show bread crumbs -->
	<!----------------------->
	<div style="margin-top:10px;" class="form-group">
		<span name="" class=""><a href="\tours\index\">Tours</a>&nbsp;>&nbsp;</span>	
		@foreach($locations as $location)
			@if ($location['breadcrumb_flag'] == 1)
				<span name="" class=""><a href="\tours\location\{{$location['id']}}">{{$location['name']}}</a>&nbsp;>&nbsp;</span>
			@endif
		@endforeach
	</div>		
		
	<div class="form-group">
		<h1 name="title" class="">{{$record->title }}</h1>
		@if (null !== Auth::user() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
			<p>View Count: {{$record->view_count}}</p>
		@endif
	</div>
	
	<?php 
		//
		// show main photo
		//
		$photo_found = false;
		$width = 800;
		$subfolder = 'entries';
		$base_folder = 'img/' . $subfolder . '/';
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
		$mapWidth = 400;
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
		<h3>@LANG('ui.Highlights')</h3>
		<div>{{$record->description_short}}</div>
	</div>
	@endif
	
	@if (isset($activity))
	<div style="clear:both;">
		<div class="row">
			@if (strlen($activity->distance) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.DISTANCE')</h3>
							<p>{{$activity->distance}}</p>
						</div>
					</div>
			@endif

			@if (strlen($activity->difficulty) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.DIFFICULTY')</h3>
							<p>{{$activity->difficulty}}</p>
						</div>
					</div>
			@endif

			@if (strlen($activity->trail_type) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.TRAIL TYPE')</h3>
							<p>{{$activity->trail_type}}</p>
						</div>
					</div>
			@endif

			@if (strlen($activity->season) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.SEASON')</h3>
							<p>{{$activity->season}}</p>
						</div>
					</div>
			@endif

			@if (strlen($activity->elevation) > 0)
					<div class="col-md-4 col-sm-6">
						<div class="amenity-item">
							<h3>@LANG('ui.ELEVATION')</h3>
							<p>{{$activity->elevation}}</p>
						</div>
					</div>
			@endif
					
		</div><!-- row -->	
	</div>
	@endif
					
	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! $record->description !!}</span>	
		</div>
	</div>

		
	<div class="amenities">
		
	@if (isset($activity))
			
		@if (!empty(trim($activity->info_link)))
			<div class="entry-div">
				<div class="entry amenity-item">
					<!-- h3>MORE INFORMATION</h3 -->
					<div id="" style="display:default; margin-top:20px;">				
						<a href="{{ $activity->info_link }}">Please click here for more information</a>
					</div>
				</div>
			</div>
		@endif			
		
		@if (!empty(trim($activity->map_link)))
			<div style="float:left;" class="entry-div">
				<div class="entry amenity-item">
					<h3>{{$activity->map_label}}</h3>
					<div id="" style="display:default; margin-top:20px; margin-bottom:10px;">				
						<iframe id="xttd-map" src="{{$activity->map_link}}" style="max-width:100%;" width="{{ $mapWidth }}" height="{{ floor($mapWidth * .75) }}"></iframe>
					</div>
					
					<p><a target="_blank" href="{{$activity->map_link}}">{{$activity->map_labelalt}}</a></p>
					
				</div>
			</div>
		@endif	

		@if (!empty(trim($activity->map_link2)))
			<div style="float:left;" class="xentry-div">
				<div class="entry amenity-item">
					<h3>{{$activity->map_label2}}</h3>
					<div id="" style="display:default; margin-top:20px; margin-bottom:10px;">				
						<iframe id="xttd-map" src="{{$activity->map_link2}}" style="max-width:100%;" width="{{ $mapWidth }}" height="{{ floor($mapWidth * .75) }}"></iframe>
					</div>
					
					<p><a target="_blank" href="{{$activity->map_link2}}">{{$activity->map_labelalt2}}</a></p>
					
				</div>
			</div>
		@endif	
		
		<div style="clear:both;"></div>

		
		@if (strlen($activity->parking) > 0)
			<div class="entry-div">
				<div class="entry amenity-item">
					<h3>@LANG('ui.PARKING')</h3>
					<span name="parking" class="">{{$activity->parking}}</span>	
				</div>
			</div>
		@endif

		@if (strlen(trim($activity->cost)) > 0)
			<div class="entry-div">
				<div class="entry amenity-item">
					<h3>@LANG('ui.COST / ENTRY FEE')</h3>
					<span name="cost" class="">{{$activity->cost}}</span>	
				</div>
			</div>
		@endif
		
		@if (strlen(trim($activity->facilities)) > 0)
			<div class="entry-div">
				<div class="entry amenity-item">
					<h3>@LANG('ui.FACILITIES')</h3>
					<span name="facilities" class="">{{$activity->facilities}}</span>	
				</div>
			</div>
		@endif
				
		@if (strlen(trim($activity->public_transportation)) > 0)
			<div class="entry-div">
				<div class="entry amenity-item">
					<h3>@LANG('ui.PUBLIC TRANSPORTATION')</h3>
					<span name="facilities" class="">{{$activity->public_transportation}}</span>	
				</div>
			</div>
		@endif

		@if (strlen(trim($activity->wildlife)) > 0)
			<div class="entry-div">
				<div class="entry amenity-item">
					<h3>@LANG('ui.WILDLIFE')</h3>
					<span name="facilities" class="">{{$activity->wildlife}}</span>	
				</div>
			</div>
		@endif

		<div class="entry-div">
			<div class="entry amenity-item">
				<h3>@LANG('ui.PHOTOS')</h3>
			</div>
		</div>
	
	@endif
	
	</div><!-- class="amenities" -->
			
	<!-------------------------------------------------->
	<!-- Show the Photos                              -->
	<!-------------------------------------------------->
	
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
	</div>
	
	<!-------------------------------------------------->
	<!-- Show the Affiliates                          -->
	<!-------------------------------------------------->
	
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

@endsection
