<?php

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_kpanel' );
function wpcf7_add_form_tag_kpanel() {
	wpcf7_add_form_tag( array( 'kpanel_begin','kpanel_end'),'wpcf7_kpanel_form_tag_handler', true );
}
function wpcf7_kpanel_form_tag_handler( $tag ) {
    $tag = new WPCF7_Shortcode( $tag );
    $html='';
    if ($tag->type =='kpanel_end') $html= '</div>';
    if ($tag->type =='kpanel_begin'){
        $class = wpcf7_form_controls_class( $tag->type );
        $atts = array();
        $atts['class'] = $tag->get_class_option( $class );
        $atts['class'] .=(' '.sanitize_html_class( $tag->name ));
        $atts['id'] = $tag->get_id_option();
        $atts['name'] = $tag->name ;
        $atts = wpcf7_format_atts( $atts );
        $html=  sprintf('<div %1$s>',$atts ); 
    }
    return $html;
}


add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_kpanel', 51 );

function wpcf7_add_tag_generator_kpanel() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'kpanel', __( 'Panel', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kpanel' );
}

function wpcf7_tag_generator_kpanel( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'kpanel_begin';
    $description = __( "Generate a form-tag for a refresh panel.", 'cf7-kimera-crm' );

?>
<div class="control-box">
    <fieldset>
        <legend><?php echo esc_html( $description );?></legend>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row"><?php echo esc_html( __( 'Events', 'cf7-kimera-crm' ) ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html( __( 'Events', 'cf7-kimera-crm' ) ); ?></legend>
                            <label>
                                <?php echo esc_html( __( 'Master elements:', 'cf7-kimera-crm' ) ); ?></label><br />
                            <input type="text" name="cf7k_master_elements" class="cf7k_master_elements oneline filetype option" id="<?php echo esc_attr( $args['content'] . '-cf7k_master_elements' ); ?>"/><br />
                            <br />
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>

<div class="insert-box">
    <textarea name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" style="width:100%;height:70px;"></textarea>
    <div class="submitbox">
        <input type="button" class="button button-primary insert-tag-textarea" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
    </div>
</div>
<?php
}
