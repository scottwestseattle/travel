@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<form method="POST" action="/activities/view/{{ $record->id }}">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/activities/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
		</tr></table>
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
		$base_folder = 'img/tours/';
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
	?>

	<div style="display:default; margin-top:20px;">
		@foreach($photos as $photo)
			@if ($photo->main_flag === 1)
				<img src="/img/tours/{{$record->id}}/{{$photo->filename}}" title="{{$photo->alt_text}}" style="max-width:100%; width:{{ $width }}" />
			@endif
		@endforeach	
	</div>	
	
	<div class="entry" style="margin-bottom:20px;">
		<h3>Highlights</h3>
		<div>{{$record->highlights}}</div>
	</div>
	
	<div style="clear:both;">
		<div class="row">
				
					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3>Distance:</h3>
							{{$record->distance}}
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3>Difficulty:</h3>
							{{$record->difficulty}}
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3>Parking:</h3>
							{{$record->parking}}
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3></span>Season:</h3>
							{{$record->season}}
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3>Location:</h3>
							<a target="_blank" href="{{$record->map_link}}">Google Maps</a>
						</div>
					</div>

					<div class="col-md-4 col-sm-6">
						<div class="center steps">
							<h3>Elevation Change</h3>
							{{$record->elevation_change}}
						</div>
					</div>
					
				</div><!-- row -->	
	</div>
					
	<div class="entry-div" style="margin-top:20px;width:100%;">
		<div class="entry" style="width:100%;">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>	
		</div>
	</div>
		
	<div class="amenities">
	
	@if (!empty(trim($record->map_link)))
		<div class="entry-div">
			<div class="entry">
				<h3>Trail Map</h3>
				<div id="" style="display:default; margin-top:20px;">				
					<iframe id="xttd-map" src="{{ $record->map_link }}" style="max-width:100%;" width="{{ $width }}" height="{{ floor($width * .75) }}"></iframe>
				</div>
			</div>
		</div>
	@endif		
	
	@if (strlen(trim($record->entry_fee)) > 0)
		<div class="entry-div">
			<div class="entry">
				<h3>Entry Fee</h3>
				<span name="entry_fee" class="">{{$record->entry_fee}}</span>	
			</div>
		</div>
	@endif
	
	@if (strlen(trim($record->facilities)) > 0)
		<div class="entry-div">
			<div class="entry">
				<h3>Facilities</h3>
				<span name="facilities" class="">{{$record->facilities}}</span>	
			</div>
		</div>
	@endif
			
	@if (strlen(trim($record->public_transportation)) > 0)
		<div class="entry-div">
			<div class="entry">
				<h3>Public Transportation</h3>
				<span name="facilities" class="">{{$record->public_transportation}}</span>	
			</div>
		</div>
	@endif

	@if (strlen(trim($record->wildlife)) > 0)
		<div class="entry-div">
			<div class="entry">
				<h3>Wildlife</h3>
				<span name="facilities" class="">{{$record->wildlife}}</span>	
			</div>
		</div>
	@endif

	@if (count($photos) > 0)
		<div class="entry-div">
			<div class="entry">
				<h3>Photos</h3>
			</div>
		</div>
	@endif
	
	</div><!-- class="amenities" -->
	
	<div style="display:default; margin-top:5px;">	
		@foreach($photos as $photo)
			@if ($photo->main_flag !== 1)
				<img style="width:100%; max-width:400px; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/tours/{{$record->id}}/{{$photo->filename}}" />		
			@endif
		@endforeach	
	</div>
		
{{ csrf_field() }}

</form>

</div>
@endsection
