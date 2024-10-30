<?php
/**
 * @author Kimera Srl <info@kimeranet.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */
$errormessage='';

if (!empty($kimeracrm['url'])){
    $url = $kimeracrm['url'].'/kapi/system/_webtodatacontrollers?withfields=1';   

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// !!!!!!!

    if (!($json = curl_exec($ch))){
        $errormessage= $errormessage . curl_error($ch);
    }
    else 
    {
        $http_code= curl_getinfo($ch)['http_code'];
        if ($http_code!=200)
        {
            $errormessage= $errormessage . 'Error --> return code: '.$http_code;
        }
    }
    curl_close($ch); 
    if(empty($errormessage)){
        $return=json_decode($json,true);
    }
}
else {
    $errormessage=__('In order to view the resources and fields available, you need to specify the CRM Url', 'cf7-kimera-crm');
}


function cmp($a, $b)
{
    if ($a['id'] == $b['id']) {
        return 0;
    }
    return ($a['id'] < $b['id']) ? -1 : 1;
}

?>
<script>
    
</script>

<div class="contact-form-editor-box-kimeracrm">
    <h3><?php echo esc_html(__( 'List of available resources and related fields on the selected crm', 'cf7-kimera-crm')); ?></h3>

    <?php if(empty($errormessage)){?>
    <table border="1" cellpadding="5" style="border-width: 1px; border-spacing: 0px;">
        <thead>
            <tr>
                <th style="text-align: left;"><?php echo esc_html(__('Resource', 'cf7-kimera-crm')); ?></th>
                <th style="text-align: left;"><?php echo esc_html(__('Field Code', 'cf7-kimera-crm')); ?></th>
                <th style="text-align: left;"><?php echo esc_html(__('Description', 'cf7-kimera-crm')); ?></th>
            </tr>
        </thead>
        <?php 
              usort($return, "cmp");
              foreach($return as $controller) {?>
        <?php 
                  $index = 0;
                  usort($controller['fields'], "cmp");
                  foreach($controller['fields'] as $fields) {?>
        <tr>
            <?php if($index==0){?>
            <td style="vertical-align:top; " rowspan="<?php echo sizeof($controller['fields']);?>">
                <?php echo $controller['id'];?>
            </td>
            <?php } ?>
            <td><?php echo $fields['id'];?></td>
            <td><?php echo $fields['text'];?></td>
        </tr>
        <?php $index++;
                  }?>
        <?php }?>
    </table>
    <?php } else { ?>
    <div class="update-message notice inline notice-error notice-alt">
        <p>
            <?php echo esc_html($errormessage); ?>
        </p>
    </div>
    <?php } ?>
</div>
