@php
	$id = isset($record) ? $record->id : 0;
	$marginTop = isset($marginTop) ? $marginTop : 10;
@endphp
<div class="text-center" style="margin-top: {{$marginTop}}px; background-color: #f1f1f1; border-radius:15px;">
	<div style="display: inline-block; width: 95%; max-width:500px;">	
		<div style="" class="sectionHeader main-font">	
			<h3>@LANG('content.Leave a Comment')</h3>
		</div>

		<div class="text-left" style="font-size: 1em;">
			<form method="POST" action="/comments/create">
		
				<input type="hidden" name="parent_id" value="{{$id}}" />	
				
				<label for="name" class="control-label">@LANG('ui.Name'):</label>
				<input type="text" name="name" class="form-control" />

				<label for="comment" class="control-label" style="margin-top:20px;">@LANG('content.Comment'):</label>
				<textarea name="comment" class="form-control"></textarea>
	
				<div class="submit-button text-center" style="margin: 20px 0;">
					<button type="submit" name="update" class="btn btn-primary">@LANG('ui.Submit')</button>
				</div>

				{{ csrf_field() }}

			</form>
		</div>
	</div>
</div>
