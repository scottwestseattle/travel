@extends('layouts.app')

@section('content')

@if(session()->has('message.level'))
    <div class="alert alert-{{ session('message.level') }}"> 
    {!! session('message.content') !!}
    </div>
@endif

<?php
	$add_link = '/photos/add/';
	$id = (isset($id) ? $id : 0);
	if ($id > 0)
		$add_link .= $id;
?>

<div class="container">
	<h1 style="font-size:1.5em;"><a href="{{$add_link}}"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a><span style="margin-left: 5px;">Photos ({{ count($photos) }})</span></h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->filename; ?>
				<tr>
					<td>
						<table>
							<tr><td>{{ $photo->filename }}</td></tr>
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
							<tr><td style="padding-top:15px;"><a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
						</table>
					</td>
					<td><a href="/view/{{$photo->id}}"><img src="{{$fullpath}}" style="width: 100%; max-width:500px"/></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>
@endsection
