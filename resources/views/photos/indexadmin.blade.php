@extends('layouts.app')

<!--
@component('entries.menu-submenu')
@endcomponent
-->

@section('content')

<?php
	$add_link = '/photos/add/';
	$id = (isset($id) ? $id : 0);
	if ($id > 0)
		$add_link .= $id;
	
	if (!isset($title))
		$title = '';
	
	if (!isset($record_id))
		$record_id = null;
?>

<div class="container">
	
	<h1 style="font-size:1.5em;">
	@guest
		<span style="margin-left: 5px;">Photos ({{ count($photos) }})</span>
	@else
		@if (Auth::user()->user_type >= 100)
		<span style="margin-left: 5px;">{{$title}} Photos ({{ count($photos) }})</span>
		@endif
	@endguest
	</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->parent_id . '/' . $photo->filename; ?>
				<tr>
					<td>
						<?php
							$alt_text = $photo->alt_text;
							if (strlen($photo->location) > 0)
								$alt_text .= ' - ' . $photo->location;
						?>					
						<a href="/photos/view/{{$photo->id}}">
							<img title="{{ $alt_text }}" src="{{$fullpath}}" style="width: 100%; max-width:500px"/>
						</a>
					</td>
					
					<td>
						<table>
						
						@guest
						@else
							@if (Auth::user()->user_type >= 100)
								<tr><td>{{ $photo->filename }}</td></tr>
								<tr><td>{{ number_format($photo->size) }} bytes</td></tr>
							@endif
						@endguest						
						
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							@if ($photo->main_flag === 1)
							<tr><td style=""><span class="glyphSliders glyphicon glyphicon-picture"></span>{{ $photo->main_flag === 1 ? 'Main Photo' : '' }}</td></tr>
							@endif
							
							@guest
							@else
								@if (Auth::user()->user_type >= 100)
									<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
									<tr><td style="padding-top:15px;"><a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
								@endif
							@endguest
							
						</table>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>

@endsection
