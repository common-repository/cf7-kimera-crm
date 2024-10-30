<?php
/**
 ** A Kimera enhanced module for [file] and [file*]
 **/

/* Tag generator */

add_action( 'wpcf7_admin_init', 'wpcf7_add_tag_generator_kfile', 51 );

function wpcf7_add_tag_generator_kfile() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'kfile', __( 'CRM file', 'cf7-kimera-crm' ),
		'wpcf7_tag_generator_kfile' );
}

function wpcf7_tag_generator_kfile( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'file';

	$description = __( "Generate a form-tag for a file uploading field that sends the file to Kimera CRM. For more details, see %s.", 'cf7-kimera-crm' );

	$desc_link = wpcf7_link( __( 'https://contactform7.com/file-uploading-and-attachment/', 'contact-form-7' ), __( 'File Uploading and Attachment', 'contact-form-7' ) );

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
                        <label for="<?php echo esc_attr( $args['content'] . '-entitycontroller' ); ?>"><?php echo esc_html( __( 'Document resource', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="entitycontroller" class="entitycontroller oneline option" id="<?php echo esc_attr( $args['content'] . '-entitycontroller' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-entityfield' ); ?>"><?php echo esc_html( __( 'Entity field', 'cf7-kimera-crm' ) ); ?></label></th>
                    <td>
                        <input type="text" name="entityfield" class="entityfield oneline option" id="<?php echo esc_attr( $args['content'] . '-entityfield' ); ?>"/></td>
                </tr>


                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-limit' ); ?>"><?php echo esc_html( __( "File size limit (bytes)", 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="limit" class="filesize oneline option" id="<?php echo esc_attr( $args['content'] . '-limit' ); ?>" /></td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>"><?php echo esc_html( __( 'Acceptable file types', 'contact-form-7' ) ); ?></label></th>
                    <td>
                        <input type="text" name="filetypes" class="filetype oneline option" id="<?php echo esc_attr( $args['content'] . '-filetypes' ); ?>" /></td>
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
        <label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To attach the file uploaded through this field to mail, you need to insert the corresponding mail-tag (%s) into the File Attachments field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label>
    </p>
</div>
<?php
}


