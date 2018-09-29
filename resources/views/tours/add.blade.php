@extends('layouts.app')

@section('content')

<div class="page-size container">

	@component('menu-submenu-tours')
	@endcomponent

	<h1>Add</h1>
               
	<form method="POST" action="/tours/create">
		<div class="form-control-big">	
			<div class="entry-title-div">
				<label for="title">Title:</label>
				<input type="text" id="title" name="title" class="form-control" />
			</div>
			
			<div class="entry-title-div">
				<a href='#' onclick="javascript:urlEncode('title', 'permalink')";>
					<span id="" class="glyphCustom glyphicon glyphicon-copy" style="font-size:1.3em; margin-left:5px;"></span>
				</a>						
				<input type="text" id="permalink" name="permalink" class="form-control"  placeholder="Permalink" />
			</div>						

			<div class="entry-description-div">
			
				<label for="description">Description:</label>
				<textarea name="description" class="form-control entry-description-text" ></textarea>	
				
				<div style="margin-bottom:10px;">
					<button type="submit" name="update" class="btn btn-primary">Add</button>
				</div>	
								
				<label for="highlights">Highlights:</label>
				<textarea name="description_short" class="form-control entry-description-text" ></textarea>
				
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
			
				<label for="map_label">Map Label:</label>
				<input type="text" name="map_label" class="form-control" />

				<label for="map_link">Map Link:</label>
				<input type="text" name="map_link" class="form-control" />
				
				<label for="map_labelalt">Map Alt Label:</label>
				<input type="text" name="map_labelalt" class="form-control" />

				<label for="map_label2">Map 2 Label:</label>
				<input type="text" name="map_label2" class="form-control" />

				<label for="map_link2">Map 2 Link:</label>
				<input type="text" name="map_link2" class="form-control" />

				<label for="map_labelalt2">Map 2 Alt Label:</label>
				<input type="text" name="map_labelalt2" class="form-control" />
								
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
