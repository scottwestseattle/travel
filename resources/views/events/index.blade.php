@extends('layouts.app')

@section('content')

<div class="page-size container">
		
		@component('menu-submenu-events')@endcomponent

		<div style="margin:15px 0;">
			<a style="margin-right:10px;" href="/events/index">Show All</a>
			<a style="margin-right:10px;" href="/events/index/{{LOG_TYPE_INFO}}">Info</a>
			<a style="margin-right:10px;" href="/events/index/{{LOG_TYPE_WARNING}}">Warnings</a>
			<a style="margin-right:10px;" href="/events/index/{{LOG_TYPE_ERROR}}">Errors</a>
			<a style="margin-right:10px;" href="/events/index/{{LOG_TYPE_EXCEPTION}}">Exceptions</a>
		</div>
		
		<h3>Events ({{$records->count()}})</h3>
		
		<table style="width:100%;" class="xtable xtable-striped">
			<tbody>
			<?php $cnt = 0; ?>
			@foreach($records as $record)
				<?php
					$type = '';
					if ($record->type_flag == 1) $type = 'Info';
					if ($record->type_flag == 2) $type = 'Warning';
					if ($record->type_flag == 3) $type = 'Error';
					if ($record->type_flag == 4) $type = 'Exception';
					if ($record->type_flag == 5) $type = 'Other';
				?>
				
				<tr>
					<td>
						<table style="margin-bottom:0;" class="table">
							@if ($cnt++ == 0)
							<tr>
								<th>Timestamp</th>
								<th>Site</th>
								<th>Type</th>
								<th>Model</th>
								<th>Action</th>
							</tr>			
							@endif
							<tr>
								<td style="width:50px;">{{$record->created_at}}</td>
								<td style="width:50px;">{{$record->site_id}}</td>
								<td style="width:50px;">{{$type}}</td>
								<td style="width:50px;">{{$record->model_flag}}</td>
								<td style="width:50px;">{{$record->action_flag}}</td>
							</tr>
						</table>
						@if (isset($record->updates))
							<?php $parts = explode('  ', $record->updates); ?>
							<div style="padding:0px 5px 10px 5px;">
								Updates:|From|To|<br/>
								@foreach($parts as $part)
									{{$part}}<br/>
								@endforeach
							</div>
						@endif
						@if (isset($record->error))
							<div style="padding:0px 5px 10px 5px;">{{$record->error}}</div>
						@endif
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	
</div>
@endsection
