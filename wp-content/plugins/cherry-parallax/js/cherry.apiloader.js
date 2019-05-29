var _window = $(window),
    youtube_api_load = false,
    youtube_api_loaded = false,
    vimeo_api_load = false,
    vimeo_api_loaded = false;

function check_youtube_api(callback)
{
    if(!youtube_api_load && !youtube_api_loaded)
    {
        youtube_api_load = true;
        $.getScript( 'https://www.youtube.com/iframe_api', function( ) {
            _window.on('youtubeApiReadyEvent', function(){
                callback();
            })
        })
    } else if (youtube_api_load && !youtube_api_loaded)
    {
        _window.on('youtubeApiReadyEvent', function(){
            callback();
        })
    } else if (youtube_api_load && youtube_api_loaded)
    {
        callback();
    } 
}

function onYouTubeIframeAPIReady() {
    youtube_api_loaded = true;
    _window.trigger('youtubeApiReadyEvent');
}

function check_vimeo_api(callback)
{
    if(!vimeo_api_load && !vimeo_api_loaded)
    {
        vimeo_api_load = true;
        $.getScript( '//f.vimeocdn.com/js/froogaloop2.min.js', function( ) {
            vimeo_api_loaded = true;
            _window.trigger('vimeoApiReadyEvent');
            callback();
        })
    } else if(vimeo_api_load && !vimeo_api_loaded)
    {
        _window.on('vimeoApiReadyEvent', function(){
             callback();
        })
    }  else if(vimeo_api_load && vimeo_api_loaded)
    {
        callback();
    }
}