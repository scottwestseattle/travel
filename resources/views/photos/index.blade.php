@extends('layouts.app')

@section('content')

<div class="container">
	<h1 style="font-size:1.5em;"><a href="/photos/add"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a><span style="margin-left: 5px;">Photos ({{ count($photos) }})</span></h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
				<tr>
					<td>
						<table>
							<tr><td>{{ $photo }}</td></tr>
							<tr><td style="padding-top:15px;"><a href="/photos/edit"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
							<tr><td style="padding-top:15px;"><a href="/photos/delete"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
						</table>
					</td>
					<td><a href="/view/{{$photo}}"><img src="/img/theme1/{{ $photo }}" style="width: 100%; max-width:500px"/></a></td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>
@endsection
