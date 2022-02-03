<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

    wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/custom.css', true);

}
    

//======================================================================
// CUSTOM DASHBOARD
//======================================================================
// ADMIN FOOTER TEXT
function remove_footer_admin () {
    echo "Divi Child Theme";
} 

add_filter('admin_footer_text', 'remove_footer_admin');

/**
 * Add a login/logout shortcode button
 * @since 1.0.0
 */
function login_logout() {
ob_start();
    if (is_user_logged_in()) : 
    // Set the logout URL - below it is set to the root URL
    ?>
    <a class="et_pb_button et_pb_button_0 et_pb_bg_layout_light" role="button" href="<?php echo wp_logout_url('/'); ?>">Log Out</a>

<?php 
    else : 
    // Set the login URL - below it is set to get_permalink() - you can set that to whatever URL eg '/whatever'
?>
    <a class="et_pb_button et_pb_button_0 et_pb_bg_layout_light" role="button" href="<?php echo wp_login_url(get_permalink()); ?>">Log In</span></a>

<?php 
    endif;

return ob_get_clean();
}
add_shortcode( 'login_logout', 'login_logout' );
/**
 * WordPress function for redirecting users on login based on user role
 */
function wpdocs_my_login_redirect( $url, $request, $user ) {
    if ( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
        if ( $user->has_cap( 'administrator' ) ) {
            $url = admin_url();
        } else {
            $url = home_url( 'https://www.movementfund.com/dashboard/' );
        }
    }
    return $url;
} 
add_filter( 'login_redirect', 'wpdocs_my_login_redirect', 10, 3 );
/* end login redirect */

/* start logout redirect */
function auto_redirect_after_logout(){
wp_redirect( 'https://www.movementfund.com/wp-admin');
exit();
}
add_action('wp_logout','auto_redirect_after_logout');
/* end logout redirect */

function show_user_name(){
    $affiliate_id = affwp_get_affiliate_id();
    $user_info = get_userdata( 1 );
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;
    echo $first_name . $last_name;
    
}
add_shortcode("showusername", show_user_name );

    //add shortcode for register date
    function date_joined(){
      $affiliate_id = affwp_get_affiliate_id(); 
      $user_info = get_userdata(2);
      $registered = $user_info->user_registered;
      return date( "M Y", strtotime( $registered ) );
    }
    add_shortcode('dateregistered', 'date_joined');
function give_profile_name($atts){
    $user=wp_get_current_user();
    $name=$user->user_firstname; 
    return $name;
}


add_filter( 'wp_nav_menu_objects', 'my_dynamic_menu_items' );
function my_dynamic_menu_items( $menu_items ) {
    foreach ( $menu_items as $menu_item ) {
        if ( '#profile_name#' == $menu_item->title ) {
            global $shortcode_tags;
            if ( isset( $shortcode_tags['profile_name'] ) ) {
                // Or do_shortcode(), if you must.
                $menu_item->title = call_user_func( $shortcode_tags['profile_name'] );
            }    
        }
    }

    return $menu_items;
}
add_shortcode('profile_name', 'give_profile_name');
add_shortcode('login',function(){
   $url = wp_login_url();
        if ( is_user_logged_in() ) {
            $url = wp_logout_url( home_url() );
            return "<a href='$url'> Logout</a>";
        } else {        
            return "<a href='$url'>Login</a>";
        }
});

/**
 * Code added by anshu 
 * 
 * @author Anshu Kushwaha 
 */

add_action( 'init','wwc_add_affiliate_shortcode');
function wwc_add_affiliate_shortcode(){

    // Add dashboard shortcode
    add_shortcode('wwc-affiliate-dashboard','wwc_dashboard_shortcode_callback');
    add_shortcode('wwc-view-affiliates','wwc_view_shortcode_callback');

        
}

// add_action( 'admin_menu', 'wwc_aff_settings_page' );

function wwc_aff_settings_page() {
    add_menu_page( 'WWC Affiliate Settings', 'WWC Affiliate Settings', 'manage_options', 'wwc-aft-menu', 'wwc_aft_menu_callback' );
}

add_action('admin_enqueue_scripts','wwc_add_scripts');
function wwc_add_scripts(){
    wp_enqueue_style( 'wwc-admin-style', get_stylesheet_directory_uri() . '/wwc-assets/css/wwc-admin-style.css' );
    wp_enqueue_script( 'wwc-admin-js', get_stylesheet_directory_uri() . '/wwc-assets/js/wwc-admin-js.js' );

}

add_action('wp_enqueue_scripts','wwc_add_frontend_scripts');
function wwc_add_frontend_scripts(){

    wp_enqueue_style( 'datatables-css', get_stylesheet_directory_uri() . '/wwc-assets/css/dataTables.css', array(), 'all' );
    wp_enqueue_style( 'wwc-front-css', get_stylesheet_directory_uri() . '/wwc-assets/css/wwc-front-style.css', true);

    wp_enqueue_script( 'wwc-front-js', get_stylesheet_directory_uri() . '/wwc-assets/js/wwc-front-js.js', array('jquery'),false );

    wp_enqueue_script( 'datatable-js', get_stylesheet_directory_uri() . '/wwc-assets/js/datatables.js', false );

    wp_enqueue_script( 'datatable-js', get_stylesheet_directory_uri() . '/wwc-assets/js/datatables.js', false );
    wp_enqueue_script( 'dtable',  get_stylesheet_directory_uri(). '/wwc-assets/js/dataTables.buttons.min.js', false );
    
    wp_enqueue_script( 'buttons',  get_stylesheet_directory_uri(). '/wwc-assets/js/buttons.html5.min.js', false );


    
}

function wwc_aft_menu_callback(){

    // echo "<pre>".print_r( $_POST, 1 )."</pre>";
    if( !empty($_POST ) && $_POST['wwc-save-form']=='Save'){
        if( !empty($_POST['wwc_aff_signed']) ){
            update_option('wwc_visibility_status_logged[wwc_aff_signed]',$_POST["wwc_aff_signed"]);
            $status_signed = 'success';
        }
        
        if( !empty($_POST["wwc_aff_display"])){
            update_option('wwc_visibility_user_status',$_POST["wwc_aff_display"]);
            $status_display = 'success';
        }

        if( $status_signed =='success' || $status_display = 'success'){
            echo '<div class="notice notice-success is-dismissible">
                <p>Settings Saved</p>
            </div>';
        }
    }



    $logged_status  = get_option('wwc_visibility_status_logged[wwc_aff_signed]') ;
    // echo "<pre>".print_r($logged_status,1)."</pre>";

    // Status for logged in
    $signed_in = ''; 
    if( $logged_status[in] == 'on' ){
        $signed_in = 'checked';
    }else{
        $signed_in = '';
    }

    $admin_status = '';
    if( $logged_status[in] == 'on' && $logged_status[admin] =='on'){
        $admin_status = 'checked';
    }else{
        $admin_status = '';
    }

    // Signed out Status
    $signed_out = ''; 
    if( $logged_status[out] == 'on' ){
        $signed_out = 'checked';
    }else{
        $signed_out = '';
    }

    // End


    // Status for User 

    $user_status  = get_option('wwc_visibility_user_status') ;
    // Status for logged in
    $user_all = ''; 
    if( $user_status[all] == 'on' ){
        $user_all = 'checked';
    }else{
        $user_all = '';
    }

    // Signed out Status
    $user_current = ''; 
    if( $user_status[current] == 'on' ){
        $user_current = 'checked';
    }else{
        $user_current = '';
    }
    // Ends

    // reset data
    if( !empty( $_POST ) && $_POST['wwc-reset-form'] == 'Reset'){
        update_option('wwc_visibility_user_status','');
        update_option('wwc_visibility_status_logged[wwc_aff_signed]','');
    }
    ?>
    <div class="wwc-wrapper">
        <div class="wwc-row">
            <div class="wwc-col">
                
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" id="wwc-settings-form">
                    <p class="wwc-setting-title">Data to View</p>
                    <div>
                        <input type="checkbox" id="wwc-show-all" name="wwc_aff_display[all]" <?php echo $user_all; ?>>
                        <label for="wwc-show-all">Show all User data</label>
                
                    </div>
                    <div>
                        <input id="wwc-show-current" type="checkbox" name="wwc_aff_display[current]" <?php echo $user_current; ?>>
                        <label for="wwc-show-current">Show only current user</label>
                    </div>

                    
                    <p class="wwc-setting-title">Visible to logged in or Logged out users</p>
                    <div>
                        <input id="wwc-signed-in" type="checkbox" name="wwc_aff_signed[in]" <?php echo $signed_in; ?>> 
                        <label for="wwc-signed-in">Logged in</label>
                            <div id="show-logged-in-menu" style="display: none;">
                               
                                <div>
                                    <input id="wwc-admin-only" type="checkbox" name="wwc_aff_signed[admin]" <?php echo $admin_status; ?>> 
                                    <label for="wwc-admin-only">Admin Only</label>
                                </div>

                            </div>
                        <div>
                            <input id="wwc-signedout" type="checkbox" name="wwc_aff_signed[out]" <?php echo $signed_out; ?>> 
                            <label for="wwc-signedout">Logged Out</label>

                        </div>
                        
                    </div>                   
                      
                    <div class="wwc-button-wrapper">
                        <input type="submit" class="wp-primary-button" value="Save" name="wwc-save-form">
                        <input type="submit" class="wwwc-button" value="Reset" name="wwc-reset-form">
                    </div>   

                                     

                </form>
            </div>    
        </div>

    </div>

    <?php

}


function wwc_dashboard_shortcode_callback(){

    // get user status
    $user = get_users();
    
    
    global $wpdb;
    $content = '';
    // if( $logged_status['in'] == 'on' ):


        $content .= 
            '<div class="wwc-front-wrapper">
                <div class="wwc-front-col">';

        $affiliate_table = $wpdb->prefix.'affiliate_wp_affiliates'; 

        $results = $wpdb->get_results( "SELECT * FROM $affiliate_table", ARRAY_A );
        // echo "<pre>".print_r($results,1)."</pre>";
        $content .='<table class="wwc-aff-table" id="wwc-aff-table">
                
                <thead >
                    <tr>
                        <th>Data</th>
                        
                    </tr>
                </thead>
                <tbody>';
        foreach( $results as $key => $value ){
            $user = get_user_by( 'id', $value['user_id'] ); 
            $content .='<tr>
                        <td>
                            <p class="wwc-aff-id">Affiliate ID : '.$value["affiliate_id"].'</p>
                            <ul style="list-style-type:none;" class="wwc-user-data">
                                <li>User Data 
                                    <ul>
                                        <li style="list-style-type:none;"> User ID : '.$value["user_id"].'</li>
                                        <li style="list-style-type:none;"> User Name : '.$user->user_login.'</li>
                                    </ul>
                                </li>
                            </ul>
                            
                            <p class="aff-status">Status : '.$value["status"].'</p>
                            <p class="donate-button view-aff"><a href="/view-affiliate?aff_id='.$value["affiliate_id"].'&user_id='.$value["user_id"].'">View</a></p>
                        </td>
                    </tr>
                ';
        }


        $content .='    </tbody>
                    </table>
                </div>
            </div>';

        // endif;
        return $content;
}


// View Single Affiliate Data

function wwc_view_shortcode_callback( $atts ){

    // Pull ID shortcode attributes.
    $atts = shortcode_atts(
        [
            'id'     => '',
            'user'   => '',
            
        ],
        $atts
    );

    if( empty( $_GET['user_id']) ){
        $content = '';
        $content .='<p class="no-entries">You are not allowed to view this page</p>';
        return $content;
    }
    // Check for an ID attribute (required) and that WPForms is in fact
     $user_id = $_GET['user_id'];
    // installed and activated.
    if ( empty( $atts['id'] ) || ! function_exists( 'wpforms' ) ) {
        return;
    }

    // Get the form, from the ID provided in the shortcode.
    $form = wpforms()->form->get( absint( $atts['id'] ) );

    // If the form doesn't exists, abort.
    if ( empty( $form ) ) {
        return;
    }


    $form_data = ! empty( $form->post_content ) ? wpforms_decode( $form->post_content ) : '';
    // echo "<pre>".print_r( $form_data, 1 )."</pre>";

    // Setup the form fields.
    if ( empty( $form_field_ids ) ) {
        $form_fields = $form_data['fields'];
    } else {
        $form_fields = [];
        foreach ( $form_field_ids as $field_id ) {
            if ( isset( $form_data['fields'][ $field_id ] ) ) {
                $form_fields[ $field_id ] = $form_data['fields'][ $field_id ];
            }
        }
    }

    // Here we define what the types of form fields we do NOT want to include,
    // instead they should be ignored entirely.
    $form_fields_disallow = apply_filters( 'wpforms_frontend_entries_table_disallow', [ 'divider', 'html', 'pagebreak', 'captcha' ] );

    // Loop through all form fields and remove any field types not allowed.
    foreach ( $form_fields as $field_id => $form_field ) {
        if ( in_array( $form_field['type'], $form_fields_disallow, true ) ) {
            unset( $form_fields[ $field_id ] );
        }
    }

    $entries_args = [
        'form_id' => absint( $atts['id'] ),
        'number'    => '1',
        'order_by' =>'entry_id',
        'order'     => 'DESC'
    ];

    // Narrow entries by user if user_id shortcode attribute was used.
    $entries_args['user_id'] = $_GET['user_id'];

    // Get all entries for the form, according to arguments defined.
    // There are many options available to query entries. To see more, check out
    // the get_entries() function inside class-entry.php (https://a.cl.ly/bLuGnkGx).
    $entries = wpforms()->entry->get_entries( $entries_args );

    // echo "<pre>".print_r( $entries, 1 )."</pre>";
    if ( empty( $entries ) ) {
        $content = '';
        $content .='<div class="wwc-wrapper"><p class="no-entries" text-align="center">No Data found for this user.</p></div>';
        return $content;
    }

      // Get affiliate ID to get the Affiliate URL
    $affiliate_id = affwp_get_affiliate_id( $user_id );
    $affiliate_url = affwp_get_affiliate_referral_url( array( 'affiliate_id' => $affiliate_id ) ) ;


    $content = '';

    $content .= '<div class="wwc-wrapper">';
    foreach( $entries as $entry ){
        $entry_fields = json_decode( $entry->fields, true );
            // echo "<pre>".print_r( $entry_fields , 1 )."</pre>";
        $goal_amount = $entry_fields[26]['value'];
            $content .= '
                <div class="wwc-post-section">
                    <div class="wwc-row">
                        <div class="col">
                            <div class="wwc-company-logo">';
                            $content .='<a href="/fundraiser-update-form/?user_id='.$entries_args['user_id'].'&edit=true" style="background-color: #2b539c;
    color: rgba(0,0,0,.6);padding:10px;border-radius: 25px;color:white;font-weight:bold">Edit Entry</a>';


                        $content .='</div></div>
                    </div>
                    <div class="wwc-row">
                        <div class="col">
                              <div class="wwc-company-logo">';
                                if( !empty( $entry_fields[1]['value'] ) ){
                                    $content .='<img src="'.$entry_fields[1]['value'].'" alt="">';
                                }
                              $content .='</div>
                        </div>
                        <div class="col">
                            <div class="wwc-page-title" style="font-size:50px;">';
                                if( !empty($entry_fields[18]['value'] ) ){
                                    $content .='<h2>'.$entry_fields[18]['value'].'</h2>';
                                }
                            $content .='</div>
                        </div>

                        <div class="col info-area">
                            <div class="wwc-page-title" style="font-size:50px;">';
                                if( !empty($entry_fields[3]['value'] ) ){
                                    $content .='<h2>'.$entry_fields[3]['value'].'</h2>';
                                }
                            $content .='</div>';
                            $content.='<div class="sf-progress-bar">';
                                // Fixed the ultimeter going out of the page
                                $ultimeter = do_shortcode('[ultimeter id="50231"]'); 
                                $content .= $ultimeter;
                                $content.='</div> 
                                <div class="wwc-buttons">

                                    <a style"text-align:center" class="share-button-cf" href="'.$affiliate_url.'">SHARE</a>                                
                                    <a style"text-align:center" class="share-button-cf" href="/donate/">Donate Now</a><br>
                                </div>
                        </div>
                    </div>

                    <div class="wwc-row">
                        <div class="col" id="company-auv">
                            <div class="wwc-company-auv">';
                                
                            if( !empty($entry_fields[4]['value_raw'][0] ) ){
                                if( $entry_fields[4]['value_raw'][0][type] == 'image/jpeg' || $entry_fields[4]['value_raw'][0][type] == 'image/jpg'){
                                    $content .='<img src="'.$entry_fields[4]['value_raw'][0][value].'" alt="">';
                                }

                                if( $entry_fields[4]['value_raw'][0][type] == 'video/mp4' 
                                    || $entry_fields[4]['value_raw'][0][type] == 'video/webm'
                                    || $entry_fields[4]['value_raw'][0][type] == 'video/avi'
                                    || $entry_fields[4]['value_raw'][0][type] == 'video/x-ms-wmv'
                                    || $entry_fields[4]['value_raw'][0][type] == 'video/quicktime'

                                ){
                                    $content .='<video width="800px" height="450px" controls> 
                                                    <source src="'.$entry_fields[4]['value_raw'][0][value].'" type="video/mp4"> 
                                                </video>';
                                }

                            }
                        $content .='
                            </div>
                        </div>
                        <div class="col">
                            <div class="wwc-company-description">';
                                if( !empty( $entry_fields[5]['value'] )  ){
                                    $content .='<p>'.$entry_fields[5]["value"].'</p>';
                                }
                                
                            $content .='</div>
                            
                        </div>
                    </div>
                    <div class="wwc-row">

                        <div class="no-class">
                            <div class="wwc-extra-description">';
                                if( !empty( $entry_fields[6]['value'] )  ){
                                    $content .='<p>'.$entry_fields[6]["value"].'</p>';
                                }
                            $content .='</div>
                        </div>
                    </div>
                </div>

            ';
    }

    $content .='</div>';
    return $content;
}


add_action( 'wpforms_process_complete','wwc_affiliate_register_user', 10, 4 );

function wwc_affiliate_register_user( $fields, $entry, $form_data, $entry_id ){

    // if user is logged in then update data
    if( is_user_logged_in() && absint( $form_data['id'] ) == 50414 ){

        /**
         * Get the entries of update form
         */

        $user_id = $_GET['user_id'];
        $entry = wpforms()->entry->get( $entry_id );
 
        // Fields are in JSON, so we decode to an array
        $entry_fields = $entry->fields;

        // get target form
        $entries_args = [
            'form_id' => absint(49926),
            'user_id' => $user_id
        ];

        // Get the last entry id 
        $entries = wpforms()->entry->get_entries( $entries_args );

        // get the last entry id
        $last_array = end($entries);
        $target_entry_id = $last_array->entry_id;

        // update target entry
        wpforms()->entry->update( $target_entry_id, array( 'fields' => $entry_fields,'user_id' => $user_id ), '', '', array( 'cap' => false ) );

        // delete current entry
        wpforms()->entry->delete( $entry_id, [ 'cap' => false ] );


    }

    if(  absint( $form_data['id'] ) == 49926 ) {
        $entry = wpforms()->entry->get( $entry_id );
 
        // Fields are in JSON, so we decode to an array
        $entry_fields = json_decode( $entry->fields, true );

        
        $user_name = $entry_fields[11]['value'];
        $user_email = $entry_fields[7]['value'];


        $password = $entry_fields[8]['value']; //pssword

        $first_name = $entry_fields[2]['value']; 

        if( !empty( $user_name ) ){
            $user_details = array(
                'user_login'        => sanitize_user( $user_name ),
                'user_pass'         => $password,
                'user_email'        => sanitize_text_field( $user_email ),
                'first_name'        => $user_name,
                'last_name'         => '',
                'user_registered'   => date('Y-m-d H:i:s'),
                'role'              => 'subscriber'
            );

            $user_id = wp_insert_user( 
                            $user_details
            );

            // if( is_wp_error( $user_id ) ){
                
            // }

            update_user_meta( $user_id, 'affwp_referral_notifications', true );

            if ( affiliate_wp()->settings->get( 'require_approval' ) ) {
                $status = 'pending';
            } else {
                $status = 'active';
            }

            $settings  = get_option( 'affwp_settings' );
            $rate_type = ! empty( $settings['referral_rate_type'] ) ? $settings['referral_rate_type'] : null;
            $rate      = isset( $settings['referral_rate'] ) ? $settings['referral_rate'] : 20;

            $args = array(
                'user_id'          => $user_id,
                'status'           => $status,
                'rate'             => $rate,
                'rate_type'        => $rate_type,
                'flat_rate_basis'  => '',
                'payment_email'    => '',
                'notes'            => '',
                'website_url'      => '',
                'date_registered'  => date('Y-m-d H:i:s'),
                'dynamic_coupon'   => '',
            );

            $affiliate_id = affiliate_wp()->affiliates->add( $args );

            if ( $affiliate_id ) {

                affwp_set_affiliate_status( $affiliate_id, $status );
            }    

            $entry_fields = json_encode( $entry_fields );
 
            // Save changes
            wpforms()->entry->update( $entry_id, array( 'user_id' => $user_id ), '', '', array( 'cap' => false ) );
            
        }
    }

    
}


add_action('wp_footer','wwc_add_fundraiser_scripts');
function wwc_add_fundraiser_scripts(){

    if( !empty( $_GET )){

        $user_id = $_GET['user_id'];
        $edit = $_GET['edit'];

        if( $edit == 'true'){

            $atts = array(
                'id'     => '49926',
                'user'   => $user_id,
            );


            // Get form entries
            if ( empty( $atts['id'] ) || ! function_exists( 'wpforms' ) ) {
                return;
            }

            // Get the form, from the ID provided in the shortcode.
            $form = wpforms()->form->get( absint( $atts['id'] ) );

            // If the form doesn't exists, abort.
            if ( empty( $form ) ) {
                return;
            }


            $form_data = ! empty( $form->post_content ) ? wpforms_decode( $form->post_content ) : '';
            // echo "<pre>".print_r( $form_data, 1 )."</pre>";

            // Setup the form fields.
            if ( empty( $form_field_ids ) ) {
                $form_fields = $form_data['fields'];
            } else {
                $form_fields = [];
                foreach ( $form_field_ids as $field_id ) {
                    if ( isset( $form_data['fields'][ $field_id ] ) ) {
                        $form_fields[ $field_id ] = $form_data['fields'][ $field_id ];
                    }
                }
            }

            // Here we define what the types of form fields we do NOT want to include,
            // instead they should be ignored entirely.
            $form_fields_disallow = apply_filters( 'wpforms_frontend_entries_table_disallow', [ 'divider', 'html', 'pagebreak', 'captcha' ] );

            // Loop through all form fields and remove any field types not allowed.
            foreach ( $form_fields as $field_id => $form_field ) {
                if ( in_array( $form_field['type'], $form_fields_disallow, true ) ) {
                    unset( $form_fields[ $field_id ] );
                }
            }

            $entries_args = [
                'form_id' => absint( $atts['id'] ),
                'number'    => '1',
                'order_by' =>'entry_id',
                'order'     => 'DESC'
            ];

            // Narrow entries by user if user_id shortcode attribute was used.
            $entries_args['user_id'] = $_GET['user_id'];

            // Get all entries for the form, according to arguments defined.
            // There are many options available to query entries. To see more, check out
            // the get_entries() function inside class-entry.php (https://a.cl.ly/bLuGnkGx).
            $entries = wpforms()->entry->get_entries( $entries_args );

            //start custom fundraiser page wrapper. This design is supposed to have the look of gofundme
            foreach( $entries as $entry ){
                $entry_fields = json_decode( $entry->fields, true );
                
                // echo "<pre>".print_r($entry_fields, 1)."</pre>";
                // Get the values from the entry
                $username               = $entry_fields[11]['value'];
                $org_address_1          = $entry_fields[12]['address1'];
                $org_address_city       = $entry_fields[12]['city'];
                $org_address_state      = $entry_fields[12]['state'];
                $goal_amount            = $entry_fields[13]['value'];
                $fund_rec_org_name      = $entry_fields[16]['value'];
                $fund_rec_org_address1  = $entry_fields[17]['address1'];
                $fund_rec_org_city      = $entry_fields[17]['city'];
                $fund_rec_org_state     = $entry_fields[17]['state'];

                $campaign_title         = $entry_fields[18]['value'];
                $campaign_description   = $entry_fields[19]['value'];
                $funding_category       = $entry_fields[20]['value'];

                $email    = $entry_fields[7]['value'];
                $company_name    = $entry_fields[2]['value'];
                $slogan    = $entry_fields[3]['value'];
                $company_interest    = $entry_fields[5]['value'];
                $company_description    = $entry_fields[6]['value'];
                // Now populate the fields of the form
                $hidden = $entry_fields[12]['value'];

                $commission = $entry_fields[21]['value'];
                $partner_commission = $entry_fields[22]['value'];
                ?>

                <script>
                    
                    jQuery(document).ready( function(){

                        // jQuery('input[name="wpforms[fields][8][primary]"]').hide();
                        // jQuery('#wpforms-49926-field_8-container').hide();
                        // jQuery('input[name="wpforms[fields][8][secondary]"]').hide();
                        jQuery('#wpforms-49926-field_10').hide();

                        
                        var username = '<?php echo $username; ?>';
                        var org_address_1 = '<?php echo $org_address_1; ?>';
                        var org_address_city = '<?php echo $org_address_city; ?>';
                        var org_address_state = '<?php echo $org_address_state; ?>';
                        var goal_amount = '<?php echo $goal_amount; ?>';
                        var fund_rec_org_name = '<?php echo $fund_rec_org_name; ?>';
                        var fund_rec_org_address1 = '<?php echo $fund_rec_org_address1; ?>';
                        var fund_rec_org_city = '<?php echo $fund_rec_org_city; ?>';
                        var fund_rec_org_state = '<?php echo $fund_rec_org_state; ?>';
                        var campaign_title = '<?php echo $campaign_title; ?>';
                        var campaign_description = '<?php echo $campaign_description; ?>';

                        var email = '<?php echo $email; ?>';
                        var company_name = '<?php echo $company_name; ?>';
                        var slogan = '<?php echo $slogan; ?>';
                        var company_interest = '<?php echo $company_interest; ?>';
                        var company_description = '<?php echo $company_description; ?>';
                        var funding_category    = '<?php echo $funding_category; ?>';

                         var commission    = '<?php echo $commission; ?>';

                         var partner_commission    = '<?php echo $partner_commission; ?>';


                        jQuery('input[name="wpforms[fields][11]"]').val(username);
                        jQuery('input[name="wpforms[fields][12][address1]"]').val(org_address_1);
                        jQuery('input[name="wpforms[fields][12][city]"]').val(org_address_city);
                        jQuery('select[name="wpforms[fields][12][state]"] option[value="'+org_address_state+'"]').attr('selected','selected');
                        jQuery('input[name="wpforms[fields][13]"]').val(goal_amount);
                        jQuery('input[name="wpforms[fields][16]"]').val(fund_rec_org_name);
                        jQuery('input[name="wpforms[fields][17][address1]"]').val(fund_rec_org_address1);
                        jQuery('input[name="wpforms[fields][17][city]"]').val(fund_rec_org_city);
                        jQuery('select[name="wpforms[fields][17][state]"] option[value="'+fund_rec_org_state+'"]').attr('selected','selected');
                        jQuery('input[name="wpforms[fields][18]"]').val(campaign_title);
                        jQuery('textarea[name="wpforms[fields][19]"]').val(campaign_description);

                        jQuery('input[name="wpforms[fields][21]"]').val(commission);

                        jQuery('input[name="wpforms[fields][22]"]').val(partner_commission);

                        jQuery('input[name="wpforms[fields][11]"]').attr('disabled','disabled');

                        jQuery('select[name="wpforms[fields][20]"] option[value="'+funding_category+'"]').attr('selected','selected');



                        jQuery('input[name="wpforms[fields][7]"]').val(email);

                        jQuery('input[name="wpforms[fields][7]"]').attr('disabled','disabled');

                        jQuery('input[name="wpforms[fields][2]"]').val(company_name);
                        jQuery('input[name="wpforms[fields][3]"]').val(slogan);
                        jQuery('textarea[name="wpforms[fields][5]"]').val(company_interest);
                        jQuery('textarea[name="wpforms[fields][6]"]').val(company_description);

                        

                    })




                </script>
                <?php
            }
        }
    }
 
}


// Render Ultimeter progressbar
add_action('wp_footer','wwc_render_progress_bar');
function wwc_render_progress_bar(){

    $user_id = $_GET['user_id'];


    $atts = array(
        'id'     => '49926',
        'user'   => $user_id,
    );


    // Get form entries
    if ( empty( $atts['id'] ) || ! function_exists( 'wpforms' ) ) {
        return;
    }

    // Get the form, from the ID provided in the shortcode.
    $form = wpforms()->form->get( absint( $atts['id'] ) );

    // If the form doesn't exists, abort.
    if ( empty( $form ) ) {
        return;
    }


    $form_data = ! empty( $form->post_content ) ? wpforms_decode( $form->post_content ) : '';
    // echo "<pre>".print_r( $form_data, 1 )."</pre>";

    // Setup the form fields.
    if ( empty( $form_field_ids ) ) {
        $form_fields = $form_data['fields'];
    } else {
        $form_fields = [];
        foreach ( $form_field_ids as $field_id ) {
            if ( isset( $form_data['fields'][ $field_id ] ) ) {
                $form_fields[ $field_id ] = $form_data['fields'][ $field_id ];
            }
        }
    }

    // Here we define what the types of form fields we do NOT want to include,
    // instead they should be ignored entirely.
    $form_fields_disallow = apply_filters( 'wpforms_frontend_entries_table_disallow', [ 'divider', 'html', 'pagebreak', 'captcha' ] );

    // Loop through all form fields and remove any field types not allowed.
    foreach ( $form_fields as $field_id => $form_field ) {
        if ( in_array( $form_field['type'], $form_fields_disallow, true ) ) {
            unset( $form_fields[ $field_id ] );
        }
    }

    $entries_args = [
        'form_id' => absint( $atts['id'] ),
        'number'    => '1',
        'order_by' =>'entry_id',
        'order'     => 'DESC'
    ];

    $user_id = $_GET['user_id'];
    // Narrow entries by user if user_id shortcode attribute was used.
    $entries_args['user_id'] = $_GET['user_id'];

    // Get all entries for the form, according to arguments defined.
    // There are many options available to query entries. To see more, check out
    // the get_entries() function inside class-entry.php (https://a.cl.ly/bLuGnkGx).
    $entries = wpforms()->entry->get_entries( $entries_args );

    $affiliate_id = affwp_get_affiliate_id( $user_id );
    $earnings = affwp_get_affiliate_earnings( $affiliate_id );

    //start custom fundraiser page wrapper. This design is supposed to have the look of gofundme
    foreach( $entries as $entry ){
        $entry_fields = json_decode( $entry->fields, true );
        
        // echo "<pre>".print_r($entry_fields, 1)."</pre>";
        // Get the values from the entry
       
        $goal_amount            = $entry_fields[13]['value'];

        $ultimeter_post = get_post(50231);
        $ul_goal_amount = get_post_meta(50231,'_ultimeter_goal_amount',true );

        ?>
        <script>
            $(document).ready( function(){

                // render Progress on ultimeter

                var progress_goal = '<?php echo $earnings; ?>'; 
                
                var goal_by_user = '<?php echo $goal_amount; ?>';
                // var goal_by_user = 30;


                var progress_percent = (goal_by_user/progress_goal)*100;
          
                $('#post-50231 .ultimeter_meter_goal .ultimeter_meter_amount').html('$'+goal_by_user);
                $('#post-50231 .ultimeter_meter_progress .ultimeter_meter_amount').html('$'+progress_goal);

                $('#post-50231 .ultimeter_meter .ultimeter_meter_outer .ultimeter_progressbar_progress').css('width',progress_percent+' !important');

            })



        </script>

        <?php
    }
}
/*
 * Remove all user entries before saving the entry - this is for all forms.
 * Always get the latest entries of the users
 * @link https://wpforms.com/developers/how-to-overwrite-entries-from-users-who-have-already-submitted-a-form/
 *
 */
 
function remove_all_before_entry_save( $fields, $entry, $form_id ) {
 
        //get the current user's user ID
    $user_id = get_current_user_id();
 
        //check if the user is logged in, if not logged in skip this code snippet all together
    if ( empty( $user_id ) ) {
        return;
    }
 
        //perform a query for any entries submitted on this form by this user ID
    $entries = wpforms()->entry->get_entries(
        [
            'form_id' => 49926,
            'user_id' => $user_id,
            'number'  => -1,
            'select'  => 'entry_ids',
            'cap'     => false,
        ]
    );
 
        //for any previous entries this user has submitted on this form, remove them and replace them with this entry only 
    foreach ( $entries as $_entry ) {
        wpforms()->entry->delete( $_entry->entry_id, [ 'cap' => false ] );
    }
}
add_action( 'wpforms_process_entry_save', 'remove_all_before_entry_save', 9, 3 );


/**
 * Ends
 */