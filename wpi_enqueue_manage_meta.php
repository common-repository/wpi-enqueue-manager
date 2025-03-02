<?php
class wpiEnqueueManageMeta{
    var $wpi_scripts_ignore_list;
    var $wpi_styles_ignore_list;
    public function __construct(){
        add_action( 'add_meta_boxes', array($this,'wpi_enqueue_manage_add_meta') );
        add_action( 'save_post', array($this,'wpi_enqueue_manage_save_meta') );
    }
    
    
    public function wpi_enqueue_manage_add_meta()
    {
        add_meta_box( 'wpi_enqueue_manage', 'WPI Scripts Enqueue Manager', array($this,'call_to_wpi_enqueue'), 'page', 'normal', 'high' );
        add_meta_box( 'wpi_enqueue_manage', 'WPI Scripts Enqueue Manager', array($this,'call_to_wpi_enqueue'), 'post', 'normal', 'high' );
    }
    
    public function call_to_wpi_enqueue($post)
    {
        // $post is already set, and contains an object: the WordPress post
        global $post;
        $values = get_post_custom( $post->ID );
        $wpi_scripts = isset( $values['wpi_selective_scripts_dequeue'][0] ) ? esc_attr( $values['wpi_selective_scripts_dequeue'][0] ) : '';
        $wpi_styles = isset( $values['wpi_selective_styles_deregister'][0] ) ? esc_attr( $values['wpi_selective_styles_deregister'][0] ) : '';
        // We'll use this nonce field later on when saving.
        wp_nonce_field( 'my_meta_box_nonce', 'meta_box_nonce' );
        ?>



        <p>
            <label for="wpi_scripts_dequeue">Scripts To Dequeue (bar [|] separated)</label>
            <input type="text" id="wpi_scripts_dequeue" name="wpi_scripts_dequeue" size="70" value="<?php echo $wpi_scripts; ?>" />
        </p>
        


        <p>
            <label for="wpi_styles_deregister">Styles to Deregister (bar [|] separated)</label>
            <input type="text" id="wpi_styles_deregister" name="wpi_styles_deregister" size="70" value="<?php echo $wpi_styles; ?>" />
        </p>
		
		<p style="text-align:right"><a href="http://phpwpinfo.com/wordpress-plugin-allows-to-selectively-dequeue-scriptsstyles-on-each-post/" target="_blank">Refer to the Documentation on WPIndex</a></p>

        <?php   
    }
    
   function wpi_print_scripts() {
        global $wp_scripts;
       // echo "<pre>";var_dump($wp_scripts);echo "</pre>";
        foreach( $wp_scripts->queue as $handle1 ) :
            $script_handles[]=$handle1;
        endforeach;
        
        return $script_handles;
    }
    
    
    function wpi_print_styles() {
        global $wp_styles;
        
        //echo "<pre>";var_dump( $wp_styles ); echo "</pre>";
        foreach( $wp_styles->queue as $handle2 ) :
            $style_handles[]=$handle2;
        endforeach;
        
        return $style_handles;
    }
    
    function wpi_enqueue_manage_save_meta( $post_id )
    {
       // Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // if our nonce isn't there, or we can't verify it, bail
        if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return;

        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post' ) ) return;


        if( isset( $_POST['wpi_scripts_dequeue'] ) )
            update_post_meta( $post_id, 'wpi_selective_scripts_dequeue', esc_attr( $_POST['wpi_scripts_dequeue'] ) );
        
       

       if( isset( $_POST['wpi_styles_deregister'] ) )
            update_post_meta( $post_id, 'wpi_selective_styles_deregister', esc_attr( $_POST['wpi_styles_deregister'] ) );
       


    }
}
?>
