@extends('layouts.app')

@section('content')

@component('menu-submenu')
	@component('menu-icons-start')@endcomponent
	@component('menu-icons-links', ['data' => (isset($data) ? $data : null)])@endcomponent	
	@component('control-search')@endcomponent	
@endcomponent

<div style="margin:0;padding:0; margin-top:20px; margin-left:50px;" class="single-view-page container">
               
<form method="POST" action="/hasher">

	<?php //dd($hash); ?>
	<div class="form-group">
		<label name="" class="">Enter Text:</label>	
		<input type="text" name="hash" class="form-control" value="{{ $hash }}" />
	</div>
	
	<div class="form-group">
		<span>{{ $hashed }}</span>
	</div>	
		
	<div class="form-group">
		<button type="submit" name="submit" class="btn btn-primary">Submit</button>
	</div>
	
{{ csrf_field() }}
</form>

</div>
@endsection
