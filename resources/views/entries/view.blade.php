@extends('layouts.app')

@section('content')

<div class="page-size container">
               
<form method="POST" action="/entries/view/{{ $entry->id }}">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/entries/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/entries/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/entries/edit/{{$entry->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
			<td style="width:40px; font-size:20px;"><a href='/entries/confirmdelete/{{$entry->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a></td>
		</tr></table>
		@endif
	@endguest
		
	<div class="form-group">
		<h1 name="title" class="">{{$entry->title }}</h1>
	</div>

	@if (isset($tags))
	<div class="form-group">
	@foreach($tags as $tag)
		<span name="" class=""><a href="\tags\entries\{{$tag->id}}">{{$tag->name}}</a>&nbsp;>&nbsp;</span>		
	@endforeach
	</div>
	@endif
	
	<div class="entry-div">
		<div class="entry">
			<span name="description" class="">{!! nl2br($entry->description) !!}</span>	
		</div>
	</div>

	<?php 
		//
		// show main photo
		//
		$photo_found = false;
		$width = 800;
		$base_folder = 'img/tours/';
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

	@if (isset($photos))
	<div style="display:default; margin-top:20px;">
		@foreach($photos as $photo)
			@if ($photo->main_flag === 1)
				<img src="/img/tours/{{$entry->id}}/{{$photo->filename}}" title="{{$photo->alt_text}}" style="max-width:100%; width:{{ $width }}" />
			@endif
		@endforeach	
	</div>
	
	<div style="display:default; margin-top:5px;">
		@foreach($photos as $photo)
			@if ($photo->main_flag !== 1)
				<img style="width:100%; max-width:400px; margin-bottom:5px;" title="{{$photo->alt_text}}" src="/img/tours/{{$entry->id}}/{{$photo->filename}}" />		
			@endif
		@endforeach	
	</div>
	@endif
	
{{ csrf_field() }}

</form>

</div>
@endsection
