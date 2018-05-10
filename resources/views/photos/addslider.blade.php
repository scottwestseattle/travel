@extends('layouts.app')

@section('content')

<div class="page-size container">

	<h1>Add Slider</h1>
               			   
	<form method="POST" action="/photos/createslider/">
		<div class="form-control-big">	

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				Slider size = 1920 x 934
			</div>
		
			<!-- div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="file" name="image" id="image" class="" />
			</div -->

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="filename" class="form-control" placeholder="Optional: new photo name"/>
			</div>			

			<div style="clear:both; margin:20px 0; font-size:20px;" class="">
				<input type="text" name="alt_text" class="form-control" placeholder="Optional: alt text"/>
			</div>			
			
			<div class="">
				<button type="submit" name="update" class="btn btn-primary">Upload</button>
			</div>	
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>
@endsection
