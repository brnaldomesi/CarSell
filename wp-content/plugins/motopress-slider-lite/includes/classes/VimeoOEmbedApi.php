<?php
// https://developer.vimeo.com/apis/oembed
class MPSLVimeoOEmbedApi {    
    private $transientPrefix = 'mpsl-vimeo-img-';
    private $apiBaseUrl = 'https://vimeo.com/api/oembed.json';
    
    protected static $_instance;    
    
    private function __construct() {}
    private function __clone() {}
    
    public static function getInstance(){
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public static function getIdByUrl($urlOrId){
        preg_match('/^(?:(?:https?:\/\/|)(?:www\.|player\.|)vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(?P<idbyurl>\d+)(?:\/|\?|\#\w*|))|(?P<id>\d+)$/', $urlOrId, $matches);
        return isset($matches['id']) ? $matches['id'] : ( isset($matches['idbyurl']) ? $matches['idbyurl'] : '');        
    }
    
    public static function generateUrlById($id){
        return 'https://player.vimeo.com/video/' . $id;
    }
    
    public function getThumbnail($urlOrId){       
        $id = $this->getIdByUrl($urlOrId);      
        
        if (empty($id))
            return false;
  
        $thumbnail = get_transient($this->transientPrefix . $id);       
        if (false === $thumbnail) {
            $thumbnail = $this->getThumbnailByAPI($id);
            $oneDay = 60 * 60 * 24;
            set_transient($this->transientPrefix . $id, $thumbnail, $oneDay);
        }
        
        return $thumbnail;
    }        
    
    private function getThumbnailByApi($id){        
        $response = wp_remote_get(add_query_arg(array('url' => $this->generateUrlById($id)), $this->apiBaseUrl), array('timeout' => 15, 'sslverify' => false));
        
        if (is_wp_error($response)) 
            return false;
        
        $responseBody = wp_remote_retrieve_body($response);
        $data = json_decode($responseBody, true);                
        
        return (!is_null($data) && isset($data['thumbnail_url'])) ? $data['thumbnail_url'] : false;                
    }

}
