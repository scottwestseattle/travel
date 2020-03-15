@extends('layouts.app')

@section('content')

<div class="page-size container">		
	<h1>@LANG('content.Visitor Countries') ({{count($records['countries'])}})</h1>

	@foreach($records['countries'] as $record)
	
		<div class="text-center flag-box gray">
			<div style="margin-top:10px;"><img height="70" src="/img/flags/{{strtolower($record->countryCode)}}.png" /></div>
			<div class="flag-box-footer">@LANG('geo.' . $record->country)</div>
		</div>
		
	@endforeach
	
</div>
@endsection
