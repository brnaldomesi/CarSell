(function($){
     $.fn.parallaxSlider=function(o){ 
            
        var options = {
            prevButton: $('.prevButton')
        ,   nextButton: $('.nextButton')
        ,   duration: 1000
        ,   autoSwitcher: true
        ,   autoSwitcherDelay: 7000
        ,   slider_navs: true
        ,   scrolling_description: false
        ,   slider_pagination: 'buttons_pagination'
        ,   animateLayout: 'zoom-fade-eff' //simple-fade-eff, zoom-fade-eff, slide-top-eff
        ,   parallaxEffect: 'parallax_effect_normal'
        ,   parallaxInvert: false
        ,   liteMode: false
        }
        $.extend(options, o);
        
        var 
            _this = $(this)
        ,   _window = $(window)
        ,   _document = $(document)
        ,   ImgIdCounter = 0
        ,   previewArray = []
        ,   isPreviewLoading = false
        ,   isPreviewAnimate = false
        ,   intervalSwitcher
        ,   parsedArray
        ,   parallax = true
        ,   parallaxType = 'parallax_normal'
        ,   _baseHeight
        ,   _thisOffsetTop = _this.offset().top
        ,   _thisHeight = _this.height()
        ,   _windowWidth = _window.width()
        ,   _windowHeight = _window.height()
        ,   itemLength = 0
        ,   bufferRatio 
        ;
    
        var
            img
        ,   imgBlock
        ,   mainImageHolder
        ,   primaryImageHolder
        ,   secondaryHolder
        ,   mainCaptionHolder
        ,   primaryCaption
        ,   secondaryCaption
        ,   mainCaptionHolderContainer
        ,   previewSpinner
        ,   parallaxPrevBtn
        ,   parallaxNextBtn
        ,   slidesCounterList
        ,   paralaxSliderPagination
        ;
    
        //--------------- Init ------------------//
        init();
        function init(){
            parsedArray = [];
            $('ul li', _this).each(
                function(){
                    parsedArray.push([$(this).attr('data-preview'), $(this).attr('data-thumb-url'), $(this).html()]);
                }
            )
                
            //  holder erase
            _this.html('');
            _this.addClass(options.animateLayout);
    
            //  preview holder build
            imgBlocksStructure = !options.liteMode ? "<div id='mainImageHolder'><div class='primaryHolder'><div class='imgBlock'></div></div><div class='secondaryHolder'><div class='imgBlock'></div></div></div>" : "<div id='mainImageHolder'><div class='primaryHolder'><img class='imgBlock' src='' alt=''/></div><div class='secondaryHolder'><img class='imgBlock' src='' alt=''/></div></div>"; 
            _this.append(imgBlocksStructure);
            mainImageHolder = $('#mainImageHolder');
            primaryImageHolder = $('> .primaryHolder', mainImageHolder);
            secondarImageHolder = $('> .secondaryHolder', mainImageHolder);
            imgBlock = $('.imgBlock', mainImageHolder);
    
             //  caption holder build
            _this.append("<div id='mainCaptionHolder'><div class='container'><div class='primaryCaption'></div><div class='secondaryCaption'></div></div></div>");
            mainCaptionHolder = $('#mainCaptionHolder');
            primaryCaption = $('.primaryCaption', mainCaptionHolder);
            secondaryCaption = $('.secondaryCaption', mainCaptionHolder);
            mainCaptionHolderContainer = $('>.container', mainCaptionHolder);
    
            //  controls build
            _this.append("<div class='controlBtn parallaxPrevBtn'><div class='innerBtn icon-angle-left'></div><div class='slidesCounter'></div></div><div class='controlBtn parallaxNextBtn'><div class='innerBtn icon-angle-right'></div><div class='slidesCounter'></div></div>");
            parallaxPrevBtn = $('.parallaxPrevBtn', _this);
            parallaxNextBtn = $('.parallaxNextBtn', _this);
    
            //  fullpreview pagination build
            _this.append("<div id='paralaxSliderPagination'><ul></ul></div>");
            paralaxSliderPagination = $('#paralaxSliderPagination');
    
            slidesCounterList = $('.slidesCounter', _this);
            
            //  preview loader build
            _this.append("<div id='previewSpinner'><span></span></div>");
            previewSpinner = $('#previewSpinner');
    
            _this.on("reBuild",
                function(e,d){
                    setBuilder(d);
                }
            )
    
            _this.on("switchNext",
                function(e){
                    nextSwither();
                }
            )
    
            _this.on("switchPrev",
                function(e){
                    prevSwither();
                }
            )
    
            setBuilder({'urlArray':parsedArray});
    
            if(!options.slider_navs){
                parallaxPrevBtn.remove();
                parallaxNextBtn.remove();
            }
            if(options.slider_pagination == 'none_pagination'){
                paralaxSliderPagination.remove();
            }
            
            if(options.liteMode) options.parallaxEffect = 'parallax_none';
            
            switch (options.parallaxEffect) {
                case 'parallax_effect_low':
                    if(!options.parallaxInvert){
                        parallaxType = 'parallax_normal';
                    } else {
                        parallaxType = 'parallax_invert';
                    }
                    
                    bufferRatio = 3;
                    
                    break
                    
                case 'parallax_effect_normal':
                    if(!options.parallaxInvert){
                        parallaxType = 'parallax_normal';
                    } else {
                        parallaxType = 'parallax_invert';
                    }
                    
                     bufferRatio = 2.25;
                     
                    break
                    
                case 'parallax_effect_high':
                    if(!options.parallaxInvert){
                        parallaxType = 'parallax_normal';
                    } else {
                        parallaxType = 'parallax_invert';
                    }
                    
                    bufferRatio = 1.5;
                    
                    break
                    
                case 'parallax_effect_fixed':
                    if($.browser.msie){
                        parallax = false;
                        imgBlock.css({backgroundAttachment:'fixed'});
                    } else {
                        parallaxType = 'parallax_fixes';
                    }
                    
                    bufferRatio = 1;
                    
                    break
                    
                default:
                    parallax = false;
                    parallaxType = 'parallax_none';
                    
                    break
            }
            
            _baseHeight = getBaseHeight();
    
            addEventsFunction();
            autoSwitcher();
        }
        
        //------------------------- set Builder -----------------------------//
        function setBuilder(dataObj){ 
            currIndex = 0;
            ImgIdCounter = 0;
            previewArray = [];
            previewArray = dataObj.urlArray;
            itemLength = previewArray.length;
            
            $(">ul", paralaxSliderPagination).empty();
            
            switch (options.slider_pagination) {
                case 'buttons_pagination':
                    paralaxSliderPagination.addClass('buttons_pagination');
                    for (var i = 0; i < itemLength; i++) {
                        $(">ul", paralaxSliderPagination).append("<li></li>");
                    };
                    
                    break
                
                case 'images_pagination':
                    paralaxSliderPagination.addClass('images_pagination');
                    for (var i = 0; i < itemLength; i++) {
                        $(">ul", paralaxSliderPagination).append("<li><img src='"+previewArray[i][1]+"'></li>");
                    };
                    
                     break
            }
    
            if(itemLength==1){
                paralaxSliderPagination.remove();
                parallaxPrevBtn.remove();
                parallaxNextBtn.remove();
            }
    
            imageSwitcher(0);
            addEventsPagination();
        }
    
        function autoSwitcher(){
            if(options.autoSwitcher){
                if(itemLength>1){
                    intervalSwitcher = setInterval(function(){
                        nextSwither();
                    }, options.autoSwitcherDelay);
                }
            }
        }
        
        //---------------  addEvents  ----------------------//
        function addEventsPagination(){
            $(">ul >li", paralaxSliderPagination).on("click",
                function(){
                    if((!isPreviewLoading) && (!isPreviewAnimate) && ($(this).index() !== ImgIdCounter)){
                        clearInterval(intervalSwitcher);
                        ImgIdCounter = $(this).index();
                        imageSwitcher(ImgIdCounter);
                    }
                }
            )
        }
        
        function addEventsFunction(){
            //--------------- controls events ----------------------//
            options.prevButton.on("click",
                function(){
                    clearInterval(intervalSwitcher);
                    prevSwither();
                }
            )
            options.nextButton.on("click",
                function(){
                    clearInterval(intervalSwitcher);
                    nextSwither(); 
                }
            )
            parallaxPrevBtn.on("click",
                function(){
                    clearInterval(intervalSwitcher);
                    prevSwither();
                }
            )
            parallaxNextBtn.on("click",
                function(){
                    clearInterval(intervalSwitcher);
                    nextSwither();
                }
            )
            
            //--------------- keyboard events ----------------------//
            _window.on("keydown",
                function(eventObject){
                    switch (eventObject.keyCode){
                        case 37:
                            clearInterval(intervalSwitcher);
                            prevSwither();
                        break
                        case 39:
                             clearInterval(intervalSwitcher);
                            nextSwither();
                        break
                    }
                }
            )
            
            //------------------ window scroll event -------------//
            $(window).on('scroll',
                function(){
                    mainScrollFunction();
                }
            ).trigger('scroll');
            
            //------------------ window resize event -------------//
            $(window).on("resize",
                function(){
                    mainResizeFunction();
                }
            )
        }
        
        //-----------------------------------------------------------------
        function prevSwither(){
            if(!isPreviewLoading && !isPreviewAnimate){
                if(ImgIdCounter > 0){
                    ImgIdCounter--;
                }else{
                    ImgIdCounter = itemLength-1;
                }
                    imageSwitcher(ImgIdCounter);
            }
        }
        function nextSwither(){
            if(!isPreviewLoading && !isPreviewAnimate){
                if(ImgIdCounter < itemLength-1){
                    ImgIdCounter++;
                }else{
                    ImgIdCounter = 0;
                }
                imageSwitcher(ImgIdCounter);
            }
        }
        
        //------------------------- main Swither ----------------------------//
        function imageSwitcher(currIndex){ 
            slidesCounterList.text((currIndex+1) + '/'+itemLength);
            $(">ul >li", paralaxSliderPagination).removeClass('active').eq(currIndex).addClass('active');
            
            objectCssTransition(primaryImageHolder, 0, 'ease');
            primaryImageHolder.addClass('animateState');
    
            primaryCaption.html(previewArray[currIndex][2]);
            objectCssTransition(primaryCaption, 0, 'ease');
            primaryCaption.addClass('animateState');
    
            isPreviewLoading = true;
            isPreviewAnimate = true;
            previewSpinner.css({display:'block'}).stop().fadeTo(300, 1);
            
            img = new Image();
            img.src = previewArray[currIndex][0];
            
            img.onload = function () {
                isPreviewLoading = false;
                previewSpinner.stop().fadeTo(300, 0, function(){ $(this).css({display:'none'}); })
                
                primaryImage = $('>.imgBlock', primaryImageHolder);
                if(!options.liteMode){ primaryImage.css('background-image', 'url(' + img.src + ')'); } else { primaryImage.attr('src', img.src); }
                objectResize(primaryImage, _windowWidth, _baseHeight);

                objectCssTransition(primaryImageHolder, options.duration, 'outCubic');
                primaryImageHolder.removeClass('animateState');
                objectCssTransition(secondarImageHolder, options.duration, 'outCubic');
                secondarImageHolder.addClass('animateState');
    
                objectCssTransition(primaryCaption, options.duration, 'outCubic');
                primaryCaption.removeClass('animateState');
                objectCssTransition(secondaryCaption, options.duration, 'outCubic');
                secondaryCaption.addClass('animateState');
    
                mainCaptionHolderContainer.height(primaryCaption.height());
    
                setTimeout(
                    function(){                        
                        secondarImage = $('>.imgBlock', secondarImageHolder);
                        if(!options.liteMode){ secondarImage.css('background-image', 'url(' + img.src + ')'); } else { secondarImage.attr('src', img.src); }      
                        objectResize(secondarImage, _windowWidth, _baseHeight);
                        
                        objectCssTransition(secondarImageHolder, 0, 'ease');
                        secondarImageHolder.removeClass('animateState');
                        
                        secondaryCaption.html(previewArray[currIndex][2]);
                        objectCssTransition(secondaryCaption, 0, 'ease');
                        secondaryCaption.removeClass('animateState');
    
                        isPreviewAnimate = false;
                    }, options.duration
                )
            }
        }
    
        //----------------------------------------------------//
        function objectCssTransition(obj, duration, ease){
            var durationValue;
    
            if(duration !== 0){
                durationValue = duration/1000;
            }else{
                durationValue = 0
            }
    
            switch(ease){
                case 'ease':
                        obj.css({"-webkit-transition":"all "+durationValue+"s ease", "-moz-transition":"all "+durationValue+"s ease", "-o-transition":"all "+durationValue+"s ease", "transition":"all "+durationValue+"s ease"});
                break;
                case 'outSine':
                    obj.css({"-webkit-transition":"all "+durationValue+"s cubic-bezier(0.470, 0.000, 0.745, 0.715)", "-moz-transition":"all "+durationValue+"s cubic-bezier(0.470, 0.000, 0.745, 0.715)", "-o-transition":"all "+durationValue+"s cubic-bezier(0.470, 0.000, 0.745, 0.715)", "transition":"all "+durationValue+"s cubic-bezier(0.470, 0.000, 0.745, 0.715)"});
                break;
                case 'outCubic':
                    obj.css({"-webkit-transition":"all "+durationValue+"s cubic-bezier(0.215, 0.610, 0.355, 1.000)", "-moz-transition":"all "+durationValue+"s cubic-bezier(0.215, 0.610, 0.355, 1.000)", "-o-transition":"all "+durationValue+"s cubic-bezier(0.215, 0.610, 0.355, 1.000)", "transition":"all "+durationValue+"s cubic-bezier(0.215, 0.610, 0.355, 1.000)"});
                break;
                case 'outExpo':
                    obj.css({"-webkit-transition":"all "+durationValue+"s cubic-bezier(0.190, 1.000, 0.220, 1.000)", "-moz-transition":"all "+durationValue+"s cubic-bezier(0.190, 1.000, 0.220, 1.000)", "-o-transition":"all "+durationValue+"s cubic-bezier(0.190, 1.000, 0.220, 1.000)", "transition":"all "+durationValue+"s cubic-bezier(0.190, 1.000, 0.220, 1.000)"});
                break;
                case 'outBack':
                    obj.css({"-webkit-transition":"all "+durationValue+"s cubic-bezier(0.175, 0.885, 0.320, 1.275)", "-moz-transition":"all "+durationValue+"s cubic-bezier(0.175, 0.885, 0.320, 1.275)", "-o-transition":"all "+durationValue+"s cubic-bezier(0.175, 0.885, 0.320, 1.275)", "transition":"all "+durationValue+"s cubic-bezier(0.175, 0.885, 0.320, 1.275)"});
                break;
            }
        }
        
        //------------------------ Object resize ----------------------------//
        function objectResize(obj, baseWidth, baseHeight )
        {
            var imageRatio,
                originalImgWidth,
                originalImgHeight,
                newImgWidth,
                newImgHeight,
                newImgTop,
                newImgLeft;
            
            originalImgWidth = img.width;
            originalImgHeight = img.height;
    
            imageRatio = originalImgHeight/originalImgWidth;
            containerRatio = baseHeight/baseWidth;
    
            if(containerRatio > imageRatio){
                newImgHeight = baseHeight;
                newImgWidth = Math.round( (newImgHeight*originalImgWidth) / originalImgHeight );   
            }else{
                newImgWidth = baseWidth;
                newImgHeight = Math.round( (newImgWidth*originalImgHeight) / originalImgWidth );
            }
    
            newImgLeft=-(newImgWidth-baseWidth)*.5;
            newImgTop= -(newImgHeight-baseHeight)*.5;
            
            if(!options.liteMode){ obj.css({width: '100%', height: newImgHeight, marginTop: newImgTop}); } else { obj.css({width: newImgWidth, height: newImgHeight, marginTop: newImgTop, marginLeft: newImgLeft}); }
        }
        
        //------------------- main window scroll function -------------------//
        function mainScrollFunction(){
             if(parallax || options.scrolling_description && !options.liteMode){
                
                 var _documentScrollTop
                 ,   startScrollTop
                 ,   endScrollTop
                 ;
     
                 _documentScrollTop = _document.scrollTop();
                 _thisOffsetTop = _this.offset().top;
     
                 startScrollTop = _documentScrollTop + _windowHeight;
                 endScrollTop = _documentScrollTop - _thisHeight;
     
                 if((startScrollTop > _thisOffsetTop) && (endScrollTop < _thisOffsetTop)){  
                     
                     y = _documentScrollTop - _thisOffsetTop;
                     
                     if(parallax){
                         if(!options.parallaxInvert) {
                             newPositionTop =  parseInt(y / bufferRatio);
                         } else {
                             if(_thisOffsetTop < Math.abs(_windowHeight-_thisHeight)){
                                 newPositionTop = -parseInt(y / bufferRatio) - parseInt(_thisOffsetTop / bufferRatio);
                             } else{
                                 newPositionTop = -parseInt(y / bufferRatio) - parseInt(_windowHeight / bufferRatio)
                             }
                         }
                         
                         mainImageHolder.css({ top: newPositionTop + 'px' });   
                     }
                     
                     if(options.scrolling_description){
                         description_opacity = (1-(y / _thisHeight)).toFixed(2); 
                         if(description_opacity < 1) mainCaptionHolder.css('opacity', description_opacity);
                         
                         description_offset = parseInt(y/2.5);
                         if(description_offset > 0) {
                             mainCaptionHolder.css('top', description_offset);
                         } else {
                             mainCaptionHolder.css('top', '0px');   
                         }
                     }
                 }
             }
        }
        
        //------------------- main window resize function -------------------//
        function mainResizeFunction(){
            _windowWidth = _window.width();
            _windowHeight = _window.height();
            _thisWidth = _this.width();
            _thisHeight = _this.height();
            _thisOffsetTop = _this.offset().top;
            
            _baseHeight = getBaseHeight();
            objectResize(imgBlock, _thisWidth, _baseHeight);
            mainScrollFunction();
        }

        //------------------- get heigth function -------------------//
        function getBaseHeight(){
            switch (parallaxType) {
                case 'parallax_normal':
                    if(_thisOffsetTop < (_windowHeight-_thisHeight)){
                       baseHeight = _thisHeight + parseInt(_thisOffsetTop/bufferRatio); 
                    } else {
                       baseHeight = _thisHeight + parseInt((_windowHeight - _thisHeight)/bufferRatio);
                    }                   
                    break
                    
                case 'parallax_invert':
                    if(_thisOffsetTop < Math.abs(_windowHeight-_thisHeight)){
                       baseHeight = _thisHeight + parseInt((_thisOffsetTop + _thisHeight)/bufferRatio); 
                    } else {
                       baseHeight = _thisHeight + parseInt((_windowHeight + _thisHeight)/bufferRatio);
                    }
                    break
                    
                case 'parallax_fixes':
                    if((_thisOffsetTop + _thisHeight) < _windowHeight){
                        baseHeight = _thisOffsetTop + _thisHeight;
                    } else {
                        baseHeight = _windowHeight;
                    }
                    break
                    
                case 'parallax_none':
                    baseHeight = _thisHeight;
                    break
            }
            
            return baseHeight;
        }
        
        function toRadians (angle) {
            return angle * (Math.PI / 180);
        }        
    }
})(jQuery)