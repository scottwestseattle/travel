@extends('layouts.app')

<!--
@component('menu-submenu-entries')
@endcomponent
-->

@section('content')

<div class="container page-size">

	@component('menu-submenu-entries')@endcomponent
	
	<h1 style="font-size:1.5em;">
		<span style="margin-left: 5px;">Gallery ({{count($records)}})</span>
	</h1>
	
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td>
						{{$record->title}}
						<img height="200" src="{{$record->photo_path}}{{$record->photo}}" />
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
</div>

@endsection
