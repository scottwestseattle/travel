@extends('layouts.app')

<!--
@component('menu-submenu-tours')
@endcomponent
-->

@section('content')

<?php
	$add_link = '/photos/add/';
	$id = (isset($id) ? $id : 0);
	if ($id > 0)
		$add_link .= $id;
?>

<div class="container">
	@if ($photo_type == 2)
	<a href="/entries/view/{{$id}}">Back to Tour</a>
	@endif
	<h1 style="font-size:1.5em;">
		<a href="{{$add_link}}"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a>
		<span style="margin-left: 5px;">{{$title}} Photos ({{ count($photos) }})</span>
	</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->filename; //dd($fullpath); ?>
				<tr>
					<td>
						<table>
							<tr><td>{{ $photo->filename }}</td></tr>
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							@if ($photo->main_flag === 1)
							<tr><td style=""><span class="glyphSliders glyphicon glyphicon-picture"></span>{{ $photo->main_flag === 1 ? 'Main Photo' : '' }}</td></tr>
							@endif
							<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
							<tr><td style="padding-top:15px;"><a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
						</table>
					</td>
					<td><img title="{{ $photo->alt_text }}" src="{{$fullpath}}" style="width: 100%; max-width:500px"/></td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>

@endsection
