<?php
/**
 * Display page content in admin
 *
 * @package WPIM
 * @since 1.0
 * @version 1.0
 */
?>

<div class="wpim-toolbar">

	<div class="wpim-toolbar__left">
		<button type="button" class="wpim-toolbar__button wpim-toolbar--outline js-wpim-add-marker">
			<span class="dashicons dashicons-plus-alt"></span>
			<?php echo esc_html__( 'Add marker', 'wp-image-markers' ) ?>
		</button>

		<button type="button" class="wpim-toolbar__button wpim-toolbar--outline js-wpim-add-image">
			<span class="dashicons dashicons-format-image"></span>
			<?php echo esc_html__( 'Choose image', 'wp-image-markers' ) ?>
		</button>

		<div class='wpim-shortcode'>
			<?php
			global $post;
			$shortcode = sprintf( '[wp_image_markers id="%d"]', $post->ID );
			?>
			<input type="text" readonly value="<?php echo esc_attr( $shortcode ) ?>"/>
			<button type="button"><?php echo esc_html( 'Get Shortcode' ) ?></button>
		</div>

	</div>
	<div class="wpim-toolbar__right">
		<button type="button" class="wpim-toolbar__button js-wpim-savechanges">
			<span class="dashicons dashicons-yes"></span>
			<?php echo esc_html__( 'Save changes', 'wp-image-markers' ) ?>
		</button>
	</div>

</div>
<div class="wpim-content">
	<?php wp_nonce_field( 'wpim_markers', '_wpim_nonce' ) ?>
	<input type="hidden" id="_wpim_markers" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpim_markers', true ) ) ?>" name="_wpim_markers"/>
	<div class="wpim-layer draggable">

	</div>
</div>

<script type="text/html" id="wpim_template_marker">

	<div data-id="<%= id %>" id="wpim_marker_<%= id %>" title="<?php echo esc_attr__( 'Click to Edit', 'wp-image-markers' ) ?>" href="#" class="wpim-marker wpim-marker--<%= valueType%>">
		<div class="wpim-marker__toolbar">
			<a href="#" class="wpim-marker__copy" title="<?php echo esc_html__( 'Copy', 'wp-image-markers' ) ?>"><span class="dashicons dashicons-admin-page"></span></a>
			<a href="#" class="wpim-marker__edit" title="<?php echo esc_html__( 'Edit', 'wp-image-markers' ) ?>"><span class="dashicons dashicons-edit"></span></a>
			<a href="#" class="wpim-marker__remove" title="<?php echo esc_html__( 'Remove', 'wp-image-markers' ) ?>"><span class="dashicons dashicons-trash"></span></a>
		</div>
		<div class="wpim-marker__icon">
			<% if(valueType=='icon'){ %>
			<i class='<%= value%>'></i>
			<%}else if(valueType=='image'){
			value = value.split('|');
			if(value.length==2){
			value = wpim_var.upload_url+ decodeURIComponent(value[1]);
			}else{
			value = wpim_var.error_image_url;
			}
			%>
			<img src="<%= value%>" alt=""/>
			<%}else{%>
			<i><%= value%></i>
			<%}%>
			<span class="icon__arrow"></span>
		</div>
	</div>
</script>

<div id="wpim_dialog" title="<?php echo esc_html__( 'Marker Settings', 'wp-image-markers' ) ?>">
	<div class="wpim_dialog_inner">
		<form method="POST" name="wpim_dialog">
			<?php echo esc_html__( 'Loading...', 'wp-image-markers' ) ?>
		</form>
	</div>
</div>
