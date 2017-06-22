jQuery( document ).ready( function() {
	
    var jq = jQuery;
	
    jq( document ).on( 'click', '#clear-notifications,#wp-admin-bar-clear-notifications a, .header-notifications .pop a:last-child',  function() {
        var $this = jq( this );
		
        var nonce = get_var_in_url( $this.attr( 'href' ), '_wpnonce' );
		
        $this.text( 'clearing...' );//bad idea as localization problem
		
        jq.post( ajaxurl, {
							action:		'bpcn_clear_notifications',
							_wpnonce:	nonce,
							cookie:		encodeURIComponent(document.cookie)
						},
						function( resp ) {
							
							if( resp == 1 ) {

								//remove notification count
								jq( "#bp-adminbar-notifications-menu").find( 'span' ).remove();
								jq( "#wp-admin-bar-bp-notifications").find( 'span' ).text( '0' );
								jq( "#wp-admin-bar-bp-notifications").find( 'ul' ).remove();
								jq( "#bp-adminbar-notifications-menu>ul").remove();

								// It is a temporary solution but the boss theme has really messed up notification drop down.
								// It is not possible to identify the link for clearing.
								jq('.header-notifications').find('.pop').remove();
								jq('.header-notifications').find('#ab-pending-notifications').remove();
								//remove all notifications

								$this.remove();//in case someone has used it somewhere else

							}
						}
				);
        return false;//prevent default action
    });

    //for individual notification item
    jq( document ).on( 'click', '#bp-adminbar-notifications-menu span.close-notification',  function() {
        
		var $li = jq( this ).parent();
        return false;
    });

	/**
	 * Get query string from the url
	 * 
	 * @param {type} url
	 * @param {type} name
	 * @returns {clear-notifications_L1.get_var_in_url.qv|String}
	 */
	function get_var_in_url( url, name ) {
    
		var urla = url.split("?");
		
		var qvars = urla[1].split("&");//so we hav an arry of name=val,name=val
		for(var i = 0; i < qvars.length; i++ ) {
			
			var qv = qvars[i].split("=");
			
			if( qv[0] === name ) {
                return qv[1];
            }
		}
      
		return '';
	}
});