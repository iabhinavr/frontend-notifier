<?php

 class Frontend_Notifier {

    public $update_data;
    public $slug_list;

    public $current;

    public function __construct() {
        $update_data_json = file_get_contents( FN_PLUGIN_DIR . 'data/update_data.json' );
        if(empty($update_data_json)) {
            $this->update_data = [
                "new_posts" => [],
                "updated_posts" => []
            ];
        }
        else {
            $this->update_data = json_decode($update_data_json, true);
        }

        $slug_list_json = file_get_contents( FN_PLUGIN_DIR . 'data/slug_list.json' );

        if(empty($slug_list_json)) {
            $this->slug_list = [
                "slugs" => []
            ];
        }
        else {
            $this->slug_list = json_decode($slug_list_json, true);
        }

        $this->get_current();
  
    }

    public function get_current() {
        if(!empty($this->update_data["new_posts"])) {
            foreach ($this->update_data["new_posts"] as $current) {
                $this->current = $current;
                break;
            }
        }
        else if(!empty($this->update_data["updated_posts"])) {
            foreach ($this->update_data["updated_posts"] as $current) {
                $this->current = $current;
                break;
            }
        }
        else {
            $this->current = [];
        }
    }

    public function init() {
        add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
        add_action( 'admin_post_send_notification', array( $this, 'send_notification' ) );
        add_action( 'admin_post_fetch_post_slugs', array( $this, 'load_slugs' ) );
        add_action( 'admin_post_revalidate_home', array( $this, 'revalidate_home' ) );
        add_action( 'save_post', array( $this, 'save_update_data' ), 10, 3 );
    }

    public function add_menu_page() {

        add_menu_page(
            'Frontend Notifier Settings Page', 
            'Frontend Notifier', 'manage_options', 
            'frontend-notifier', 
            array($this, 'render'), 
            'dashicons-admin-site-alt', 
            100 
        );
    }

    public function render() {
        ob_start();
        include_once( 'views/settings.php' );
        $output = ob_get_clean();
        echo $output;
    }

    public function save() {

        $this->redirect();
    }

    public function send_notification() {

        if( !$this->has_valid_nonce('send_notification', 'notification_nonce')) {
            $this->redirect();
        }

        if(empty($this->current)) {
            $this->redirect();
        }

        $data = [
            'secret'    => FN_SECRET_KEY,
            'ID'        => $this->current['ID'],
            'slug'      => $this->current['post_name'],
            'type'      => $this->current['post_type']
        ];

        $query_string = http_build_query($data);

        $url = FN_URL . '?' . $query_string;

        $response = wp_remote_get($url);
        $body_json = wp_remote_retrieve_body($response);

        file_put_contents(FN_PLUGIN_DIR . 'data/response_body.json', json_encode($response));

        $body = json_decode($body_json);

        if(isset($body->revalidated) && $body->revalidated) {
            unset($this->update_data['new_posts'][$this->current['ID']]);
            unset($this->update_data['updated_posts'][$this->current['ID']]);
        }

        file_put_contents( FN_PLUGIN_DIR . 'data/update_data.json', json_encode($this->update_data));

        $this->redirect();

    }

    public function load_slugs() {

        if( $this->has_valid_nonce('fetch_post_slugs', 'slug_nonce')) {
            $args = array(
                'post_type' => 'post',
                'status'    => 'publish',
                'posts_per_page' => -1,
                'orderby'   => 'date',
                'order'     => 'DESC'
            );
    
            $query = new WP_Query( $args );

            $posts = $query->posts;

            $slugs = [
                "posts" => []
            ];
            foreach($posts as $p) {
                $slugs["posts"][$p->ID] = $p->post_name;
            }

            file_put_contents( FN_PLUGIN_DIR . 'data/slug_list.json', json_encode($slugs));
        }

        

        $this->redirect();

    }

    public function revalidate_home() {
        if( !$this->has_valid_nonce('revalidate_home', 'revalidate_home_nonce')) {
            $this->redirect();
        }

        $data = [
            'secret'    => FN_SECRET_KEY,
            'type'      => 'home'
        ];

        $query_string = http_build_query($data);

        $url = FN_URL . '?' . $query_string;

        $response = wp_remote_get($url);
        $body_json = wp_remote_retrieve_body($response);

        file_put_contents(FN_PLUGIN_DIR . 'data/response_body.json', json_encode($response));

        $this->redirect();
    }

    public function save_update_data($ID, $post, $updating) {

        if($post->post_status !== 'publish') {
            return;
        }

        if($post->post_date === $post->post_modified) {
            $updating = false; // original $updating param seems to be useless: always returning true
        }
        else {
            $updating = true;
        }

        $extracted_post = [
            "ID" => $ID,
            "post_name" => $post->post_name,
            "post_type" => $post->post_type
        ];
        if($updating) {
            $this->update_data['updated_posts'][$ID] = $extracted_post;
        }
        else {
            $this->update_data['new_posts'][$ID] = $extracted_post;
        }
        file_put_contents( FN_PLUGIN_DIR . 'data/update_data.json', json_encode($this->update_data) );
    }

    public function get_value( $option_key ) {
        return get_option( $option_key, '');

    }

    private function has_valid_nonce($action, $name) {

        if( ! isset( $_POST[$name] ) ) {
            return false;
        }

        $field = wp_unslash( $_POST[$name] );

        return wp_verify_nonce( $field, $action );
    }

    private function redirect() {

        if( ! isset( $_POST[ '_wp_http_referer'] ) ) {
            $_POST['_wp_http_header'] = wp_login_url();
        }

        $url = sanitize_text_field(
            wp_unslash( $_POST['_wp_http_referer'] ) 
        );

        wp_safe_redirect( urldecode( $url ) );
        exit;
    }
      
 }