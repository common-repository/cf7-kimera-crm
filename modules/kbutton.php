<?php

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_kbutton' );
function wpcf7_add_form_tag_kbutton() {
	wpcf7_add_form_tag( array( 'kbutton'),'wpcf7_kbutton_form_tag_handler', true );
}
function wpcf7_kbutton_form_tag_handler( $tag ) {
    $tag = new WPCF7_FormTag( $tag );

	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
    $atts['name'] = $tag->name ;

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value =  __( 'Refresh', 'cf7-kimera-crm' );
	}

	$atts['type'] = 'button';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;

}


add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_kbutton', 52 );

function wpcf7_add_tag_generator_kbutton() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'kbutton', __( 'Button', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kbutton' );
}

function wpcf7_tag_generator_kbutton( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'kbutton';
    $description = __( "Generate a form-tag for a button to refresh elements.", 'cf7-kimera-crm' );

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
                        <label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /></td>
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
                                <input type="checkbox" name="cf7k_onclick" class="option" />
                                <?php echo esc_html( __( 'Fire refresh event on click', 'cf7-kimera-crm' ) ); ?></label><br />
                            <label>
                                <input type="checkbox" name="cf7k_onchange" class="option" />
                                <?php echo esc_html( __( 'Fire refresh event change', 'cf7-kimera-crm' ) ); ?></label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
</div>

<div class="insert-box">
    <input name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()"/>
    <div class="submitbox">
        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
    </div>
</div>
<?php
}
