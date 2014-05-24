<?php
/**
 * Adds Foo_Widget widget.
 */
class iNatLinks_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
    // info en el llistat de widgets
		parent::__construct(
			'inat_links_widget', // Base ID
			__('iNaturalist Menu', 'inat'), // Name
			array( 'description' => __( 'iNaturalist menu links to main lists', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget']; // no tocar
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
    //'.site_url(). '/?'. http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa', 'id' => $taxa->parent_id)).'
    echo '<ul class="menu inat-menu">';
    echo ' <li><a href="'.site_url() .'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'observations')).'">'.__('Observations','inat').'</a></li>';
    echo ' <li><a href="'.site_url() .'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'places')).'">'.__('Places','inat').'</a></li>';
    echo ' <li><a href="'.site_url() .'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'projects')).'">'.__('Projects','inat').'</a></li>';
    echo ' <li><a href="'.site_url() .'/?'.http_build_query(array('page_id' => get_option('inat_post_id'), 'verb' => 'taxa')).'">'.__('Species','inat').'</a></li>';
    echo '</ul>';
		//echo __( 'Hello, World!', 'text_domain' );
		echo $args['after_widget']; // no tocar
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
   * opcions de configuracio
	 */
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'iNaturalist menu', 'inat' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Foo_Widget
// register Foo_Widget widget
function register_inatlinks_widget() {
    register_widget( 'iNatLinks_Widget' );
}
add_action( 'widgets_init', 'register_inatlinks_widget' );
?>
