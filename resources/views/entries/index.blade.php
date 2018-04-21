@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search')@endcomponent	
@endcomponent

<div class="container">
	<h1 style="font-size:1.3em;">Entries ({{ count($entries) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
			@foreach($entries as $entry)
				<tr>
					<td style="width:20px;">
						<a href='/entries/edit/{{$entry->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td style="width:50px;">
					{{ ($entry->is_template_flag ? 'TOUR' : 'POST') }}
					</td>
					<td>
						<a href="/entries/gen/{{$entry->id}}">{{$entry->title}}</a>
						
						<?php if (intval($entry->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $entry->view_count }}</span></span>
						<?php endif; ?>
						
					</td>
					<td>
						<a href='/entries/confirmdelete/{{$entry->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
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
