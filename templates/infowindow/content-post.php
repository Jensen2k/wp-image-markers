<?php
/**
 * Template part for displaying content post
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WPIM
 * @since 1.0
 * @version 1.0
 */

$post_id = $infowindow->get_content();

if ( !$post_id ) {
	return;
}

if ( $post = get_post( $post_id ) ):
	
	setup_postdata( $GLOBALS['post'] = & $post );
	?>

	<article <?php post_class() ?>>

		<?php if ( '' !== get_the_post_thumbnail() ) : ?>
			<div class="wpim-content-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'thumbnail' ); ?>
				</a>
			</div><!-- .post-thumbnail -->
		<?php endif; ?>

		<div class="wpim-content-header">
			<h4><?php the_title() ?></h4>
		</div>

		<div class="wpim-content-summary">
			<?php echo wp_trim_words( get_the_excerpt(), 20 ) ?>
		</div>

		<a class="wpim-content-morelink" href="<?php the_permalink() ?>" target="_blank"><?php echo esc_html__( 'View detail', 'wp-image-marker' ) ?></a>

	</article>

	<?php
	wp_reset_postdata();	
endif;
