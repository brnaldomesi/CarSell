<?php
add_action( 'wp_ajax_motopress_tutorials', 'motopress_tutorials_callback' );
function motopress_tutorials_callback() {
    $requirements = new MPCERequirements();
    if ($requirements->getCurl()) {
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => 'http://gdata.youtube.com/feeds/api/playlists/PLbDImkyrISyLl3bdLk4nOLZtS7EqxK646/?v=2&alt=json&feature=plcp',
            CURLOPT_RETURNTRANSFER => true,
        );
        curl_setopt_array($ch, $options);
        $jsonData = curl_exec($ch);
        curl_close($ch);
    } else {
        $jsonData = file_get_contents($url);
    }

    $firstFrame = null;
    $response = 'An internal error occurred. Try again later.';
    $scriptus = '';
    $feedCounter = 0;

    $data = @json_decode($jsonData);
    
    if ( $data !== null && isset($data->feed) && isset($data->feed->entry) ) {
        $feed = $data->feed->entry;
        $feedCounter = count($feed);
    }
    if ( $feedCounter ) {
        $response = "<div class=\"motopress-tutorials-wrapper\">";

        for ($i=0; $i < $feedCounter; $i++) {
            if ($i == 0) {
                $firstFrame = $feed[$i]->{'media$group'}->{'yt$videoid'}->{'$t'};
                $response .= "<div id=\"motopress-framewrapper\">
                                <iframe id=\"motopress-tutorials-iframe\" width=\"100%\" height=\"100%\" src=\"//www.youtube.com/embed/". $feed[$i]->{'media$group'}->{'yt$videoid'}->{'$t'} ."?version=3&enablejsapi=1&theme=light&rel=0&hd=1&showinfo=0\" frameborder=\"0\" allowfullscreen></iframe>
                              </div>";
                $response .= '<div class="tutorials-thumbnails-wrapper">';
                $response .= '<div class="tutorials-thumbnails-container">';
                $response .= '<dl class="motopress-tutorials-thumbnail active-thumbnail" data-video-id="'. $feed[$i]->{'media$group'}->{'yt$videoid'}->{'$t'} .'">';
                $response .= "<dt data-src=\"". $feed[$i]->{'media$group'}->{'media$thumbnail'}[2]->url ."\"></dt>";
                $response .= '<dd>'. $feed[$i]->title->{'$t'}.'</dd>';
                $response .= '</dl>';
            } else {
                $response .= '<dl class="motopress-tutorials-thumbnail" data-video-id="'. $feed[$i]->{'media$group'}->{'yt$videoid'}->{'$t'} .'">';
                $response .= "<dt data-src=\"". $feed[$i]->{'media$group'}->{'media$thumbnail'}[2]->url ."\"></dt>";
                $response .= '<dd>'. $feed[$i]->title->{'$t'}.'</dd>';
                $response .= '</dl>';
            }
        }

        $response .= '</div>';
        $response .= '</div>';
        $response .= '</div>';

        $scriptus = "
            <script>
                (function() {
                    var timer = null,
                        player = null,
                        frameForOpen = '<iframe id=\"motopress-tutorials-iframe\" width=\"100%\" height=\"100%\" src=\"//www.youtube.com/embed/". $firstFrame ."?version=3&enablejsapi=1&theme=light&rel=0hd=1&showinfo=0\" frameborder=\"0\" allowfullscreen></iframe>',
                        modalBlock = jQuery('#motopress-tutorials-modal'),
                        isShown = function() {
                            if ( modalBlock.is(':visible') ) {
                                return true;
                            }
                            return false;
                        },
                        setSize = function() {
                            var modalHeight = modalBlock.height(),
                                modalBodyHeight = modalHeight - 78,
                                vidWidth = modalBlock.find('.modal-body').outerWidth(),
                                thumbsWrapper = modalBlock.find('.tutorials-thumbnails-wrapper'),
                                tumbsWidth = thumbsWrapper.outerWidth(),
                                calculatedWidth = vidWidth - ( tumbsWidth + 45 ),
                                iframeElement = modalBlock.find('iframe');

                            iframeElement.height( modalBodyHeight );
                            thumbsWrapper.height( modalBodyHeight );
                            iframeElement.width( calculatedWidth );
                        };

                    jQuery(document).on('keyup', function onEscHandler(e) {
                        if ( isShown() ) {
                            if (e.which === 27) {
                                jQuery('.massive-modal-close').click();
                            }
                        }
                    });

                    jQuery(window).resize(function() {
                        if ( isShown() ) {
                            timer && clearTimeout( timer );
                            timer = setTimeout(function() {
                                setSize();
                            }, 30);
                        }
                    });

                    setSize();

                    jQuery('#motopress-tutorials-modal').on('click', 'dl', function() {
                        var thumbURL = jQuery(this).attr('data-video-id'),
                            allThumbs = jQuery('.motopress-tutorials-thumbnail'),
                            frameToReplace = '<iframe id=\"motopress-tutorials-iframe\" width=\"100%\" height=\"100%\" src=\"//www.youtube.com/embed/' + thumbURL + '?version=3&enablejsapi=1=1&theme=light&rel=0&autoplay=1&hd=1&showinfo=0\" frameborder=\"0\" allowfullscreen></iframe>';

                        allThumbs.removeClass('active-thumbnail');
                        jQuery(this).addClass('active-thumbnail');

                        jQuery('#motopress-tutorials-modal').find('#motopress-framewrapper').html( frameToReplace );
                        setSize();
                    });

                    jQuery('.tutorials-thumbnails-container').find('dt').each(function() {
                        var bgImg = jQuery(this).attr('data-src');
                        jQuery(this).css('background-image', 'url(\"' + bgImg + '\")');
                    });

                    modalBlock.on('show.bs.modal', function () {
                        var allThumbs = jQuery('.motopress-tutorials-thumbnail'),
                            iFrame = jQuery('#motopress-framewrapper');

                        iFrame.html( frameForOpen );

                        allThumbs.removeClass('active-thumbnail');
                        allThumbs.filter( ':first' ).addClass('active-thumbnail');

                        timer && clearTimeout( timer );
                        timer = setTimeout(function() {
                            setSize();
                        }, 200);
                    });

                })();
            </script>";
        $response = $response . $scriptus;
    }

    echo $response;
    die();
}
