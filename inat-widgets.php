<?php
/**
 * Plugin Name: iNaturalist v1
 * Plugin URI: http://www.inaturalist.org
 * Description: This plugin connects your wordpress to inaturalist platform 
 * Version: 1
 * Author: Julià Mestieri for Projecte Ictineo SCCL (http://projecteictineo.com) 
 * Author URI: http://projecteictineo.com
 * License: aGPLv3
 */
/**
 * Adds Foo_Widget widget.
 */
class iNatLogin_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
    // info en el llistat de widgets
		parent::__construct(
			'inat_login_widget', // Base ID
			__('iNaturalist Login', 'inat'), // Name
			array( 'description' => __( 'iNaturalist plugin lateral block for user autentication (or creation)', 'text_domain' ), ) // Args
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
		//echo __( 'Hello, World!', 'text_domain' );
  if(isset($_COOKIE) &&
    array_key_exists('inat_code', $_COOKIE) &&
    (!array_key_exists('inat_access_token', $_COOKIE) || $_COOKIE['inat_access_token'] == NULL))
  {
      if(!array_key_exists('access_token', $_COOKIE)) {
        echo '<a href="'.get_option('inat_base_url').'/oauth/authorize?client_id='.get_option('inat_login_id','').'&redirect_uri='.get_option('inat_login_callback','').'&response_type=code">'. __('Autorize this app','inat'). '</a>';
      } else {
        //$_COOKIE['inat_access_token'] = $req['access_token'];
      }

    } elseif(!isset($_COOKIE) || !array_key_exists('inat_access_token', $_COOKIE) || $_COOKIE['inat_access_token'] == NULL) {
      echo '<a href="'.get_option('inat_base_url').'/oauth/authorize?client_id='.get_option('inat_login_id','').'&redirect_uri='.get_option('inat_login_callback','').'&response_type=code">'. __('Autorize this app','inat'). '</a> or <a href="'.site_url().'/inat/add/user">'.__('create new user','inat').'</a>';
    }
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
			$title = __( 'iNaturalist Login', 'inat' );
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
function register_foo_widget() {
    register_widget( 'iNatLogin_Widget' );
}
add_action( 'widgets_init', 'register_foo_widget' );
?>
