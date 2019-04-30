@extends('layouts.app')

@section('content')

<div class="page-size container">

	@if (Auth::check() && (Auth::user()->user_type >= 1000 || (Auth::user()->id === $record->user_id)))
		<!-- Sub-menu ------>
		<div class="" style="font-size:20px;">
			<table class=""><tr>			
				<td style="width:40px;"><a href='/lessons/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
			</tr></table>
		</div>			
	@endif

	<h1 style="font-size:1.3em;">@LANG('ui.Lessons') ({{ count($records) }})</h1>

		<div class="row clearfix text-left">
			
			<table>
			<tbody>
			@foreach($records as $record)
				@if (($record->approved_flag != 1 || $record->published_flag !=1) && (!Auth::check() || Auth::user()->user_type < 1000))
					@continue
				@endif
				<tr style="vertical-align:top;">
					<td style="margin-bottom:10px; background-color: green; color: white; padding:5px;" >
						<h1>1.1</h1>
					</td>
					<td style="color:default; padding: 0 10px;">
						<table>
						<tbody>
							@if ($record->approved_flag != 1 || $record->published_flag != 1)
							<tr><td style="font-size:1.3em;"><a style="color:default;" href="/lessons/{{$record->permalink}}"><span style="color:red;">PRIVATE:</span> {{$record->title}}</a></td></tr>
							@else	
							<tr><td style="font-size:1.3em;"><a style="color:default;" href="/lessons/{{$record->permalink}}">{{$record->title}}</a></td></tr>
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
