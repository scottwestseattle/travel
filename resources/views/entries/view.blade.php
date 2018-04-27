@extends('layouts.app')

@section('content')

<div class="container">
               
<form method="POST" action="/entries/gen/{{ $entry->id }}">

	@guest
	@else
		<a href='/entries/edit/{{$entry->id}}'>
			<span class="glyphCustom glyphicon glyphicon-edit"></span>
		</a>
	@endguest
		
	<div class="form-group">
		<h1 name="title" class="">{{$entry->title }}</h1>
	</div>
	
	<div class="entry-div">
	
	
		<div class="entry">
			<span name="description" class="">{!! nl2br($entry->description) !!}</span>	
		</div>
		

	</div>

	<?php 
		$photo_found = false;
		$width = 1000;
		$base_folder = 'img/theme1/tours/';
		$photo_folder = $base_folder . $entry->id . '/';
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

	<?php if ($photo_found) : ?>
		<div style="display:default; margin-top:20px;">
			<img src="{{ $photo }}" style="max-width:100%; width:{{ $width }}" />
		</div>
	<?php endif; ?>

	<?php if (!empty($entry->map_link)) : ?>
		<div id="xttd-map" style="display:default; margin-top:20px;">				
			<iframe id="xttd-map" src="{{ $entry->map_link }}" style="max-width:100%;" width="{{ $width }}" height="{{ floor($width * .75) }}"></iframe>
		</div>
	<?php endif; ?>	
	
{{ csrf_field() }}

</form>

</div>
@endsection
