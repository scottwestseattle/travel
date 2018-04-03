<!-- 
Xcomponent('search-results', ['faqs' => $faqs])
Xendcomponent 
-->

<!-- search results -->

<div>
		<table class="table">
			<tbody>
				@foreach($faqs as $faq)
				<tr>
					<td style="width:10px;">
						<a href='/faqs/edit/{{$faq->id}}'><span class="glyphCustom glyphicon glyphicon-edit"></span></a>
					</td>
					<td style="width:10px; padding-right:20px;">
						<a href='/faqs/confirmdelete/{{$faq->id}}'><span class="glyphCustom glyphicon glyphicon-trash"></span></a>
					</td>
					<td>
					@if (isset($faq->link) && mb_strlen($faq->link) > 0 && mb_substr($faq->link, 0, 4) === "http")
						<a href="{{$faq->link}}" target="_blank">{{$faq->title}}</a>
					@else
						<span style="font-size:1.1em;"><a style="font-weight:bold; color:gray;" href="/faqs/view/{{$faq->id}}">{{$faq->title}}</a></span>
						<div>{!! $faq->description !!}</div>
					@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
</div>


