<?php
/**
 * Plugin Name:Bp Clear Notifications
 * description: Clears all bp notifications for the logged in User
 * Version: 1.0
 * Pluhin URI:http://buddydev.com/plugins/bp-clear-notifications/
 * Author: Brajesh Singh
 * Author URI: http://buddydev.com/members/sbrajesh
 * License: GPL
 */

class ClearBpNotifications{
    private static $instance;
    private function __construct(){
        add_action('bp_loaded',array($this,'remove_bp_notifications_menu'));
        add_action( 'bp_adminbar_menus', array($this,'add_notifications_menu'), 8 );
        add_action('wp_print_scripts',array($this,'load_js'));
        add_action('wp_ajax_cn_clear_notifications',array($this,'clear_all_notifications'));
    }

    public function get_instance(){
        if(!isset(self::$instance))
                self::$instance=new self();
        return self::$instance;
    }
   function remove_bp_notifications_menu(){
       remove_action( 'bp_adminbar_menus', 'bp_adminbar_notifications_menu', 8 );
   }
   function add_notifications_menu(){
       global $bp;

	if ( !is_user_logged_in() )
		return false;

	echo '<li id="bp-adminbar-notifications-menu"><a href="' . $bp->loggedin_user->domain . '">';
	_e( 'Notifications', 'buddypress' );

	if ( $notifications = bp_core_get_notifications_for_user( $bp->loggedin_user->id,'object') ) { ?>
		<span><?php echo count( $notifications ) ?></span>
	<?php
	}

	echo '</a>';
	echo '<ul>';

	if ( $notifications ) {
		$counter = 0;
		for ( $i = 0, $count = count( $notifications ); $i < $count; ++$i ) {
			$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

			<li<?php echo $alt ?>><?php echo $notifications[$i] ?> <!--<span class='close-notification' id='bp-clear-notification-'>x</span> --></  li>

			<?php $counter++;
		}
      echo '<li><a id="clear-notifications" href="'.bp_core_get_user_domain(bp_loggedin_user_id()).'?clear-all=true'.'&_wpnonce='.wp_create_nonce('clear-all-notifications-for-'.bp_loggedin_user_id()).'"> [x] Clear All Notifications</a></li>';
	} else { ?>

		<li><a href="<?php echo $bp->loggedin_user->domain?>"><?php _e( 'No new notifications.', 'buddypress' ); ?></a></li>

	<?php
	}

	echo '</ul>';
	echo '</li>';
   }
   function load_js(){
      if(!is_user_logged_in())
          return;
      wp_register_script('clear-bp-notificatins',plugin_dir_url(__FILE__)."clear-notifications.js",array('jquery'));
      wp_enqueue_script('clear-bp-notificatins');
  }
//ajax'd delete notification
//let us ajaxify it
    function clear_all_notifications(){
        //CHECK VALIDITY OF NONCE
        check_ajax_referer('clear-all-notifications-for-'.bp_loggedin_user_id());
        self::delete_notifications_for_user(bp_loggedin_user_id());
        echo "1";
        exit(0);
    }
    //helper, delete all notifications for user
    function delete_notifications_for_user($user_id){
        global $bp, $wpdb;

            return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->core->table_name_notifications} WHERE user_id = %d ", $user_id ) );

    }
}
//instantiate
ClearBpNotifications::get_instance();
?>