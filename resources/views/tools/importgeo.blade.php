@extends('layouts.app')

@section('content')

<div class="container page-size">

	<h1>Import GEO Data</h1>
	
	@if ($status['error'])
		<h3>Import Error:</h3>
		<p>{{$status['error']}}</p>
	@else
		<h3>Total Records Imported:</h3>
		<h3><strong>{{number_format($status['endCount'])}}</strong></h3>
	@endif
	
	<h3 style="margin-top:20px;"><a type="button" class="btn btn-success" href="/importgeo">Continue Importing</a></h3>
	
</div>

@endsection
