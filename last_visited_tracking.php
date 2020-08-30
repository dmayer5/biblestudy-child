<?php
require('wp-load.php');
if(is_user_logged_in()):
    if(isset($_POST['user_id']) && isset($_POST['user_visiting'])):
        $user_id = get_current_user_id();
        $vsit_url = sanitize_url($_POST['user_visiting']);
        if( get_user_meta($user_id, 'user_last_visited', true)){
            if(update_user_meta( $user_id, 'user_last_visited',$vsit_url )):
            endif;
        }
        else{
            if(add_user_meta( $user_id, 'user_last_visited',$vsit_url )):
            endif;
        }
        echo get_user_meta($user_id, 'user_last_visited', true);
    endif;
endif;