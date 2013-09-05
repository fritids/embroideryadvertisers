<?
// logout url redirect
add_filter('logout_url', 'projectivemotion_logout_home', 10, 2);
function projectivemotion_logout_home($logouturl, $redir)
{ $redir = get_option('siteurl'); return $logouturl . '&amp;redirect_to=' . urlencode($redir); }

// wrong info redirect
add_filter('login_redirect', '_catch_login_error', 10, 3);
 
function _catch_login_error($redir1, $redir2, $wperr_user)
{
    if(!is_wp_error($wperr_user) || !$wperr_user->get_error_code()) return $redir1;
    switch($wperr_user->get_error_code())
    {
        case 'incorrect_password': case 'empty_password': case 'invalid_username': default:
            wp_redirect('/?action=loginfailed'); // modify this as you wish
    }
    return $redir1;
}

// login url redirect
add_filter("login_redirect", "my_login_redirect", 10, 7);
function my_login_redirect( $redirect_to, $request, $user ){
    //is there a user to check?
    if( is_array( $user->roles ) ) {
        //check for admins
        if( in_array( "administrator", $user->roles ) ) {
            // redirect them to the default place
            return $redirect_to;
        } else {
            return home_url();
        }
    }
}
?>
