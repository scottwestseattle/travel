@extends('layouts.app')

@section('content')

<div class="container">
	
	<h1>Gallery Photos ({{count($photos)}})</h1>

	<table class="table">
		<tbody>
		@foreach($photos as $photo)
			<tr>
				<td>				
					<a href="/photos/view/{{$photo->id}}">
						<img title="{{ $photo->alt_text }}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" style="width: 100%; max-width:300px"/>
					</a>
				</td>
				
				<td>
					<table>
						<tr><td style="padding-top:15px;"><a href="/galleries/attach/{{$entry->id}}/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-duplicate"></span></a></td></tr>
						<tr><td>{{ $photo->filename }}</td></tr>
						<tr><td>{{ $photo->alt_text }}</td></tr>
						<tr><td>{{ $photo->location }}</td></tr>
						<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
						<tr><td style="padding-top:15px;"><a href="/galleries/attach/{{$entry->id}}/-{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
					</table>
				</td>
			</tr>
		@endforeach
		</tbody>
	</table>
	
</div>

@endsection
