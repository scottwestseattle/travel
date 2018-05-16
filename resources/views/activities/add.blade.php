@extends('layouts.app')

@section('content')

<div class="page-size container">

	@guest
	@else
		@if (Auth::user()->user_type >= 100)
		<table><tr>			
			<td style="width:40px; font-size:20px;"><a href='/activities/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>
		</tr></table>
		@endif
	@endguest


	<h1>Add</h1>
               
	<form method="POST" action="/activities/create">
		<div class="form-control-big">	
			<div class="entry-title-div">
				<input type="text" name="title" placeholder="Title" class="form-control" />
			</div>

			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description"></textarea>	
				
				<textarea name="highlights" class="form-control entry-description-text" placeholder="Highlights"></textarea>	
				<input type="text" name="distance" class="form-control" placeholder="Distance" />
				<input type="text" name="difficulty" class="form-control" placeholder="Difficulty" />
				<input type="text" name="elevation_change" class="form-control" placeholder="Elevation Change" />
				<input type="text" name="season" class="form-control" placeholder="Season" />
				<input type="text" name="entry_fee" class="form-control" placeholder="Entry Fee" />
				<input type="text" name="parking" class="form-control" placeholder="Parking" />
				<input type="text" name="public_transportation" class="form-control" placeholder="Public Transportation" />
				<input type="text" name="facilities" class="form-control" placeholder="Facilities" />
				<input type="text" name="wildlife" class="form-control" placeholder="Wildlife" />
			</div>

			<div style="clear:both;" class="entry-title-div">
				<input type="text" name="map_link" class="form-control"  placeholder="Map Link" />
			</div>
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
