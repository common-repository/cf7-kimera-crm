<?php
/**
 * @author Kimera Srl <info@kimeranet.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

class cf7_kimera_admin {
    const NONCE = 'cf7_kimera_admin';

    protected static $initiated = false;

    public static function init() {
        if (!self::$initiated) {
            self::$initiated = true;
            add_action( 'admin_enqueue_scripts', array('cf7_kimera_admin', 'admin_enqueue_scripts') );
            add_action( 'wpcf7_save_contact_form', array('cf7_kimera_admin', 'save_contact_form'));
            add_filter( 'wpcf7_editor_panels', array('cf7_kimera_admin', 'panels'));
        }
    }

    public static function view( $name, array $args = array() ) {
        $args = apply_filters( 'cf7_kimera_view_arguments', $args, $name );

        foreach ( $args AS $key => $val ) {
            $$key = $val;
        }

        //load_plugin_textdomain( 'cf7-kimera-crm' );

        $file = CF7_KIMERA__PLUGIN_DIR . 'views/'. $name . '.php';
        include( $file );
    }

    /**
     * Add a Kimera CRM setting panel to the contact form admin section.
     *
     * @param array $panels
     * @return array
     */
    public static function panels($panels) {
        $panels['cf7-kimeracrm-integration'] = array(
          'title' =>  'Kimera CRM',
          'callback' => array('cf7_kimera_admin', 'kimeracrm_panel'),
        ) ;
        $panels['cf7-kimeracrm-integration-help'] = array(
         'title' =>  'Kimera CRM Help',
         'callback' => array('cf7_kimera_admin', 'kimeracrm_panel_help'),
       ) ;
        return $panels;
    }

    public static function kimeracrm_panel($post) {
        $kimeracrm = $post->prop('kimeracrm' );
        cf7_kimera_admin::view('kimeracrm_panel', array('post' => $post, 'kimeracrm' => $kimeracrm));
    }

    public static function kimeracrm_panel_help($post) {
        $kimeracrm = $post->prop('kimeracrm' );
        cf7_kimera_admin::view('kimeracrm_panel_help', array('post' => $post, 'kimeracrm' => $kimeracrm));
    }

    public static function save_contact_form($contact_form) {
        $properties = $contact_form->get_properties();
        $kimeracrm = $properties['kimeracrm'];

        $kimeracrm['enable'] = false;

        if ( isset( $_POST['enable-kimeracrm'] ) ) {
            $kimeracrm['enable'] = true;
        }

        //$kimeracrm['html5_fallback'] = false;

        //if ( isset( $_POST['html5_fallback-kimeracrm'] ) ) {
        //    $kimeracrm['html5_fallback'] = true;
        //}


        $kimeracrm['skipmail'] = false;
        if ( isset( $_POST['kimeracrm-skipmail'] ) ) {
            $kimeracrm['skipmail'] = true;
        }

        if ( isset( $_POST['kimeracrm-url'] ) ) {
            $kimeracrm['url'] = trim( $_POST['kimeracrm-url'] );
        }

        if ( isset( $_POST['kimeracrm-controller'] ) ) {
            $kimeracrm['controller'] = trim( $_POST['kimeracrm-controller'] );
        }

        if ( isset( $_POST['kimeracrm-key'] ) ) {
            $kimeracrm['key'] = trim( $_POST['kimeracrm-key'] );
        }


        if ( isset( $_POST['kimeracrm-action'] ) ) {
            $kimeracrm['action'] = trim( $_POST['kimeracrm-action'] );
        }
        if ( isset( $_POST['kimeracrm-parameters'] ) ) {
            $kimeracrm['parameters'] = trim( $_POST['kimeracrm-parameters'] );
        }

        $properties['kimeracrm'] = $kimeracrm;
        $contact_form->set_properties($properties);
    }

    public static function admin_enqueue_scripts($hook_suffix) {
        if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
            return;
        }
        wp_register_style( 'cf7_kimera_crm_css', CF7_KIMERA__PLUGIN_URL.'css/cf7-kimera-crm.css',null,CF7_KIMERA__VERSION);
        wp_enqueue_style( 'cf7_kimera_crm_css' );

        wp_enqueue_script( 'cf7_kimeracrm-admin',
          CF7_KIMERA__PLUGIN_URL. 'js/admin.js',
          array( 'jquery', 'jquery-ui-tabs','wpcf7-admin-taggenerator' ),CF7_KIMERA__VERSION,true//

        );
    }
}