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
				<label for="title">Title:</label>
				<input type="text" name="title" class="form-control" />
			</div>

			<div class="entry-description-div">
			
				<label for="description">Description:</label>
				<textarea name="description" class="form-control entry-description-text" ></textarea>	
				
				<div style="margin-bottom:10px;">
					<button type="submit" name="update" class="btn btn-primary">Add</button>
				</div>	
								
				<label for="highlights">Highlights:</label>
				<textarea name="highlights" class="form-control entry-description-text" ></textarea>	
				<label for="distance">Distance:</label>
				<input type="text" name="distance" class="form-control" />
				<label for="difficulty">Difficulty:</label>
				<input type="text" name="difficulty" class="form-control" />
				<label for="elevation">Elevation:</label>
				<input type="text" name="elevation" class="form-control" />
				<label for="trail_type">Trail Type:</label>
				<input type="text" name="trail_type" class="form-control" />
				<label for="season">Season:</label>
				<input type="text" name="season" class="form-control" />
				<label for="costs">Cost:</label>
				<input type="text" name="cost" class="form-control" />
				<label for="parking">Parking:</label>
				<input type="text" name="parking" class="form-control" />
				<label for="public_transportation">Public Transportation:</label>
				<input type="text" name="public_transportation" class="form-control" />
				<label for="facilities">Facilities:</label>
				<input type="text" name="facilities" class="form-control" />
				<label for="wildlife">Wildlife:</label>
				<input type="text" name="wildlife" class="form-control" />
			</div>

			<div style="clear:both;" class="entry-title-div">
				<label for="map_link">Map Link:</label>
				<input type="text" name="map_link" class="form-control" />
				<label for="info_link">More Info Link:</label>
				<input type="text" name="info_link" class="form-control" />
			</div>
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Add</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>

</div>
@endsection
