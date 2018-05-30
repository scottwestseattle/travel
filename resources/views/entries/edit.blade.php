@extends('layouts.app')

@section('content')

<div class="container">

			<!-- Sub-menu ------>
			<div class="" style="font-size:20px;">
				<table class=""><tr>			
					<td style="width:40px;"><a href='/entries/index/'><span class="glyphCustom glyphicon glyphicon-list"></span></a></td>			
					<td style="width:40px;"><a href='/entries/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
				</tr></table>
			</div>			


	<h1>Edit Entry</h1>

	<form method="POST" action="/entries/update/{{ $entry->id }}">
		<div class="form-group form-control-big">
		
			<div class="entry-title-div">
				<input type="text" name="title" class="form-control" value="{{ $entry->title }}"  placeholder="Title" />
			</div>
			
	<?php
		$tags = [];
	?>
		
	@component('control-entry-tags', ['entry' => $entry])
	@endcomponent		
			
			
			<div class="entry-description-div">
				<textarea name="description" class="form-control entry-description-text" placeholder="Description" >{{ $entry->description }}</textarea>
			</div>

			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			{{ csrf_field() }}
		</div>
	</form>
	
</div>

@stop
