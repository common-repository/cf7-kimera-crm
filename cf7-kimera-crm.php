<?php
/**
 * @author Kimera Srl <info@kimeranet.com>
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

/*
Plugin Name: Contact Form 7 Kimera CRM integration
Display Name: Contact Form 7 Kimera CRM integration 
Plugin URI: http://wordpress.kimeranet.com/
Description: Submit contact form 7 to an external Kimera CRM
Version: 1.1.6
Author: Kimera Srl
Author URI: http://www.kimeranet.com
License: GPLv2
Text Domain: cf7-kimera-crm
 */

define( 'CF7_KIMERA__VERSION', '1.1.6' );
define( 'CF7_KIMERA__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CF7_KIMERA__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CF7_KIMERA__CF7_REQUIRED_VERSION', '4.6' );

add_action('wp_enqueue_scripts', 'cf7_kimera_crm_scripts', 10, 1);
function cf7_kimera_crm_scripts() {
    wp_register_style( 'cf7_kimera_crm_css', CF7_KIMERA__PLUGIN_URL.'/css/cf7-kimera-crm.css',null,CF7_KIMERA__VERSION );
    wp_enqueue_script( 'cf7_kimera_crm_js',  CF7_KIMERA__PLUGIN_URL.'/js/ajax.js', array( 'jquery' ), CF7_KIMERA__VERSION );
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style( 'jquery-ui-smoothness',
        wpcf7_plugin_url( 'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css' ), array(), WPCF7_VERSION);

    wp_enqueue_script( 'cf7_kimera_crm_js2',  CF7_KIMERA__PLUGIN_URL.'/js/html5.js', array( 'jquery' ), CF7_KIMERA__VERSION ,true);
    wp_enqueue_style( 'cf7_kimera_crm_css' );
    wp_localize_script( 'cf7_kimera_crm_js', 'cf7_kimera_crm_ajax', array( 'ajaxurl' => CF7_KIMERA__PLUGIN_URL.'/ajax/ajax.php' ) );   
}

if ( is_admin() ) {
    require_once( CF7_KIMERA__PLUGIN_DIR . 'cf7-kimera-crm-admin.php' );
    add_action( 'init', array( 'cf7_kimera_admin', 'init' ) );
}

add_filter( 'wpcf7_contact_form_properties', 'contact_form_kimera_properties');

add_filter('wpcf7_pre_construct_contact_form_properties','cf7_kimera_register_property',10, 2);
function cf7_kimera_register_property( $properties, $contact_form ) {
    $properties += array('kimeracrm' => array(),);
    return $properties;
}

function cf7_kimera_submit( $contactform, $result ) {
    $result['status']='validation_failed';
    $result['message']='test error';
    $result['invalid_fields']=array ();
}

add_filter('wpcf7_skip_mail',function($sm,$contact_form){
	return !!$contact_form->skip_mail; 
},10,2);

function cf7_kimera_before_send_mail($contact_form) {
    $properties = $contact_form->get_properties();
    if (empty($properties['kimeracrm']['enable'])) {
        return;
    }
    $errormessage='';
    if (empty($properties['kimeracrm']['controller'])) {
        $errormessage = $errormessage . __('Missing Kimera CRM resource','cf7-kimera-crm').'\n';
    }
    if (empty($properties['kimeracrm']['action'])) {
        $errormessage = $errormessage . __('Missing Kimera CRM action','cf7-kimera-crm').'\n';
    }
    if(empty($errormessage)){
        $submission = WPCF7_Submission::get_instance();   
        $submittedData = $submission->get_posted_data();
        $data = array();

        $originalTags = $contact_form->scan_form_tags();
        foreach ( (array) $originalTags as $tag ) {
            if ( !empty( $tag['name'] ) ) {
                $name = $tag['name'];
                $value = '';

                $value = $submittedData[$name];

                $pipes = $tag['pipes'];

                if (!( WPCF7_USE_PIPE
                && $pipes instanceof WPCF7_Pipes
                && ! $pipes->zero() )) $submittedData[$name]=wp_unslash( $value );
            }
        }

        foreach($submittedData as $key => $val) {
            if (is_array($val)) {
                $val = implode(",", $val);
            }
            $data[$key] = $val;
        }
        foreach( wpcf7_scan_shortcode( array( 'type' => 'acceptance' ) ) as $shortcode) {
            $data[ $shortcode['name']] = $data[ $shortcode['name']]!='' ? 'true' : 'false';
        }
        foreach( wpcf7_scan_shortcode( array( 'type' => array( 'checkbox', 'checkbox*' ) ) ) as $shortcode) {
            $tag = new WPCF7_Shortcode( $shortcode );  
            $tagopt=$tag->get_option('cf7k_resource','',true);
            if (empty($tagopt) && sizeof($tag->values==1)             )
                $data[ $shortcode['name']] = $data[ $shortcode['name']]!='' ? 'true' : 'false';
        }
        $parameters = explode("&", $properties['kimeracrm']['parameters']);
        foreach($parameters as $param) {
            list($key, $val) = explode("=", $param);
            if (!empty($key)) {
                $data[$key] = $val;
            }
        }
        if (!$data['id']){
            $url = $properties['kimeracrm']['controller'].'/'.$properties['kimeracrm']['action'].'/';
        }
        else{
            $url = $properties['kimeracrm']['controller'].'/put/';
            if (sizeof($submission->uploaded_files())>0){               
                $manager = WPCF7_FormTagsManager::get_instance();
                $regex = '/(\[?)\[(file\*|file)(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\](?:([^[]*?)\[\/\2\])?(\]?)/s';
                $tags=array();
                preg_match_all($regex,$contact_form->prop('form'),$tags,PREG_SET_ORDER);
                $properties=array();
                foreach($tags as $tag){                    
                    $tag = new WPCF7_Shortcode( $manager->scan($tag[0])[0] );                      
                    $properties[$tag->get_option('entityfield','',true)?$tag->get_option('entityfield','',true):'documenti'] = $tag->get_option('entitycontroller','',true)?$tag->get_option('entitycontroller','',true):'anagraficadocumenti';
                }                
                foreach($properties as $key=>$property){
                    $urlget = $property.'/get/?id='.$data['id'];
                    $tmpGetCRM=  crm_call($contact_form,$urlget);
                    if (!empty($tmpGetCRM['errormessage'])){
                        $errormessage=$tmpGetCRM['errormessage'];
                    }
                    else{
                        $data[$key] =$tmpGetCRM['data'];
                    }
                }                
            }
        }        
        if(empty($errormessage)){
            $tmpCRM= crm_call($contact_form,$url,$data,$submission->uploaded_files());
            if (!empty($tmpCRM['errormessage'])){
                $errormessage=$tmpCRM['errormessage'];
            }
        }
    }
    $contact_form->skip_mail= !empty($properties['kimeracrm']['skipmail']);
    if(!empty($errormessage)){
        $contact_form->skip_mail=false;       
        add_filter('wpcf7_display_message',function($message, $status) use($errormessage,$contact_form){            
            return (wpcf7_get_current_contact_form()->id()==$contact_form->id() && $status=='mail_sent_ng')? $errormessage:$message;
        },10,2);
        add_action('phpmailer_init', function (&$phpmailer) use($errormessage,$contact_form){            
            if (wpcf7_get_current_contact_form()->id()==$contact_form->id())
                $phpmailer=new cf7k_fake_phpmailer();
        });
    }
}

function contact_form_kimera_properties($properties) {
    //if (!isset($properties['kimeracrm'])) {
    //    $properties['kimeracrm'] = array(
    //      'html5_fallback' => true,
    //      'override_autop' => true,
    //      'autop' => false,
    //      'skipmail' => true,
    //      'enable' => false,
    //      'url' => 'https://....',
    //      'controller' => 'Anagrafiche',
    //      'key' => 'default',
    //      'action' => 'post',
    //      'parameters' => ''
    //    );
    //}
    $prop=$properties['kimeracrm'];
    if (!is_array ($prop)) { $prop=array();}
    $properties['kimeracrm'] = array_merge( array(
         // 'html5_fallback' => true,
          'skipmail' => true,
          'enable' => false,
          'url' => 'https://...',
          'controller' => 'Anagrafiche',
          'key' => 'default',
          'action' => 'post',
          'parameters' => ''
        ), $prop) ;
    return $properties;
}

function wpcf7_add_shortcode_ksubmit() {
	wpcf7_add_shortcode( 'submit', 'wpcf7_ksubmit_shortcode_handler' );
}

function wpcf7_ksubmit_shortcode_handler( $tag ) {
    $validation_error = wpcf7_get_validation_error( 'submit' );
    $html = sprintf('<span class="wpcf7-form-control-wrap %1$s">' . wpcf7_submit_form_tag_handler( $tag ) . '%2$s</span>',sanitize_html_class('submit' ),$validation_error );
    return $html;
}

function wpcf7_kcf7_form_action_url($url) {
    if (isset($_POST['_kcfid'])){
        $frag = strstr( $url, '#');
        $url=wp_make_link_relative($_SERVER['HTTP_REFERER']);
        if ($frag)  {       
            $url.=$frag;
        }
    }
    return $url;
}

function get_mime_type($filename) {
    $idx = explode( '.', $filename );
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode-1]);

    $mimet = array( 
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',


        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if (isset( $mimet[$idx] )) {
        return $mimet[$idx];
    } else {
        return 'application/octet-stream';
    }
}

function crm_call($contact_form,$action,$post_data=null,$files=null){
    $kproperties = $contact_form->prop('kimeracrm');    
    $errormessage='';
    if (empty($kproperties['url'])) {
        $errormessage = $errormessage . __('Missing Kimera CRM URL','cf7-kimera-crm').'\n';
    }
    if (empty($kproperties['key'])) {
        $errormessage = $errormessage . __('Missing Kimera CRM authorization key','cf7-kimera-crm').'\n';
    }  
    $data=array();
    if (empty($errormessage)){
        $url = $kproperties['url'].'/eapi/'.$kproperties['key'].'/'.$action;   
        if ($files){
            $manager = WPCF7_FormTagsManager::get_instance();
            $regex = '/(\[?)\[(file\*|file)(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\](?:([^[]*?)\[\/\2\])?(\]?)/s';
            $tags=array();
            preg_match_all($regex,$contact_form->prop('form'),$tags,PREG_SET_ORDER);
            foreach($tags as $tag){
                $tag = new WPCF7_Shortcode( $manager->scan($tag[0])[0] );                      
                $key=$tag->name;
                $file=$files[$key];
                if ($file){                  
                    $urlfile = $kproperties['url'].'/eapi/'.$kproperties['key'].'/k_documents/post/';
                    $file_post=array();
                    $file_post['file']=new CURLFile($file);                    
                    $file_post['metaData|fileName']=$post_data[$key];                    
                    $data = openssl_random_pseudo_bytes(16);
                    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
                    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
                    $guid= vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
                    $pathinfo =pathinfo($post_data[$key]);
                    $file_post['name']= $pathinfo['filename'].' ('.$guid.').'.$pathinfo['extension'];
                    $file_post['metaData|type']=get_mime_type($post_data[$key]);
                    $file_post['metaData|idTipologia']= $tag->get_option('idtipologia','',true) ? $tag->get_option('idtipologia','',true) : '605a73b9-7592-40d2-95f2-715a0959acfd';
                    $propertyName = $tag->get_option('entityfield','',true) ?$tag->get_option('entityfield','',true) : 'documenti';
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $HTTP_REFERER =($_SERVER['HTTPS']!='off' ? 'https://':'http://').$_SERVER['HTTP_HOST'].'/';
                    curl_setopt($ch, CURLOPT_REFERER, $HTTP_REFERER);
                    curl_setopt($ch, CURLOPT_URL, $urlfile);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// !!!!!!!
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data') ); 
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $file_post);
                    if (!($json = curl_exec($ch))){
                        $errormessage= $errormessage . curl_error($ch);
                    }
                    else 
                    {
                        $http_code= curl_getinfo($ch)['http_code'];
                        if ($http_code!=200)
                        {
                            $errormessage= $errormessage . strval( $contact_form->message( 'crm_error') . ": return code: {$http_code}");
                        }
                    }
                    curl_close($ch); 
                    if(empty($errormessage)){
                        $return=json_decode($json,true);
                        if (!empty($return[0]['error'])){
                            $errormessage=$contact_form->message( 'crm_error') . ": " . $return[0]['errorMessage'];
                        }
                        else {
                            $data=$return[0]['data'];                           
                            if(!$post_data[$propertyName]){
                                $post_data[$propertyName]=array();
                            }
                            array_push($post_data[$propertyName],array('id'=>$data['id']));
                        }
                    }
                }
            }
        }
        if(empty($errormessage)){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $HTTP_REFERER =($_SERVER['HTTPS']!='off' ? 'https://':'http://').$_SERVER['HTTP_HOST'].'/';
            curl_setopt($ch, CURLOPT_REFERER, $HTTP_REFERER);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// !!!!!!!
            if ($post_data){
                $data_json = json_encode($post_data);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length:' . strlen($data_json)) ); 
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            }
            if (!($json = curl_exec($ch))){
                $errormessage= $errormessage . curl_error($ch);
            }
            else 
            {
                $http_code= curl_getinfo($ch)['http_code'];
                if ($http_code!=200)
                {
                    $errormessage= $errormessage . strval( $contact_form->message( 'crm_error') . ": return code: {$http_code}");
                }
            }
            curl_close($ch); 
            if(empty($errormessage)){
                $return=json_decode($json,true);
                if (!empty($return[0]['error'])){
                    $errormessage=$contact_form->message( 'crm_error') . ": " . $return[0]['errorMessage'];
                }
                else {
                    $data=$return[0]['data'];
                }
            }
        }
    }
    return array("data" => $data,"errormessage"=> $errormessage);
}

class WPCF7_KDataSource {
    public $evaluated = false;
    private $data= null;
    private $tag= null;
    private $tag_string=null;
    private $cf7= null;
    private $id= null;
    public $result='';

    public function __construct($cf7,$id,$tag_string,$data) {
        $this->tag_string=$tag_string;
        $this->id=$id;
        if (is_array($data)){
            $this->data = array_change_key_case($data);
        }
        $this->cf7=$cf7;
    }

    public function is_valid (){        
        if (!empty($this->tag_string )){
            if($results = preg_match('/\$([^$]*)\$/i',$this->tag_string)) return ($results==0);
            else return true;
        }
        else {
            return true;
        }
    }

    public function load_datasorce()
    {       
        $manager = WPCF7_FormTagsManager::get_instance();
        $tag= $manager->scan($this->tag_string );
        $this->tag = new WPCF7_Shortcode( $tag[0] );
        $this->id=$this->tag->name;
        
        $url=$this->tag->get_option('cf7k_resource','',true).'/get/';
        
        $filter=$this->tag->get_option('cf7k_filter','',true);
        $valid = true;

        $key = false;

        if ($filter && strrpos($filter,'=')===false){
            $url = $url.$filter;    
            $key = true;
        }

        if ($filter && !$key){
            $filters= wp_parse_args($filter);
            foreach( $filters as $f) {
                $valid &= !empty($f) ;
            }
            if($valid){
                $url = $url.'?'.$filter;        
            }
        } 
        else {
            $valid = $key;
        }

        if ($valid){
            $tmpCRM= crm_call($this->cf7,$url);
            if ($tmpCRM &&  empty($tmpCRM['errormessage'])){
                $this->data=$key? $tmpCRM['data']:$tmpCRM['data'][0]; 
                if (is_array($this->data)){
                    $this->data=array_change_key_case($this->data); 
                    $keys = array_change_key_case($this->tag->values);
                    foreach ($keys  as $k) {
                        if(!isset($_POST[$k])){
                            $_POST[$k]=$this->data[$k];                    
                        }
                    }
                }
            }
        }
        else {
            $this->data=array();
        }
    }
    
    public function parse_from_datasorce($string) {
        if (isset($this->data)){
            $reg ='/\$'.$this->id.'\.([^$]*)\$/i';
            //  $lower_data= array_change_key_case($this->data);
            return preg_replace_callback($reg,function($matches){
                $patt = explode('|', $matches[1]);
                $retVal=$this->data[strtolower($patt[0])];
                if (sizeof($patt)>1){
                    if (sizeof($patt)>2 && $patt[2]=='date'){
                        $d=new DateTime($retVal);
                        if ($d!=false){
                            $retVal=$d->format($patt[1]);
                        }
                    }
                    else{
                        $retVal=sprintf($patt[1],$retVal);                        
                    }
                }
                return $retVal===false?'0':$retVal;
            }, $string) ;
        }
        return $string;
    }

    public function evaluate($string, $datasorces){
        if (!$this->evaluated && $this->is_valid()){
            if (!isset($this->data)){
                $this->load_datasorce();
            }
            $this->result= $this->parse_from_datasorce($string);
            foreach ($datasorces as $ds)
            {
                $ds->tag_string=$this->parse_from_datasorce($ds->tag_string);
            }
            return $this->evaluated = true;
        }
        return false;              
    }
}

function purge_post($s_tags,&$post,$elementChange) {
    foreach ($s_tags as $t){
        $tt = new WPCF7_Shortcode( $t );
        if ($filter_exist = $tt->get_option('cf7k_filter','',true)){
            $reg ='/\$post\.'.$elementChange.'\$/i';
            if (preg_match($reg,$filter_exist)>0)
            {
                if (isset($post[$tt->name])) {
                    unset($post[$tt->name]);
                    purge_post($s_tags,$post,$tt->name);
                }
            }               
        }
    }
}

function parsePanelCondition ($content,$manager)
{
    $regex = '\[kpanel_begin\s+[^\]]*\]((?:(?:(?!\[kpanel_begin[^\]]*\]|\[kpanel_end\]).)++|\[kpanel_begin[^\]]*\](?1)\[kpanel_end\])*)\[kpanel_end\]';
    return preg_replace_callback('/'.$regex.'/si',function ($matches) use(&$retVal,$manager,$refreshElements){
        $tag_str =substr  ($matches[0],0, strpos($matches[0], ']')+1);
        $tags = $manager->scan($tag_str);
        $html='';
        if (sizeof($tags)>0) 
        {
            $tag = new WPCF7_Shortcode( $tags[0] );                      
              $condition = join(' && ',array_map(function($e){return empty($e)?'':'('.$e.')';}, $tag->values));
            if (empty($condition) || eval('return '.$condition.';')){
                $html=$tag_str.parsePanelCondition ($matches[1],$manager).'[kpanel_end]';             
            }
            else{
                $html= $tag_str.'[kpanel_end]';           
            }               
        }
        return $html;        
    }, $content);
}

function kparser($cf7) {
    if (!is_admin()){
        $properties= $cf7->get_properties();        
        $newContent = $properties['form'];
        $manager = WPCF7_FormTagsManager::get_instance();
        $prev_data= function_exists ('cf7msm_get')?cf7msm_get('cf7msm_posted_data', '' ):array();
        if (!is_array($prev_data)) {$prev_data=array();}
        $post = $prev_data;
        if (is_array($_POST)){
            $post = array_merge($prev_data, $_POST) ;
        }
        //if (isset($_POST['_changed_element'])){
        //    $elementChange=$_POST['_changed_element'];
        //    unset($_POST['_changed_element']);
        //    $s_tags=$manager->scan($newContent );
        //    purge_post($s_tags,$_POST,$elementChange) ;
        //}

        // Gestione pregresso
        $regex = '/(\[?)\[(kselect\*|kselect|kcheckbox\*|kcheckbox)(?:[\r\n\t ](.*?))?(?:[\r\n\t ](\/))?\](?:([^[]*?)\[\/\2\])?(\]?)/s';
        $newContent=preg_replace_callback($regex,function ($matches) {
            $matches[0]=str_replace($matches[2],substr($matches[2],1),$matches[0]);
            $matches[0]=str_replace(' resource:',' cf7k_resource:',$matches[0]);
            return $matches[0];
        }, $newContent);

        $datasources = array();
        array_push($datasources,new WPCF7_KDataSource($cf7,'post',null,$post));
        array_push($datasources,new WPCF7_KDataSource($cf7,'get',null,$_GET));
        
        $regex = '/\[kdatasource([^]]*)\]/i';
        $newContent=preg_replace_callback($regex,function ($matches)use ($cf7,&$datasources) {
            array_push($datasources,new WPCF7_KDataSource($cf7,null,$matches[0],null));
            return '';
        }, $newContent);

        $replaced=true;
        while ($replaced){
            $replaced=false;
            foreach($datasources as $ds){
                if ($ds->evaluate($newContent,$datasources)){
                    $newContent=$ds->result;
                    $replaced =true;
                }
            }
        }

        $newContent =parsePanelCondition ($newContent,$manager);      
        $properties['form']=$newContent;
        $cf7->set_properties($properties);
    }
};

function wpcf7_add_form_tag_kdatasource() {
	wpcf7_add_form_tag( array( 'kdatasource'), function(){}, true );    
}

function wpcf7_kimera_messages( $messages ) {
	return array_merge( $messages, array(
		'invalid_cap' => array(
			'description' => __( "The CAP you entered is invalid.", 'cf7-kimera-crm' ),
			'default' => __( "The CAP you entered is invalid.", 'cf7-kimera-crm' )
		) ,
		'crm_error' => array(
			'description' => __( "A communication error with the CRM has occured", 'cf7-kimera-crm' ),
			'default' => __( "A communication error has occured", 'cf7-kimera-crm' )
		) 
        ) );
}

function custom_kimera_validation_filter( $result, $tag ) {
    $tag = new WPCF7_FormTag( $tag );
    
    if (array_search('cf7k-cap-validator', explode(' ',$tag->get_class_option()))===false) return $result;

    $value = isset( $_POST[$tag->name] ) ? trim( $_POST[$tag->name] ) : '';

    if (!empty($value)) {
        $regex ='/^[0-9]{5}$/';
        if (!preg_match($regex ,$value)){
            $result->invalidate( $tag,  wpcf7_get_message( 'invalid_cap' )  ); 
        }
    }   
    return $result;
}

function cf7_kimera_form_tag( $scanned_tag, $replace )
{
    if ($replace){
        $cf_form=wpcf7_get_current_contact_form();
        $tag = new WPCF7_FormTag( $scanned_tag );
        $elements= $tag->get_option('cf7k_master_elements','',true);
        if (!empty($elements)) {
            array_map(function($e) use(&$scanned_tag){
                array_push($scanned_tag['options'],'class:cf7k-dependence-'.$e);
            } ,explode('|',$elements));
        }

        if ($tag->has_option('cf7k_onclick')) {
            array_push($scanned_tag['options'],'class:cf7k-refresh-click');
        }
        if ($tag->has_option('cf7k_onchange')) {
            array_push($scanned_tag['options'],'class:cf7k-refresh-change');
        }

        if ($tag->has_option('cf7k_pipe_value')) {
            foreach($tag->raw_values as $index=>$raw){
                $value_text=explode('|',$raw);
                if (sizeof($value_text)>1)
                    $scanned_tag['values'][$index]=$value_text[1];
            }
        }
        $url= $tag->get_option('cf7k_resource','',true);
        if (!empty($url)) {
            $url.='/lookup/';
            $filter=$tag->get_option( 'cf7k_filter', '', true );               
            $valid = true;
            if ($filter){
                foreach(  wp_parse_args($filter) as $f) {
                    $valid &= !empty($f) ;
                }
                if($valid)  $url = $url.'?'.$filter;        
            }
            if ($valid){
                $tmpCRM= crm_call($cf_form,$url);
                if (empty($tmpCRM['errormessage'])){
                    foreach( $tmpCRM['data'] as $rec) {
                        array_push($scanned_tag['values'],$rec['id']);
                        array_push($scanned_tag['labels'],$rec['text']);
                    }
                }
                else {
                    $scanned_tag['values']=array('');
                    $scanned_tag['labels']=array('ERROR:'.$tmpCRM['errormessage']);
                }
            }
        }   
    }
    return $scanned_tag;
}

function wpcf7_kimera_warning_CF7_Version(){
    echo '<div id="message" class="error cf7k-error">';
    echo '<p>' . __('<strong>Kimera CRM Add-on</strong> has detected some issues:', 'cf7-kimera-crm') . '</p>';
    echo '<ul>';
    if (!defined( 'WPCF7_VERSION' )){
        echo '<li>'.esc_html(__('Contact Form 7 is not installed!', 'cf7-kimera-crm')).'</li>';
    }
    else {
        echo '<li>'.esc_html( __('The version of Contact Form 7 installed is incompatible', 'cf7-kimera-crm')).'</li>';
        echo '<li>&nbsp;&nbsp;&nbsp;'.esc_html( __('required version', 'cf7-kimera-crm')).': <strong>'.CF7_KIMERA__CF7_REQUIRED_VERSION.'</strong></li>';
        echo '<li>&nbsp;&nbsp;&nbsp;'.esc_html( __('installed version', 'cf7-kimera-crm')).': <strong>'.WPCF7_VERSION.'</strong></li>';
    }
    echo '</ul>';
    echo '</div>';
}

function cf7k_msm_hook_scripts() {
    wp_localize_script( 'cf7msm', 'cf7k_msm_hook_post_data', $_POST);
    wp_add_inline_script( 'cf7msm', 'jQuery.extend(cf7msm_posted_data,cf7k_msm_hook_post_data);' );
}

//function cf7_kimera_html5_fallback() {
//    setup_postdata(null);
//    $content =get_the_content();
//    $html5_fallback=false;
//    $pattern = get_shortcode_regex( array('contact-form-7') );
//    $content = preg_replace_callback( "/$pattern/", function ( $m ) use(&$html5_fallback){
//        $attr = shortcode_parse_atts( $m[3] );
//        $attr = shortcode_atts(array('id' => 0),$attr, 'wpcf7');

//        $id = (int) $attr['id'];
//        if ($id>0){
//            $properties=null;    
//            if ( metadata_exists( 'post', $id, '_' . 'kimeracrm' ) ) {
//                $properties = get_post_meta( $id, '_' . 'kimeracrm', true );
//            } elseif ( metadata_exists( 'post', $id, 'kimeracrm' ) ) {
//                $properties = get_post_meta( $id, 'kimeracrm', true );
//            }
//            $html5_fallback=$properties['html5_fallback'] || $html5_fallback ;
//        }

//    }, $content ); 
//    if ($html5_fallback){
//        add_filter( 'wpcf7_support_html5_fallback', '__return_true' );
//    }
//}


class cf7k_fake_phpmailer{
    public function send() {
		return false;
	}
    public function isSMTP() {
		return false;
	}
    public function SetFrom() {
		return false;
	}
}

add_action( 'plugins_loaded', 'cf7kcrm' );
function cf7kcrm() {
    //add_filter( 'wpcf7_support_html5_fallback', '__return_true' );
    load_plugin_textdomain( 'cf7-kimera-crm',false,'cf7-kimera-crm/languages/' );
    if (   defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, CF7_KIMERA__CF7_REQUIRED_VERSION, '>=' ) ) {
        
        require_once('modules/kselect.php');
        require_once('modules/kcheckbox.php');
        require_once('modules/kfile.php');
        require_once('modules/hidden.php');
        require_once('modules/kpanel.php');
        require_once('modules/kbutton.php');
        require_once('modules/kdate.php');

        //add_action('wp_enqueue_scripts', 'cf7_kimera_html5_fallback', 10, 1);
        add_action('wpcf7_init', 'wpcf7_add_shortcode_ksubmit' ,9000);
        add_action('wpcf7_init', 'wpcf7_add_form_tag_kdatasource' );
        
        if (function_exists ('cf7msm_get')){
            add_action('wpcf7_contact_form', 'cf7k_msm_hook_scripts',20);
        }

        add_filter('wpcf7_form_tag','cf7_kimera_form_tag',10,2);
        add_filter('wpcf7_validate_text', 'custom_kimera_validation_filter', 20, 2 );
        add_filter('wpcf7_validate_text*', 'custom_kimera_validation_filter', 20, 2 );
        add_filter('wpcf7_messages', 'wpcf7_kimera_messages' );
        add_action('wpcf7_contact_form', 'kparser');
        add_filter('wpcf7_form_action_url', 'wpcf7_kcf7_form_action_url' );
        add_action('wpcf7_submit', 'cf7_kimera_submit', 10, 2 ); // TODO: ?????
        add_action('wpcf7_before_send_mail', 'cf7_kimera_before_send_mail');
    }
    else{
        if (is_admin()) {
            add_action('admin_notices', 'wpcf7_kimera_warning_CF7_Version');
        }
    }
}

?>