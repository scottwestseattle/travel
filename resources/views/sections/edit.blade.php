@extends('layouts.app')

@section('content')

<div class="container page-size">

	@component('sections.menu-submenu', ['record' => $record])@endcomponent	

	<h1>Edit Section</h1>

	<form method="POST" action="/sections/update/{{ $record->id }}">
		<div class="form-group form-control-big">
		
			<input type="hidden" name="type_flag" value={{$record->type_flag}} />
		
			<input type="text" id="title" name="title" class="form-control" value="{{ $record->title }}"  placeholder="Title" />
			
			<div style="clear:both;">				
				<div class="">
					<button type="submit" name="update" class="btn btn-primary">Save</button>
				</div>
			</div>
			
			<div class="entry-description-div">
				<textarea name="description" rows="12" class="form-control" placeholder="Description" >{{ $record->description }}</textarea>
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

@endsection