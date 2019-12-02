@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('entries.menu-submenu')@endcomponent	

	<h1 style="font-size:1.3em;">@LANG('ui.Articles') ({{ count($records) }})</h1>

			<div class="row clearfix text-left">
				
				<table>
				<tbody>
				@foreach($records as $record)
					@if (($record->approved_flag != 1 || $record->published_flag !=1) && (!Auth::check() || Auth::user()->user_type < 1000))
						@continue
					@endif
					<tr style="vertical-align:top;">
						<td style="margin-bottom:10px;" >
							<a href="/entries/{{$record->permalink}}">
								@component('entries.show-main-photo', ['record' => $record, 'class' => 'index-article'])@endcomponent
							</a>							
						</td>
						<td style="color:default; padding: 0 10px;">
							<div style="font-size:11px;">
								@if ($record->approved_flag != 1 || $record->published_flag != 1)
								<div style="margin-bottom:5px;"><a style="color:default;" href="/entries/{{$record->permalink}}"><span style="color:red;">PRIVATE:</span> {{$record->title}}</a></div>
								@else
								<div style="font-size:{{strlen($record->title) > 50 ? '1' : '1.2'}}em; margin-bottom:5px;"><a style="color:default;" href="/entries/{{$record->permalink}}">{{$record->title}}</a></div>
								@endif
								@if (isset($record->location))
									@if ($record->location_type != LOCATION_TYPE_COUNTRY)
										<div style="font-size:1.1em; ">{{$record->location}}, {{$record->location_parent}}</div>
									@else
										<div style="font-size:1.1em;">{{$record->location}}</div>
									@endif
								@endif
																
								@if (isset($record->display_date))
									<div style="margin-top:5px;">{{App\Tools::translateDate($record->display_date)}}</div>
								@endif	

								<div>{{$record->view_count}} @LANG('ui.views')</div>

							</div>
						</td>
					</tr>
					<tr><td>&nbsp;</td><td></td></tr>
				@endforeach
				</tbody>
				</table>
					
			</div><!-- row -->		
	
</div>
@endsection
