<?php
// https://developers.google.com/youtube/v3/
class MPSLYoutubeDataApi {
    private $transientPrefix = 'mpsl-youtube-img-';
    private $apiBaseUrl = 'https://www.googleapis.com/youtube/v3/videos';
    private $apiKey = 'QUl6YVN5REVmOXhteU5uWS0zckhvRW9vM3pWOHRmRWY3My1IVzdv';
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
        preg_match('/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))(?P<idbyurl>[^\?&\"\'>]+)|(?P<id>[A-za-z0-9_-]{11})/', $urlOrId, $matches);
        return isset($matches['id']) ? $matches['id'] : ( isset($matches['idbyurl']) ? $matches['idbyurl'] : '' );
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
        $response = wp_remote_get(add_query_arg(array('key' => base64_decode($this->apiKey), 'part' => 'snippet', 'fields' => 'items/snippet/thumbnails', 'id' => $id), $this->apiBaseUrl), array('timeout' => 15, 'sslverify' => false));
        if (is_wp_error($response))
            return false;
        
        $responseBody = wp_remote_retrieve_body($response);
        $data = json_decode($responseBody, true);   
        
        if (is_null($data) || isset($data['error']) || !isset($data['items'][0]['snippet']['thumbnails'])) 
            return false;
        
        $thumbnails = $data['items'][0]['snippet']['thumbnails'];
        if (isset($thumbnails['maxres']) && isset($thumbnails['maxres']['url'])) {
            return $thumbnails['maxres']['url'];
        } else if (isset($thumbnails['standart']) && isset($thumbnails['standart']['url'])) {
            return $thumbnails['standart']['url'];
        } else if (isset($thumbnails['high']) && isset($thumbnails['high']['url'])) {
            return $thumbnails['high']['url'];
        }else if (isset($thumbnails['medium']) && isset($thumbnails['medium']['url'])) {
            return $thumbnails['medium']['url'];
        } else if(isset($thumbnails['default']) && isset($thumbnails['default']['url'])) {
            return $thumbnails['default']['url'];
        } else {
            return false;
        }
    }
      
}
