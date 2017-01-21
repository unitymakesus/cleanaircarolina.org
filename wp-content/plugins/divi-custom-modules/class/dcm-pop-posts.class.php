<?php

/**
 * Created by PhpStorm.
 * User: Fred
 * Date: 16/08/2016
 * Time: 00:49
 */
class DCM_Pop_Posts extends WP_Widget{


	function __construct() {
		$params = array(
			'description' => 'Display Popular Posts',
			'name'        => 'Divichild Popular Posts'
		);
		parent::__construct( 'DCM_Pop_Posts', '', $params );

	}

	public function form( $instance ) {
		extract( $instance );
		$background = esc_attr( $instance['background'] );
		$color      = esc_attr( $instance['color'] );
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function () {
				// colorpicker field
				jQuery('.cw-color-picker').each(function () {
					var $this = jQuery(this),
						id = $this.attr('rel');

					$this.farbtastic('#' + id);
				});
			});
			//]]>
		</script>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ) ?>">Title:</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id( 'title' ) ?>"
				name="<?php echo $this->get_field_name( 'title' ) ?>"
				value="<?php if ( isset( $title ) ) {
					echo esc_attr( $title );
				} ?>"
			/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'numberOfPosts' ) ?>">Number of Posts:</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id( 'numberOfPosts' ) ?>"
				name="<?php echo $this->get_field_name( 'numberOfPosts' ) ?>"
				value="<?php if ( isset( $numberOfPosts ) ) {
					echo esc_attr( $numberOfPosts );
				} ?>"
			/>
		</p>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'popcateg' ) ?>">Category:</label>
			<input
				type="text"
				class="widefat"
				id="<?php echo $this->get_field_id( 'popcateg' ) ?>"
				name="<?php echo $this->get_field_name( 'popcateg' ) ?>"
				value="<?php if ( isset( $popcateg ) ) {
					echo esc_attr( $popcateg );
				} ?>"
			/>
		<?php
	}


	public function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		if ( ! isset( $title ) ) {
			$title = '';
		}
		if ( ! isset( $numberOfPosts ) ) {
			$numberOfPosts = 5;
		}
		if ( ! isset( $popcateg ) ) {
			$popcateg = '';
		}
		$background = $instance['background'];
		$color      = esc_attr( $instance['color'] );
		?>
		<?php echo $before_widget; ?>
		<?php
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		?>
		<div class="side-list">
			<ul>
				<?php
				$wpbp = new WP_Query( array( 'post_type'      => 'post',
				                             'paged'          => 1,
				                             'posts_per_page' => $numberOfPosts,
				                             'orderby'        => 'comment_count',
				                             'category_name'  => $popcateg
				) );
				$temp_out = "";
				if ( $wpbp->have_posts() ) : while ( $wpbp->have_posts() ) : $wpbp->the_post();
					?>
					<li>
						<div class="wid_img_container">
						<a class="pop-posts-title" href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium_crop'); the_title(); ?></a>
						</div>
					</li>
					<?php
				endwhile; endif;
				wp_reset_query();
				?>
			</ul>
		</div>
		<?php echo $after_widget; ?><!-- Disclaimer -->
		<?php
	}
}

add_action( 'widgets_init', 'register_divichild_PopularPosts' );
function register_divichild_PopularPosts() {
	register_widget( 'DCM_Pop_Posts' );
	function sample_load_color_picker_script() {
		wp_enqueue_script( 'farbtastic' );
	}

	function sample_load_color_picker_style() {
		wp_enqueue_style( 'farbtastic' );
	}

	add_action( 'admin_print_scripts-widgets.php', 'sample_load_color_picker_script' );
	add_action( 'admin_print_styles-widgets.php', 'sample_load_color_picker_style' );

}
