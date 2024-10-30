<?php
/**
 ** A base module for the following types of tags:
 ** 	[kdate] and [kdate*]		# Date
 **/

/* form_tag handler */

add_action( 'wpcf7_init', 'wpcf7_add_form_tag_kdate' );

function wpcf7_add_form_tag_kdate() {
	wpcf7_add_form_tag( array( 'kdate', 'kdate*' ),
		'wpcf7_kdate_form_tag_handler', array( 'name-attr' => true ) );
}

function wpcf7_kdate_form_tag_handler( $tag ) {
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = wpcf7_get_validation_error( $tag->name );

	$class = wpcf7_form_controls_class( $tag->type );

	$class .= ' wpcf7-validates-as-date ';

	if ( $validation_error ) {
		$class .= ' wpcf7-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );
	$atts['dateFormat'] = $tag->get_option( 'dateFormat', '', true );
	$atts['defaultDate'] = $tag->get_option( 'defaultDate', '', true);
	$atts['yearRange'] = $tag->get_option( 'yearRange', '', true);

	if ( $tag->has_option( 'readonly' ) ) {
		$atts['readonly'] = 'readonly';
	}

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$value = (string) reset( $tag->values );

	if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
		$atts['placeholder'] = $value;
		$value = '';
	}

	$value = $tag->get_default_option( $value );

	$value = wpcf7_get_hangover( $tag->name, $value );

	//$atts['value'] = $value;

    $atts['type'] = 'text';

	/*if ( wpcf7_support_html5() ) {
    $atts['type'] = $tag->basetype;
	} else {
    $atts['type'] = 'text';
	}*/

	//$atts['name'] = $tag->name;
	$atts['id'] = $tag->name;

	$atts = wpcf7_format_atts( $atts );
    
    $atts2['type'] = 'hidden';
    $atts2['value'] = $value;
	$atts2['name'] = $tag->name;
    $atts2 = wpcf7_format_atts( $atts2 );
    

	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><input %2$s /><input %4$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error,$atts2 );

	return $html;
}

add_filter( 'wpcf7_validate_kdate', 'wpcf7_date_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_kdate*', 'wpcf7_date_validation_filter', 10, 2 );


add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_kdate', 19 );

function wpcf7_add_tag_generator_kdate() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'kdate', __( 'enhanced date', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kdate' );
}

function wpcf7_tag_generator_kdate( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'kdate';

	$description = __( "Generate a form-tag for an enhanched date input field.", 'cf7-kimera-crm' );

	$desc_link ='';// wpcf7_link( __( 'https://contactform7.com/date-field/', 'contact-form-7' ), __( 'Date Field', 'contact-form-7' ) );

?>
<div class="control-box">
    <fieldset>
        <legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
                            <label>
                                <input type="checkbox" name="required" />
                                <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
                        </fieldset>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Placeholder', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /><br />
                        <label>
                            <input type="checkbox" name="placeholder" class="option" />
                            <?php echo esc_html( __( 'Use this text as the placeholder of the field', 'contact-form-7' ) ); ?></label></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-dateFormat' ); ?>"><?php echo esc_html( __( 'Format', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="dateFormat" class="oneline option" id="<?php echo esc_attr( $args['content'] . '-dateFormat' ); ?>" />
                        &nbsp;<a href="http://api.jqueryui.com/datepicker/#option-dateFormat" target="_blank"><?php echo esc_html( __( 'Format', 'cf7-kimera-crm' ) ); ?></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-yearRange' ); ?>"><?php echo esc_html( __( 'Years selector range (optional)', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="yearRange" class="oneline option" id="<?php echo esc_attr( $args['content'] . '-yearRange' ); ?>" />
                        &nbsp;<a href="http://api.jqueryui.com/datepicker/#option-yearRange" target="_blank"><?php echo esc_html( __( 'Format', 'cf7-kimera-crm' ) ); ?></a>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-defaultDate' ); ?>"><?php echo esc_html( __( 'Starting date (optional)', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="defaultDate" class="oneline option" id="<?php echo esc_attr( $args['content'] . '-defaultDate' ); ?>" />
                        &nbsp;<a href="http://api.jqueryui.com/datepicker/#option-defaultDate" target="_blank"><?php echo esc_html( __( 'Format', 'cf7-kimera-crm' ) ); ?></a>
                    </td>
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
            </tbody>
        </table>
    </fieldset>
</div>

<div class="insert-box">
    <input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

    <div class="submitbox">
        <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
    </div>

    <br class="clear" />

    <p class="description mail-tag">
        <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
