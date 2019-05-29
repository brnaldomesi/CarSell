cherryIsotopeView_not_resizes = false;

jQuery(document).ready(function($)
{
    parallax_box = $('section.parallax-box');
    
    if(parallax_box.length > 0)
    {    
        ParallaxBox();
    }
    
    function ParallaxBox()
    {
        var windowSelector = $(window),
            documentSelector = $(document),
            windowWidth = windowSelector.width(),
            windowHeight = windowSelector.outerHeight(),
            liteMode = false,
            ieVersion = getInternetExplorerVersion();
            
        if(!device.mobile() && !device.tablet())
        {
			liteMode = false;
            
            if (ieVersion !=-1 && ieVersion < 10){
                liteMode = true;
            }
		}else{
			liteMode = true;
		}
        
        
        parallax_box.each(function()
        {
           parallaxBox($(this));
        });
        
        function parallaxBox(obj)
        {   
            var obj_bg = obj.find('.parallax-bg'),
                type = obj_bg.data('parallax-type'),
                obj_bg_inner,
                img,
                originalWidth = 0,
                originalHeight = 0,
                img_url = obj_bg.data('img-url'),
                bufferRatio = obj_bg.data('speed'),
                parallaxInvert = obj_bg.data('invert'),
                fullwidth = obj_bg.data('fullwidth'),
                mute = obj_bg.data('mute'),
                parallaxType = 'parallax_normal',
                objHeight = obj.height(),
                objOffsetTop = obj.offset().top,
                baseHeight = 0,
                parallax = true;
                notResize = false;
            
            switch (type)
            {
                case 'image':
                    loadImg(); 
                    
                    break
                    
                case 'video':
                    obj_bg_inner = $('.parallax_media', obj_bg);
                    
                    if(liteMode){
                        obj_bg_inner.remove();
                        loadImg();   
                    } else {
                        loadVideo();
                    }
                    
                    break
                
                case 'youtube':
                    obj_bg_inner = $('.parallax_youtube', obj_bg);
                    
                    if(liteMode){
                        obj_bg_inner.remove();
                        loadImg();   
                    } else {
                        loadYotubeVideo();
                    }
                    
                    break
                    
                case 'vimeo':
                    obj_bg_inner = $('.parallax_vimeo', obj_bg);
                    
                    if(liteMode){
                        obj_bg_inner.remove();
                        loadImg();   
                    } else {
                        loadVimeoVideo();
                    }
                    
                    break
            }
            
            function loadImg()
            {
                if(img_url)
                {
                    img = new Image();
                    img.src = img_url;
                    
                    img.onload = function ()
                    {
                        originalWidth = img.width;
                        originalHeight = img.height;
                        
                        imgBlocksStructure = !liteMode ? "<div class='parallax-img parallax-bg-inner'></div>" : "<img class='parallax-img parallax-bg-inner' src='' alt=''/>";
                        obj_bg.append(imgBlocksStructure);
                        obj_bg_inner = $('.parallax-img', obj_bg);
                        
                        if(!liteMode){
                            obj_bg_inner.css('background-image', 'url(' + (img_url) + ')');
                        } else { 
                            obj_bg_inner.attr('src', img_url);
                            bufferRatio = 'none';
                        }
                        
                        initParallax();
                    }
                }
            }
            
            function loadVideo()
            {   
                var videoElement = obj_bg_inner.get(0);
                    
                videoElement.load();
                videoElement.play();
                if(mute) videoElement.muted = true;
                
                originalWidth = videoElement.videoWidth;
                originalHeight = videoElement.videoHeight;
                    
                if(img_url)
                {
                    img = new Image();
                    img.src = img_url;
                    
                    img.onload = function ()
                    {
                        originalWidth = originalWidth==0 ? img.width : originalWidth;
                        originalHeight = originalHeight==0 ? img.height : originalHeight;
                        parallaxObjResize();
                        objectResize(obj_bg_inner, obj_bg.width(), baseHeight, originalWidth, originalHeight);
                    }
                }
                
                videoElement.onloadeddata = function()
                {
                    originalWidth = videoElement.videoWidth;
                    originalHeight = videoElement.videoHeight;
                    parallaxObjResize();
                    objectResize(obj_bg_inner, obj_bg.width(), baseHeight, originalWidth, originalHeight);
                }
                
                initParallax();
            }
            
            function loadYotubeVideo()
            {
                var youtube_id = obj_bg_inner.data('youtube-id');
                
                obj_bg_inner.attr('id', 'youtubeplayer'+obj.index());
                check_youtube_api(yotubeRady);
                
                function yotubeRady()
                {
                    function onPlayerReady(event)
                    {
                        event.target.playVideo();
                        if(mute) youtubePlayer.mute();
                    } 
                    
                    originalWidth = 16;
                    originalHeight = 9;
                    
                    initParallax();
    
                    var youtubePlayer = new YT.Player('youtubeplayer'+obj.index(), {
                        height: '0px',
                        width: '0px',
                        playerVars : {
                             'autoplay' : 1,
                             'showinfo' : 0,
                             'controls' : 0,
                             'loop' : 1,
                             'iv_load_policy' : 3,
                             'modestbranding ' : 1,
                             'disablekb' : 1, 
                             'enablejsapi' : 1,
                             'html5' : ieVersion != -1 ? 1 : 0,                                  
                             'playlist': youtube_id
                        },
                        videoId: youtube_id,
                        events: {
                            'onReady': onPlayerReady
                        }
                    });
                    
                    obj_bg_inner = $('#youtubeplayer'+obj.index(), obj_bg);
                }
            }
            
            function loadVimeoVideo()
            {
                check_vimeo_api(vimeoRady);
                
                function vimeoRady()
                {                    
                    var vimeo_id = obj_bg_inner.data('vimeo-id'),
                        vimeo_iframe,
                        vimeo_player,
                        vimeo_player_playstarted = false;
                        
                    base_vimeo_class = obj_bg_inner.attr('class');   
                    obj_bg.html('<iframe id="vimeoplayer'+obj.index()+'" class="'+base_vimeo_class+'" src="http://player.vimeo.com/video/'+vimeo_id+'?api=1&player_id=vimeoplayer'+obj.index()+';autoplay=1&amp;loop=1" frameborder="0"></iframe>');
                    obj_bg_inner = $('.parallax-bg-inner', obj_bg);
                    
                    vimeo_iframe = $('#vimeoplayer'+obj.index())[0];
                    vimeo_player = $f(vimeo_iframe);
                        
                    vimeo_player.addEvent('ready', function()
                    {
                        if(mute) vimeo_player.api('setVolume', 0);
                        vimeo_player.addEvent('playProgress', function()
                        {
                            vimeo_player.removeEvent('playProgress');
                            
                            originalWidth = 16;
                            originalHeight = 9;
                                
                            initParallax();
                        })
                    });   
                }
            }
            
            function initParallax()
            {
                notResize = false;
                
                if(!parallaxInvert){
                    parallaxType = 'parallax_normal';
                } else {
                    parallaxType = 'parallax_invert';
                }
                
                switch (bufferRatio)
                {
                    case 'low':
                        bufferRatio = 3;
                        
                        break
                        
                    case 'normal':
                        bufferRatio = 2.25;
                        
                        break
                        
                    case 'hight':
                        bufferRatio = 1.5;
                        
                        break
                        
                    case 'fixed':
                        if(ieVersion != -1 || getSafari()){
                            if(type != 'video'){
                                parallax = false;
                                notResize = true;
                                obj_bg_inner.css({backgroundAttachment:'fixed', width:'100%', height:'100%'});
                            } else {
                                parallaxType = 'parallax_normal';
                                bufferRatio = 2.25;
                            }
                        } else {
                            parallaxType = 'parallax_fixes';
                            bufferRatio = 1;
                        }
                        
                        break
                        
                    case 'none':
                        parallax = false;
                        parallaxType = 'parallax_none';
                        
                        break
                        
                    default:
                        bufferRatio = 2.25;
                        
                        if(bufferRatio > 5) bufferRatio = 5; 
                        if(bufferRatio <= 1){
                            if($.browser.msie){
                                parallax = false;
                                obj_bg_inner.css({backgroundAttachment:'fixed'});
                            } else {
                                parallaxType = 'parallax_fixes';
                                bufferRatio = 1;
                            }
                        }
                    
                        break
                }
                
                setTimeout(function(){obj_bg_inner.removeClass('load');}, 10);         
                
                windowSelector.resize(parallaxObjResize);
                documentSelector.resize(parallaxObjResize);
                parallaxObjResize();
                
                if(!liteMode) windowSelector.scroll(parallaxMove);
            }
            
            function parallaxObjResize()
            {
                windowWidth = windowSelector.width();
                windowHeight = windowSelector.outerHeight();
            
                objHeight = obj.height();
                
                if(fullwidth && !cherryIsotopeView_not_resizes)
                {
                    obj_bg.width(windowWidth);
                    obj_bg.css({'width' : windowWidth, 'margin-left' : Math.floor(windowWidth*-0.5), 'left' : '50%'});
                }
                               
                if(!notResize)
                {
                    baseHeight = getBaseHeight(parallaxType, objHeight, bufferRatio);
                    objectResize(obj_bg_inner, obj_bg.width(), baseHeight, originalWidth, originalHeight);
                }                
                
                if(!liteMode) parallaxMove();
            }
            
            function parallaxMove()
            {
                if(parallax && !liteMode)
                {    
                     var documentScrollTop,
                         startScrollTop,
                         endScrollTop;
         
                     objOffsetTop = obj.offset().top;
                     documentScrollTop = documentSelector.scrollTop();
         
                     startScrollTop = documentScrollTop + windowHeight;
                     endScrollTop = documentScrollTop - objHeight;
         
                     if((startScrollTop > objOffsetTop) && (endScrollTop < objOffsetTop))
                     {      
                         y = documentScrollTop - objOffsetTop;
                         
                         if(!parallaxInvert) {
                             newPositionTop =  parseInt(y / bufferRatio);
                         } else {
                             newPositionTop = -parseInt(y / bufferRatio) - parseInt(windowHeight / bufferRatio)
                         }
        
                         obj_bg_inner.css({"transform": "translate3d(0px, " + newPositionTop + "px, 0px)"});  
                     }
                 } 
            }
        }
        
        function objectResize(obj, baseWidth, baseHeight, originalWidth, originalHeight )
        {
            var imageRatio,
                originalWidth,
                originalHeight,
                newImgWidth,
                newImgHeight,
                newImgTop,
                newImgLeft;
    
            imageRatio = originalHeight/originalWidth;
            containerRatio = baseHeight/baseWidth;
    
            if(containerRatio > imageRatio){
                newImgHeight = baseHeight;
                newImgWidth = Math.round( (newImgHeight*originalWidth) / originalHeight );   
            }else{
                newImgWidth = baseWidth;
                newImgHeight = Math.round( (newImgWidth*originalHeight) / originalWidth );
            }
    
            newImgLeft=-(newImgWidth-baseWidth)*.5;
            newImgTop= -(newImgHeight-baseHeight)*.5;
            
            obj.css({width: newImgWidth, height: newImgHeight, marginTop: newImgTop, marginLeft: newImgLeft});
        }
        
        function getBaseHeight(parallaxType, objHeight, bufferRatio)
        {
            var newBaseHeight = 0;
            
            switch (parallaxType)
            {
                case 'parallax_normal':
                    newBaseHeight = objHeight + parseInt((windowHeight - objHeight)/bufferRatio);             
                    break
                    
                case 'parallax_invert':
                    newBaseHeight = objHeight + parseInt((windowHeight + objHeight)/bufferRatio);
                    break
                    
                case 'parallax_fixes':
                    newBaseHeight = windowHeight;
                    break
                    
                case 'parallax_none':
                    newBaseHeight = objHeight;
                    break
            }
            
            return newBaseHeight;
        }
        
        function getInternetExplorerVersion()
        {                        
            var rv = -1;
            if (navigator.appName == 'Microsoft Internet Explorer')
            {
                var ua = navigator.userAgent;
                var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
                if (re.exec(ua) != null)
                    rv = parseFloat( RegExp.$1 );
            }
            else if (navigator.appName == 'Netscape')
            {
                var ua = navigator.userAgent;
                var re  = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
                                
                if (re.exec(ua) != null)
                    rv = parseFloat( RegExp.$1 );                                      
            }
            
            return rv;
        }
        function getSafari(){
            var safari = false;
            if (navigator.userAgent.search("Safari") >= 0 && navigator.userAgent.search("Chrome") < 0) safari = true;
            return  safari;       
        }
    }
});