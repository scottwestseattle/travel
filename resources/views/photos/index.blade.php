@extends('layouts.app')

<!--
@component('menu-submenu-entries')
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

	@guest
	@else
	
		@if (Auth::user()->user_type >= 1000)
			@component('menu-submenu-activities', ['record_id' => $record_id])
			@endcomponent
		@endif
				
	@endguest
		
	<h1 style="font-size:1.5em;">
	@guest
		<span style="margin-left: 5px;">Photos ({{ count($photos) }})</span>
	@else
		@if (Auth::user()->user_type >= 100)
		<a href="{{$add_link}}"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a>
		<span style="margin-left: 5px;">{{$title}} Photos ({{ count($photos) }})</span>
		@endif
	@endguest
	</h1>
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->filename; //dd($fullpath); ?>
				<tr>
					<td>
						<table>
						
						@guest
						@else
							@if (Auth::user()->user_type >= 100)
								<tr><td>{{ $photo->filename }}</td></tr>
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
					<td>
						<a href="/photos/view/{{$photo->id}}">
							<img title="{{ $photo->alt_text }}" src="{{$fullpath}}" style="width: 100%; max-width:500px"/>
						</a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>

@endsection
