@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('menu-submenu-entries', ['record_id' => $record_id])@endcomponent
	
	<h1 style="font-size:1.5em;">
	@guest
		<span style="margin-left: 5px;">Photos ({{ count($photos) }})</span>
	@else
		@if (Auth::user()->user_type >= 100)
		<a href="/photos/add/{{$type_flag}}/{{$id}}"><span class="glyphSliders glyphicon glyphicon-cloud-upload" style="padding:5px;"></span></a>
		<span style="margin-left: 5px;">{{$type}} Photos ({{ count($photos) }})</span>
		<a href="/photos/share/{{$type_flag}}/{{$id}}"><span class="glyphSliders glyphicon glyphicon-duplicate" style="padding:5px;"></span></a>
		@endif
	@endguest
	</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->filename; //dd($fullpath); ?>
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
								<!-- tr><td>{{ number_format($photo->size) }} bytes</td></tr -->
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
									<tr><td style="padding-top:15px;"><a href="/photos/rotate/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-repeat"></span></a></td></tr>
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
