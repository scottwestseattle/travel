@extends('layouts.app')

@section('content')

<div class="page-size container">

		@if (Auth::check() && (Auth::user()->user_type >= 1000 || Auth::user()->id === $record->user_id))
			<!-- Sub-menu ------>
			<div class="" style="font-size:20px;">
				<table class=""><tr>			
					<td style="width:40px;"><a href='/entries/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
				</tr></table>
			</div>			
		@endif

	<h1 style="font-size:1.3em;">Articles ({{ count($records) }})</h1>

			<div class="row clearfix text-left">
				
				<table>
				<tbody>
				@foreach($records as $record)
					<tr style="vertical-align:top;">
						<td style="margin-bottom:10px;" >
							<a href="/entries/{{$record->permalink}}">
								<?php if (!isset($record->photo)) { $record->photo_path = '.'; $record->photo = TOUR_PHOTO_PLACEHOLDER; } ?>
								<div style="min-width:150px; min-height:100px; background-color: white; background-size: cover; background-position: center; background-image: url('{{$record->photo_path}}/{{$record->photo}}'); "></div>
							</a>							
						</td>
						<td style="color:default; padding: 0 10px;">
							<table>
							<tbody>
								<tr><td style="font-size:1.3em;"><a style="color:default;" href="/entries/{{$record->permalink}}">{{$record->title}}</a></td></tr>
								@if (isset($record->display_date))
								<tr><td>{{$record->display_date}}</td></tr>
								@endif
								@if (isset($record->location))
								<tr><td>{{$record->location}}, {{$record->location_parent}}</td></tr>
								@endif
							</tbody>
							</table>
						</td>
					</tr>
					<tr><td>&nbsp;</td><td></td></tr>
				@endforeach
				</tbody>
				</table>
					
			</div><!-- row -->		
	
</div>
@endsection
