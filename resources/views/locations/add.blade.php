@extends('layouts.app')

@section('content')

<div class="container page-size">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/locations/indexadmin/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
		</tr></table>
		@endif
	@endguest

	<h1>Add</h1>
               
	<form method="POST" action="/locations/create">
		
<!--
0 = World
1 = Continent
2 = Country
3 = Region
4 = State
5 = Zone
6 = City/Place
7 = Neighborhood/Area
-->

		<div class="form-group">
			<label for="location_type">Type:&nbsp;</label>		
			<select name="location_type" id="location_type">
				<option value="100">Continent</option>
				<option value="200">Sub-Continent</option>
				<option value="300">Country</option>
				<option value="400">Region</option>
				<option value="500">State</option>
				<option value="600">Zone</option>
				<option value="700">City/Place</option>
				<option value="800">Neighborhood/Area</option>
			</select>
		</div>

		<div class="form-group">
			<label for="parent_id">Parent:&nbsp;</label>		
			<select name="parent_id" id="parent_id">
				<option value="0">World</option>
				@foreach($records as $record)
					<option value="{{$record->id}}">{{$record->name}}</option>
				@endforeach
			</select>
		</div>

		<div class="form-group">
			<input type="text" name="name" class="form-control"  placeholder="Name"></input>
		</div>
		
		<div style="clear: both;" class="">
			<input type="checkbox" name="breadcrumb_flag" id="breadcrumb_flag" class="" />
			<label for="breadcrumb_flag" class="checkbox-big-label">Show as Bread Crumb</label>
		</div>			

		<div style="clear: both;" class="">
			<input type="checkbox" name="popular_flag" id="popular_flag" class="" />
			<label for="popular_flag" class="checkbox-big-label">Include in Popular</label>
		</div>			
		
		<div class="form-group">
			<button type="submit" class="btn btn-primary">Add</button>
		</div>
		
	{{ csrf_field() }}
	</form>
</div>

@endsection
