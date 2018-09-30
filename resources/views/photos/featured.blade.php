@extends('layouts.app')

@section('content')

<?php
	$add_link = '/photos/add/' . PHOTO_TYPE_SLIDER;
?>

<div class="xcontainer xpage-size">

	@component('entries.menu-submenu')@endcomponent
	
	<h1 style="font-size:1.5em;">
		@if (Auth::user() && Auth::user()->user_type >= 100)
			<a href="{{$add_link}}"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a>
			<span style="margin: 0 5px;">Featured Photos ({{ count($photos) }})</span><span style="font-size:.6em;"><a href="/first">Show</a></span>
		@endif
	</h1>
	
	<div class="{{SHOW_NON_XS}}">	
		@foreach($photos as $photo)
		<div style="float:left; margin:2px;">
			<a href="/photos/view/{{$photo->id}}">
				<img title="{{$photo->photo_title}}" src="{{$slider_path}}{{$photo->filename}}" style="height: 140px;"/>
			</a>
		</div>
		@endforeach
	</div>
	<div class="{{SHOW_XS_ONLY}}">
		<table class="table">
			<tbody>
			@foreach($photos as $photo)
				<tr>
					<td>
						<a href="/photos/view/{{$photo->id}}">
							<img title="{{$photo->photo_title}}" src="{{$slider_path}}{{$photo->filename}}" style="width: 100%;"/>
						</a>

						<table>
						
							@if (Auth::user() && Auth::user()->user_type >= 100)
								<tr><td>{{ $photo->filename }}</td></tr>
								@if (isset($photo->size))
									<tr><td>{{ number_format($photo->size) }} bytes</td></tr>
								@endif
							@endif
						
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							
							@if ($photo->main_flag === 1)
								<tr><td style=""><span class="glyphSliders glyphicon glyphicon-picture"></span>{{ $photo->main_flag === 1 ? 'Main Photo' : '' }}</td></tr>
							@endif
							
							@if (Auth::user() && Auth::user()->user_type >= 100)
								<tr>
									<td style="xpadding-top:15px;">
										<a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a>
										<a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a>
									</td>
								</tr>
							@endif
							
						</table>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>	
	</div>
</div>

@endsection
