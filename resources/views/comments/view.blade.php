@extends('layouts.theme1')

@section('content')

<div class="page-size container" style="font-size:1.3em;">

	@component($prefix . '.menu-submenu', ['record' => $record, 'prefix' => $prefix])@endcomponent

	<h3>{{date_format($record->created_at, "l, F j, Y")}}</h3>	
               
	<h3>{{$record->name}}</h3>

	<p style="">{{$record->comment}}</p>
	
	<div class='text-center' style="margin-top: 50px;">
		<a href="/comments">
			<button style="margin-bottom:10px;" type="button" class="btn btn-info">@lang('content.Back to Comments')</button>
		</a>
	</div>
	
</div>
@endsection
