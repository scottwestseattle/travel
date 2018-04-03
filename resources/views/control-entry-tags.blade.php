<?php //echo $this->Form->control('tags._ids', ['multiple' => true, 'type' => 'select', 'options' => $tags]); ?>

<?php

$currTags = [];
$tagList = '';
if (false && isset($entry->tags) && sizeof($entry->tags) > 0)
{
	foreach($entry->tags as $tag)
	{
		$currTags[$tag['id']] = $tag['name'];

		if (strlen($tagList) > 0)
			$tagList .= ' | ';
			
		$tagList .= $tag['name'];
		
		//echo $currTags[$tag['id']] . '<br/>';	
	}
}

?>

@if (sizeof($currTags) > 0)
	
<div class="container" style="float:left; margin-top:20px;">
	<div class="row">
		<div class="col-lg-12">
			<div class="button-group">
	 
				<?php if (strlen($tagList) == 0) : ?>
					<button type="button" class="btn btn-info btn-md dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-tag"></span> <span class="caret"></span></button>
				<?php else : ?>
					<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown"><span style="font-size: 1em;"><?= $tagList ?></span> <span class="caret"></span></button>
				<?php endif; ?>
				
				<ul class="dropdown-menu">

					<?php				
						$ix = 0;
						foreach($tags as $key => $value)
						{
							$checked = (isset($currTags) && isset($currTags[$key])) ? 'checked' : '';

							echo '<li><a href="#" class="small" data-value="option1" tabIndex="-1">'
							. '<input type="checkbox" ' . $checked . ' value="' . $key . '"' . ' name="tags[_ids][' . $ix++ . ']"' . '/>&nbsp;&nbsp;' . $value  			
							. '</a></li>';
						}
					?>			
				   
				</ul>
			</div>
		</div>
	</div>
</div>

<div style="clear:both;"></div>

@endif
