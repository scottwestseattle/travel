@extends('layouts.app')

@section('content')

<div class="container page-size">

	@if (isset($entry))
		@component('entries.menu-submenu', ['record' => $entry])@endcomponent
	@endif
	
	<h3>{{$record_title}}</h3>
	
	@if (isset($redirect))
	<p><a href="{{$redirect}}">Back to Transactions</a></p>
	@endif
	
	@if (Auth::user()->user_type >= 100)
	
	<form method="POST" action="/photos/entriesupdate">
		<div class="form-group form-control-big">

			@component('control-dropdown-date', ['div' => true, 'months' => $dates['months'], 'years' => $dates['years'], 'days' => $dates['days'], 'filter' => $filter])@endcomponent		

			<input type="hidden" name="parent_id" value="{{$record_id}}" />
			
			<div style="margin:10px 0;">				
				<button type="submit" name="update" class="btn btn-primary">Update All Dates</button>
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
	<h3>
		<a href="/photos/add/{{$type_flag}}/{{$id}}"><span class="glyphSliders glyphicon glyphicon-cloud-upload" style="padding:5px;"></span></a>
		<span style="margin-left: 5px;">{{$type}} Photos ({{ count($photos) }})</span>
	</h3>
	
	@endif
		<table class="table table-striped">
			<tbody>
			@foreach($photos as $photo)
			<?php $fullpath = $path . $photo->filename; ?>
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
						
							@if (Auth::user()->user_type >= 100)
								@if ($photo->gallery_flag <> 1)
									<tr><td><span style="color:red;">Not in Gallery</span></td></tr>
								@endif																	
								<tr><td>{{ $photo->filename }}</td></tr>
							@endif					
						
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							
							@if (isset($photo->display_date))
								<tr><td>{{ $photo->display_date }}</td></tr>
							@endif
							
							
							@if ($photo->main_flag === 1)
							<tr><td style=""><span class="glyphSliders glyphicon glyphicon-picture"></span>{{ $photo->main_flag === 1 ? 'Main Photo' : '' }}</td></tr>
							@endif
							
							@if (Auth::user()->user_type >= 100)
								<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
								<tr><td style="padding-top:15px;"><a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
								<tr><td style="padding-top:15px;"><a href="/photos/rotate/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-repeat"></span></a></td></tr>
								<tr><td style="padding-top:15px;">
								@if (isset($entry))
									@component('control-dropdown-gallery-move', ['entry_id' => $entry->id, 'photo_id' => $photo->id, 'galleries' => $galleries])@endcomponent
								@endif
								</td></tr>
							@endif
							
						</table>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
		
	@if (isset($entry) && isset($entry->photos))
	@if (Auth::user()->user_type >= 100)
	<h3>
		<a href="/galleries/share/{{$id}}"><span class="glyphSliders glyphicon glyphicon-duplicate" style="padding:5px;"></span></a>
		<span style="margin-left: 5px;">Gallery Photos ({{ count($entry->photos) }})</span>
	</h3>
	@endif
		<table class="table table-striped">
			<tbody>
			@foreach($entry->photos as $photo)
				<tr>
					<td>				
						<a href="/photos/view/{{$photo->id}}">
							<img title="{{$photo->alt_text}}" src="/img/entries/{{$photo->parent_id}}/{{$photo->filename}}" style="width: 100%; max-width:500px"/>
						</a>
					</td>
					
					<td>
						<table>
						
							@if (Auth::user()->user_type >= 100)
								<tr><td>{{ $photo->filename }} <a href="/photos/entries/{{$photo->parent_id}}">(Gallery)</a></td></tr>
							@endif									
						
							<tr><td>{{ $photo->alt_text }}</td></tr>
							<tr><td>{{ $photo->location }}</td></tr>
							
							@if (Auth::user()->user_type >= 100)
								@if (isset($entry->photo_id) && $entry->photo_id == $photo->id)
									<tr><td style="padding-top:15px;">Main Photo</td></tr>
								@else
									<tr><td style="padding-top:15px;"><a href="/galleries/setmain/{{$entry->id}}/{{$photo->id}}">Set as Main Photo</a></td></tr>
								@endif
								<tr><td style="padding-top:15px;"><a href="/galleries/attach/{{$entry->id}}/-{{$photo->id}}">Unlink</a></td></tr>
								<tr><td style="padding-top:15px;"><a href="/photos/edit/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-edit"></span></a></td></tr>
								<tr><td style="padding-top:15px;"><a href="/photos/rotate/{{$photo->id}}"><span class="glyphSliders glyphicon glyphicon-repeat"></span></a></td></tr>
								<tr><td style="padding-top:15px;"><a href="/photos/confirmdelete/{{$photo->id}}"><span class="glyphSliders  glyphicon glyphicon-trash"></span></a></td></tr>
							@endif
							
						</table>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>		
	@endif	
		
</div>

@endsection
