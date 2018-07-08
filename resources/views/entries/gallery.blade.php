@extends('layouts.gallery')

@section('content')
	
<div id="container" style="background-color: yellow;" >

		<!------------------------------------------------------------------------------------------------------------->
		<!-- Content -------------------------------------------------------------------------------------------------->
		<!------------------------------------------------------------------------------------------------------------->
		<?php
			$w = 200;
			$h = 150;
		?>
		
		<div id="content" style='background-color: white; margin:0; padding:0; padding-bottom: 5px; min-height: 200px; '>
			<div style='text-align: center; margin-top: 200px;'>
				@foreach($records as $record)
					<div class='frontpage-box' >
						<!-- BACKGROUND PHOTO LINK -->
						<a href="/" class="frontpage-box-link" style="width: {{$w}}px; height: {{$h}}px; background-size: 100%; background-repeat: no-repeat; background-image: url('{{$record->photo_path}}{{$record->photo}}')" ></a>

						<div class='frontpage-box-text'>
						
							<!-- CAPTION/TITLE ------------------------------------------ -->
							<p>{{$record->title}}</p>
							
							<!-- DATE ------------------------------------------ -->
							@if (false)
							<p>{{$record->photo}}</p>	
							@endif
							
						</div>
					</div>	
				@endforeach			
			</div>
		</div>
		
</div><!-- container -->


@endsection
