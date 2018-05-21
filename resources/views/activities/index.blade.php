@extends('layouts.app')

@section('content')

@if (false)
@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
@endcomponent
@endif

<?php 

$header = 'Activites';
if (isset($title))
{
	$header = $title;
}

?>

<div class="page-size container">
	
	<h1 style="font-size:1.3em;"><a href="/activities/add"><span class="glyphSliders glyphicon glyphicon-plus-sign" style="padding:5px;"></span></a>{{ $header }} ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/activities/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/activities/location/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-map-marker"></span></a></td>
					<td style="width:20px;"><a href='/photos/tours/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<?php 
						$loc = $record->locations()->orderByRaw('location_type DESC')->first(); 
						$loc = (isset($loc) ? $loc->name : 'no location'); 
					?>
					<td>
						<a href="{{ route('activity.view', [urlencode($record->title), $record->id]) }}">{{$record->title . ' (' . $loc . ')'}}</a>
							
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						<?php endif; ?>
						
						@if ($record->published_flag === 0 || $record->approved_flag === 0)
							<div class="publish-pills">
								<ul class="nav nav-pills">
									@if ($record->published_flag === 0)
										<li class="active"><a href="/activities/publish/{{$record->id}}">Private</a></li>
									@elseif ($record->approved_flag === 0)
										<li class="active"><a href="/activities/publish/{{$record->id}}">Pending Approval</a></li>
									@else
										<li class="active"><a href="/activities/publish/{{$record->id}}">Published</a></li>
									@endif
								</ul>
							</div>
						@endif
					</td>
					<td>
						<a href='/activities/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
