<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class WPSF_Contact_Form {
        
        /**
         * Static private variable to hold instance this class
         * @var type 
         */
        private static $instance;

        private function __construct() {
                                               
          add_shortcode( 'wpsf-contact-form', array($this, 'wpsf_contact_form_render' ));              
          add_action( 'admin_post_wpsf_contact_form', array($this, 'wpsf_save_contact_form_data') );
          add_action( 'admin_post_nopriv_wpsf_contact_form', array($this, 'wpsf_save_contact_form_data') );  
          
          add_action( 'wp_ajax_wpsf_contact_form', array($this, 'wpsf_save_contact_form_data') );
          add_action( 'wp_ajax_nopriv_wpsf_contact_form', array($this, 'wpsf_save_contact_form_data') );  
        
          add_action( 'init', array($this, 'wpsf_create_wp_super_post_type'));

                                 
        }
       
        /**
         * Return the unique instance 
         * @return type instance
         * @since version 1.9.18
         */
        public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
        }
        
        public function wpsf_create_wp_super_post_type(){
                        
            $wpsuperform = array(
                    'labels' => array(
                        'name'              => esc_html__( 'WP Super Form', 'wp-super-form' ),
                        'singular_name'     => esc_html__( 'WP Super Form', 'wp-super-form' ),
                        'add_new' 	    => esc_html__( 'Contact', 'wp-super-form' ),
                        'add_new_item'      => '',
                        'edit_item'         => esc_html__( 'Edit Contact','wp-super-form'),           
                        'all_items'         => esc_html__( 'Contacts', 'wp-super-form' ),  

                   ),
                  'public'                => true,
                  'has_archive'           => false,
                  'exclude_from_search'   => true,
                  'publicly_queryable'    => false,
                  'show_in_admin_bar'     => false,
                  'supports'              => array('title'),  
                  'menu_position'         => 100
          
                );
            
                register_post_type( 'wp-super-form', $wpsuperform);
            
        }
        
        public function wpsf_save_contact_form_data(){
            
            $form_data = $_POST;                             
            $rv_link   = $form_data['wpsf_contact_link'];
                
            if(!wp_verify_nonce($form_data['wpsf_contact_nonce'], 'wpsf_contact')){
                wp_redirect( $rv_link );
                exit; 
            }
            
            if($form_data){
                
                $contact = array(                  
                    'post_title'            => sanitize_text_field($form_data['wpsf_contact_name']),
                    'post_name'             => sanitize_text_field($form_data['wpsf_contact_name']),
                    'post_type'             => 'wp-super-form',                                        
                                        
                );
                
                $post_id    = wp_insert_post($contact);
                
                if($post_id){
                    
                    update_post_meta($post_id, 'wpsf_contact_name', sanitize_text_field($form_data['wpsf_contact_name']));
                    update_post_meta($post_id, 'wpsf_contact_email', sanitize_text_field($form_data['wpsf_contact_email']));
                    update_post_meta($post_id, 'wpsf_contact_message', sanitize_textarea_field($form_data['wpsf_contact_message']));
                    
                    wp_redirect( $rv_link );
                    exit;
                    
                }
            }else{
                
                wp_redirect( $rv_link );
                exit;
            }
                        
        }
                        
        public function wpsf_contact_form_render($attr){
                                    
            ob_start();
            
            global $post;
            global $wp;
                       
            wp_enqueue_script( 'wpsf-contact-form-js', WPSF_PLUGIN_URL . 'public/assets/js/contact.js', array('jquery', 'jquery-ui-core'), WPSF_VERSION );
            wp_enqueue_style(  'wpsf-contact-form-css', WPSF_PLUGIN_URL . 'public/assets/css/contact.css', false, WPSF_VERSION );
                        
            $form = $current_url = '';
            
            if(is_object($wp)){
                $current_url = home_url( add_query_arg( array(), $wp->request ) );
            }
            
            $form   .= '<div class="wpsf-contact-container">';
                            
            $form   .= '<form action="'.esc_url( admin_url('admin-post.php') ).'" method="post" class="wpsf-contact-submission">';
                    
            $form   .= wp_nonce_field( 'wpsf_contact', 'wpsf_contact_nonce' )

                    . '<div class="wpsf-form-tbl">'
                    
                    . '<div class="wpsf-form-fld">'
                    .   '<span>'.esc_html__('Name', 'wp-super-form').'</span>'
                    .   '<input type="text" name="wpsf_contact_name" required>'
                    . '</div>'
                    . '<div class="wpsf-form-fld">'
                    .   '<span>'.esc_html__('Email', 'wp-super-form').'</span>'
                    .   '<input type="text" name="wpsf_contact_email" required>'
                    . '</div>'

                    . '<div class="wpsf-form-fld">'
                    .   '<span>'.esc_html__('Message', 'wp-super-form').'</span>'
                    .   '<textarea name="wpsf_contact_message"></textarea>'
                    . '</div>'

                    . '<input type="hidden" name="wpsf_contact_link" value="'.esc_url($current_url).'">'                    
                    . '<input type="hidden" name="wpsf_place_id" value="'.esc_attr($post->ID).'">'
                    . '<input type="hidden" name="action" value="wpsf_contact_form">'
                    . '<input name="wpsf-contact-save" type="submit" class="submit">'                                        
                    . '</div>'
                    . '</form>'
                    . '</div>';
            
            
             echo $form;
             return ob_get_clean();
            
        }
            
}

if ( class_exists( 'WPSF_Contact_Form') ) {
	WPSF_Contact_Form::get_instance();
}