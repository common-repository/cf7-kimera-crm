<?php
require_once( '../../../../wp-load.php' );

class cf7_kimera_crm{
    public static function replace_form_tags($manager,$content) {
		$form = $content;
		if ( WPCF7_AUTOP ) {
			$form = $manager->normalize( $form );
			$form = wpcf7_autop( $form );
		}
		$form = $manager->replace_all( $form );
		return $form;
	}
}

$id = (int) $_POST['_kcfid'];
$refreshElements =  $_POST['_refreshElements'];
$retVal = array();

if (sizeof($refreshElements)>0) {
    $cf7_form= WPCF7_ContactForm::get_instance($id);
    $form=$cf7_form->prop('form');
    $manager = WPCF7_FormTagsManager::get_instance();
    $tagregexp = join( '|', array_map( 'preg_quote', $refreshElements ) );
    $regex = '\[kpanel_begin\s+(?:'.$tagregexp.')(?:\]|\s[^\]]*\])((?:(?:(?!\[kpanel_begin[^\]]*\]|\[kpanel_end\]).)++|\[kpanel_begin[^\]]*\](?1)\[kpanel_end\])*)\[kpanel_end\]';
    $form= preg_replace_callback('/'.$regex.'/si',function ($matches) use(&$retVal,$manager,$refreshElements){
        $tag_str =substr  ($matches[0],0, strpos($matches[0], ']')+1);
        $tags = $manager->scan($tag_str);
        if (sizeof($tags)>0) 
        {
            $tag = new WPCF7_Shortcode( $tags[0] );       
            $html= cf7_kimera_crm::replace_form_tags($manager,$matches[0]);           
            $retVal[$tag->name]=$html;
            return '';
        }
        else return $matches[0];        
    }, $form);
    $regex = '(\[?)'
            . '\[(.*?)(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\]'
            . '(?:([^[]*?)\[\/\2\])?'
            . '(\]?)';
    preg_replace_callback('/'.$regex.'/s',function ($matches) use(&$retVal,$manager,$refreshElements){
        if ($manager->tag_type_exists($matches[2]))
        {
            $tags = $manager->filter($matches[0],array('name'=>$refreshElements));
            if (sizeof($tags)>0) 
            {
                $tag = new WPCF7_Shortcode( $tags[0] );
                $retVal[$tag->name]=cf7_kimera_crm::replace_form_tags($manager,$matches[0]);            
            }
        }
    }, $form);
}
echo json_encode($retVal);

//$html = apply_filters( 'wpcf7_form_elements',cf7_kimera_crm::replace_form_tags($manager,$tag) );

die();
?>