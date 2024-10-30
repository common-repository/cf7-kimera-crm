<?php
/**
 ** A Kimera enhanced module for [checkbox], [checkbox*], and [radio]
 **/

/* Tag generator */

add_action( 'wpcf7_admin_init',
	'wpcf7_add_tag_generator_kcheckbox_and_kradio', 31 );

function wpcf7_add_tag_generator_kcheckbox_and_kradio() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'kcheckbox',  __( 'checkboxes (from CRM)', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kcheckbox' );
	$tag_generator->add( 'kradio',  __( 'radio buttons (from CRM)', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kcheckbox' );
}

function wpcf7_tag_generator_kcheckbox( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = substr($args['id'],1);


	if ( 'radio' != $type ) {
		$type = 'checkbox';
	}

    $description='';
    if ( 'checkbox' == $type ) {
		$description =  __( "Generate a form-tag for a group of checkboxes retrieving the values from the CRM.", 'cf7-kimera-crm' );
	} elseif ( 'radio' == $type ) {
		$description =  __( "Generate a form-tag for a group of radio buttons retrieving the values from the CRM.", 'cf7-kimera-crm' );
	}

	$desc_link = wpcf7_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7' ), __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7' ) );

?>
<div class="control-box">
    <fieldset>
        <legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

        <table class="form-table">
            <tbody>
                <?php if ( 'checkbox' == $type ) : ?>
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
                <?php endif; ?>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-cf7k_resource' ); ?>"><?php echo esc_html( __( 'Resource', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="cf7k_resource" class="option" id="<?php echo esc_attr( $args['content'] . '-cf7k_resource' ); ?>" /></td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-cf7k_filter' ); ?>"><?php echo esc_html( __( 'Filter', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="cf7k_filter" class="option" id="<?php echo esc_attr( $args['content'] . '-cf7k_filter' ); ?>" /></td>
                </tr>


                <tr>
                    <th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
                            <textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
                            <label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'contact-form-7' ) ); ?></span></label><br />
                            <label>
                                <input type="checkbox" name="label_first" class="option" />
                                <?php echo esc_html( __( 'Put a label first, a checkbox last', 'contact-form-7' ) ); ?></label><br />
                            <label>
                                <input type="checkbox" name="use_label_element" class="option" />
                                <?php echo esc_html( __( 'Wrap each item with label element', 'contact-form-7' ) ); ?></label>
                            <?php if ( 'checkbox' == $type ) : ?>
                            <br />
                            <label>
                                <input type="checkbox" name="exclusive" class="option" />
                                <?php echo esc_html( __( 'Make checkboxes exclusive', 'contact-form-7' ) ); ?></label>
                            <?php endif; ?>
                        </fieldset>
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
                <tr>
                    <th scope="row"><?php echo esc_html( __( 'Events', 'cf7-kimera-crm' ) ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php echo esc_html( __( 'Events', 'cf7-kimera-crm' ) ); ?></legend>
                            <label>
                                <?php echo esc_html( __( 'Master elements:', 'cf7-kimera-crm' ) ); ?></label><br />
                            <input type="text" name="cf7k_master_elements" class="cf7k_master_elements oneline filetype option" id="<?php echo esc_attr( $args['content'] . '-cf7k_master_elements' ); ?>"/><br />
                            <br />
                            <label>
                                <input type="checkbox" name="cf7k_onclick" class="option" />
                                <?php echo esc_html( __( 'Refresh panels on click event', 'cf7-kimera-crm' ) ); ?></label><br />
                            <label>
                                <input type="checkbox" name="cf7k_onchange" class="option" />
                                <?php echo esc_html( __( 'Refresh panels on change event', 'cf7-kimera-crm' ) ); ?></label>
                        </fieldset>
                    </td>
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
        <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label>
    </p>
</div>

<?php
}

?>