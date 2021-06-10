<?php
$target = in_array( BPAPR_Data::get( 'link_target', 'same' ), array( 'all', 'external' ) ) ? 'target="_blank"' : '';
?><div class="bpfb_final_link">
	<?php if ($image) { ?>
	<div class="bpfb_link_preview_container">
		<a href="<?php echo esc_url($url);?>" <?php echo $target; ?> ><img src="<?php echo esc_url($image); ?>" /></a>
	</div>
	<?php } ?>
	<div class="bpfb_link_contents">
		<div class="bpfb_link_preview_title"><?php echo $title;?></div>
		<div class="bpfb_link_preview_url">
			<a href="<?php echo esc_url($url);?>" <?php echo $target; ?> ><?php echo $url;?></a>
		</div>
		<div class="bpfb_link_preview_body"><?php echo $body;?></div>
	</div>
</div>