<div class="bpfb_images">
<?php $rel = md5(microtime() . rand());?>
<?php foreach ($images as $img) { ?>
	<?php if (!$img) continue; ?>
	<?php if (preg_match('!^https?:\/\/!i', $img)) { // Remote image ?>
		<img src="<?php echo esc_url($img); ?>" />
	<?php } else { ?>
		<?php $info = pathinfo(trim($img));?>
		<?php $thumbnail = file_exists( bpapr_get_image_dir($activity_blog_id) . $info['filename'] . '-bpfbt.' . strtolower($info['extension'])) ?
			bpapr_get_image_url($activity_blog_id) . $info['filename'] . '-bpfbt.' . strtolower($info['extension'])
			:
			bpapr_get_image_url($activity_blog_id) . trim($img)
		;
		$target = 'all' == BPAPR_Data::get( 'link_target', 'same' ) ? 'target="_blank"' : '';
		?>
		<a href="<?php echo esc_url( bpapr_get_image_url($activity_blog_id) . trim($img)); ?>" class="<?php echo $use_thickbox; ?>" rel="<?php echo $rel;?>" <?php echo $target; ?> >
			<img src="<?php echo esc_url($thumbnail);?>" />
		</a>
	<?php } ?>
<?php } ?>
</div>