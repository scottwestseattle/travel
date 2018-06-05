@extends('layouts.app')

@section('content')

<?php 

$header = 'Entries';
if (isset($title))
{
	$header = $title;
}

function getTypeName($type_flag)
{
	$name = '';
	
	switch($type_flag)
	{
		case ENTRY_TYPE_NOTSET:
			$name = 'Not Set';
			break;
		case ENTRY_TYPE_ENTRY:
			$name = 'Entry';
			break;
		case ENTRY_TYPE_TOUR:
			$name = 'Tour/Hike';
			break;
		case ENTRY_TYPE_BLOG:
			$name = 'Blog';
			break;
		case ENTRY_TYPE_BLOG_ENTRY:
			$name = 'Blog Entry';
			break;
		case ENTRY_TYPE_ARTICLE:
			$name = 'Article';
			break;
		case ENTRY_TYPE_NOTE:
			$name = 'Note';
			break;
		case ENTRY_TYPE_OTHER:
			$name = 'Other';
			break;
		default:
			$name = 'Not found';
			break;
	}
	
	return $name;
}

?>

<div class="page-size container">

	<!-- Sub-menu ------>
	<div class="" style="font-size:20px;">
		<table class=""><tr>			
			<td style="width:40px;"><a href='/entries/add/'><span class="glyphCustom glyphicon glyphicon-plus-sign"></span></a></td>			
		</tr></table>
	</div>			

	<h1 style="font-size:1.3em;">{{ $header }} ({{ count($records) }})</h1>
	@if (Auth::check())
		<table class="table table-striped">
			<tbody>
			@foreach($records as $record)
				<tr>
					<td style="width:20px;"><a href='/entries/edit/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a></td>
					<td style="width:20px;"><a href='/photos/entries/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-picture"></span></a></td>
					<td style="width:20px;"><a href='/entries/publish/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-flash"></span></a></td>
					<td>
						<a href="{{ route('entry.permalink', [$record->permalink]) }}">{{$record->title}}&nbsp;({{getTypeName($record->type_flag) . ', ' . intval($record->photo_count) . ' photo(s)'}})</a>
						
						<?php if (intval($record->view_count) > 0) : ?>
							<span style="color:#8CB7DD; margin-left: 5px; font-size:.9em;" class="glyphCustom glyphicon glyphicon-copy"><span style="font-family:verdana; margin-left: 2px;" >{{ $record->view_count }}</span></span>
						<?php endif; ?>
						
						@if (strlen($record->permalink) === 0)
							<div><a href="/entries/edit/{{$record->id}}"><button type="button" class="btn btn-danger btn-alert">No Permalink</button></a></div>
						@endif
												
					</td>
					<td>
						<a href='/entries/confirmdelete/{{$record->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
				</tr>
			@endforeach
			</tbody>
		</table>
	@else
		<h3>You need to log in. <a href="/login">Click here to login</a></h3>
	@endif       
</div>
@endsection
