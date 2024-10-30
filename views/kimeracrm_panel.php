<?php
/**
 * @author Kimera Srl <info@kimeranet.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
?>
<h3><?php echo esc_html(__( 'Settings for', 'cf7-kimera-crm').' Kimera CRM'); ?></h3>

<div class="contact-form-editor-box-kimeracrm">



    <fieldset>
        <legend><?php echo esc_html(__("Enable Kimera CRM integration for this Contact Form.", 'cf7-kimera-crm'));?></legend>
        <label><?php echo esc_html(__("WARNING: in order to take advantage of all the functionalities of this plugin we STRONGLY RECOMMEND to include the \"define( 'WPCF7_AUTOP', false );\" directive in your \"wp-config.php\" file.", 'cf7-kimera-crm')); ?></label><br />
        <br />
        <label><?php echo esc_html(__("Insert the URL for your Kimera CRM installation and the Authorization Key associated to enable read/write data features.", 'cf7-kimera-crm')); ?></label>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="url"><?php echo esc_html(__('Url CRM', 'cf7-kimera-crm')); ?></label>
                    </th>
                    <td>
                        <input type="text" id="kimeracrm-url" name="kimeracrm-url" class="large-text code" size="70" value="<?php echo esc_attr( $kimeracrm['url'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="key"><?php echo esc_html( __('Authorization key', 'cf7-kimera-crm')); ?></label>
                    </th>
                    <td>
                        <input type="text" id="kimeracrm-key" name="kimeracrm-key" class="large-text code" size="70" value="<?php echo esc_attr( $kimeracrm['key'] ); ?>" /><br />
                        <label><?php echo esc_html( __("Retrieve this value from the 'Impostazioni / Gestione Form Web' section of the CRM. Look for the label 'Codice'", 'cf7-kimera-crm')); ?></label>
                    </td>
                </tr>
            </tbody>
        </table>
    </fieldset>
<!--    <p>
        <label for="html5_fallback-kimeracrm">
            <input type="checkbox" id="html5_fallback-kimeracrm" name="html5_fallback-kimeracrm" value="1"<?php echo ( ! empty( $kimeracrm['html5_fallback'] ) ) ? ' checked="checked"' : ''; ?> />
            <?php echo esc_html(__("Enable HTML 5 fallback functionality (useful when using DATE input)", 'cf7-kimera-crm' )); ?></label>
    </p>-->
    <p>
        <label for="enable-kimeracrm">
            <input type="checkbox" id="enable-kimeracrm" name="enable-kimeracrm" class="toggle-k-table" value="1"<?php echo ( ! empty( $kimeracrm['enable'] ) ) ? ' checked="checked"' : ''; ?> />
            <?php echo esc_html(__("Enable the submission of data to Kimera CRM", 'cf7-kimera-crm' )); ?></label>
    </p>
    <fieldset>
        <legend><?php echo esc_html(__("Performs an insert operation on a resource of a Kimera CRM installation. E.g Resource: Anagrafiche, Action: POST.", 'cf7-kimera-crm'));?></legend>
        <table class="form-table toggle-k-target">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="controller"><?php echo esc_html(__('Resource', 'cf7-kimera-crm')); ?></label>
                    </th>
                    <td>
                        <input type="text" id="kimeracrm-controller" name="kimeracrm-controller" class="large-text code" size="70" value="<?php echo esc_attr( $kimeracrm['controller'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="action"><?php echo esc_html(__('Action', 'cf7-kimera-crm')); ?></label>
                    </th>
                    <td>
                        <input type="text" id="kimeracrm-action" name="kimeracrm-action" class="large-text code" size="70" value="<?php echo esc_attr( $kimeracrm['action'] ); ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="parameters"><?php echo esc_html(__('Fixed parameters', 'cf7-kimera-crm')); ?></label>
                    </th>
                    <td>
                        <input type="text" id="kimeracrm-parameters" name="kimeracrm-parameters" class="large-text code" size="70" value="<?php echo esc_attr( $kimeracrm['parameters'] ); ?>" />
                        <label><?php echo esc_html(__("Sets Form independent values.  E.g. idTipologia=xxxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx&idProvenienza=xxxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx", 'cf7-kimera-crm')); ?></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row" colspan="2">
                        <label for="kimeracrm-skipmail">
                            <input type="checkbox" id="kimeracrm-skipmail" name="kimeracrm-skipmail" value="1"<?php echo ( ! empty( $kimeracrm['skipmail'] ) ) ? ' checked="checked"' : ''; ?> />
                            <?php echo esc_html(__("Prevent Conctat Form 7 from actually sending the email.", 'cf7-kimera-crm')); ?>
                        </label>
                    </th>
                </tr>
            </tbody>
        </table>

    </fieldset>
</div>
