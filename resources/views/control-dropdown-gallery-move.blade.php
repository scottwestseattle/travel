<form method="POST" action="/galleries/move/{{$photo_id}}">
@component('control-dropdown-menu', ['field_name' => 'parent_id', 'options' => $galleries, 'selected_option' => null])@endcomponent
	<div>
		<input type="hidden" value="{{$entry_id}}" name="entry_id" />
		<button type="submit" name="update" class="btn btn-primary" style="font-size:12px; padding:1px 4px; margin:5px;">Move to Gallery</button>
	</div>
	{{ csrf_field() }}
</form>