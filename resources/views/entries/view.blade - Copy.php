@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<form method="POST" action="/entries/view/{{ $record->id }}">

	@component('menu-submenu-entries', ['record_id' => $record->id, 'record_title' => $record->title])
	@endcomponent
		
	<div class="form-group">
		<h1 name="title" class="">{{$record->title }}</h1>
	</div>

	@if (isset($tags))
	<div class="form-group">
	@foreach($tags as $tag)
		<span name="" class=""><a href="\tags\entries\{{$tag->id}}">{{$tag->name}}</a>&nbsp;>&nbsp;</span>		
	@endforeach
	</div>
	@endif

	@if (strlen($record->description_short) > 0)
	<h3 class="">Highlights:</h3>
	<div class="entry-div entry">
		{!! nl2br($record->description_short) !!}
	</div>
	<div style="clear:both;margin:75px;"></div>
	@endif
	
	<div class="entry-div">
		<div class="entry">
			<span name="description" class="">{!! nl2br($record->description) !!}</span>	
		</div>
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

	@if (isset($photos))
	<div style="display:default; margin-top:20px;">
		@foreach($photos as $photo)
			@if ($photo->main_flag === 1)
				<img src="/img/tours/{{$record->id}}/{{$photo->filename}}" title="{{$photo->alt_text}}" style="max-width:100%; width:{{ $width }}" />
			@endif
		@endforeach	
	</div>
	
	<div style="display:default; margin-top:5px;">
		@foreach($photos as $photo)
			@if ($photo->main_flag !== 1)
				<img style="width:100%; max-width:400px; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/tours/{{$record->id}}/{{$photo->filename}}" />		
			@endif
		@endforeach	
	</div>
	@endif
	
{{ csrf_field() }}

</form>

</div>
@endsection
