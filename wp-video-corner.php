<?php
/*
Plugin Name: Video Corner
Description: Dodaje shortcode [wideo autoplay=1 mute=1]url-youtube[/wideo] z opcją minimalizacji wideo do lewego dolnego rogu podczas przewijania strony.
Author: Sebastian Bort
Version: 1.0.0
*/
class Video_Corner {

        private $assets_included = false;

        public function __construct() {           
                add_shortcode('film', [$this, 'handle_shortcode']);        
        }

        private function assets_output() {
        
            if($this->assets_included)
                return '';
             
            $this->assets_included = true;    
            
            ob_start();
            ?>
                  <script>
                  jQuery(function($) { 
                      
                      var $window = $(window);
                      var $videoWrap = $('.video-wrap');
                      var $video = $('.video');
                      var $videoHeight = $video.outerHeight();
                      var $videoClosed = false;
                      var $positionFix = 300;
                      
                      $window.on('scroll',  function() {
                      
                          if($videoClosed) {
                                return false;
                          }
                        
                          var windowScrollTop = $window.scrollTop();
                          var videoBottom = $videoHeight + $videoWrap.offset().top - $positionFix;
                        
                          if(windowScrollTop > videoBottom) {
                              $videoWrap.height($videoHeight);
                              $video.addClass('stuck');
                          } 
                          else {
                              $videoWrap.height('auto');
                              $video.removeClass('stuck');
                          } 
                        
                      });
                      
                      $('.close-video').click(function(){
                              $video.removeClass('stuck');
                              $videoClosed = true;
                      });                       
                  });
                  </script>
                  
                  <style>            
                  @-webkit-keyframes fade-in-up {
                  	0% {
                  		opacity: 0;
                  	}
                  	100% {
                  		-webkit-transform: translateY(0);
                  		transform: translateY(0);
                  		opacity: 1;
                  	}
                  }
                  
                  @keyframes fade-in-up {
                  	0% {
                  		opacity: 0;
                  	}
                  	100% {
                  		-webkit-transform: translateY(0);
                  		transform: translateY(0);
                  		opacity: 1;
                  	}
                  }
                  
                  .video-wrap {
                  	text-align: center;
                  	margin: 30px 0;
                  }
                  
                  .video iframe {
                  	max-width: 100%;
                  	max-height: 100%;
                  }
                  
                  .video.stuck {
                  	position: fixed;
                  	bottom: 20px;
                  	left: 20px;
                  	z-index: 9999;
                  	-webkit-transform: translateY(100%);
                  	transform: translateY(100%);
                  	width: 260px;
                  	height: 145px;
                  	-webkit-animation: fade-in-up .75s ease forwards;
                  	animation: fade-in-up .75s ease forwards;
                  	border: 3px solid rgba(120, 120, 120, 0.1);
                  	box-shadow: 0px 0px 3px 3px rgba(0, 0, 0, 0.1);
                  }
                  
                  .video.stuck .close-video {
                  	display: flex;
                  }
                  
                  .close-video {
                  	position: absolute;
                  	height: 20px;
                  	width: 20px;
                  	top: -10px;
                  	right: -10px;
                  	z-index: 999;
                  	background: #000;
                  	border-radius: 50%;
                  	border: 1px solid #ccc;
                  	line-height: 0;
                  	display: flex;
                  	justify-content: center;
                  	align-items: center;
                  	font-size: 10px;
                  	cursor: pointer;
                  	color: #ccc;
                  	display: none;
                  }
                  
                  .close-video:hover {
                  	background-color: #212121;
                  }
                  </style>            
            
            <?php
            $style = ob_get_contents();
            ob_end_clean();
                                                        
            return $style;
        }      

        public function handle_shortcode($atts, $content) {
        
               if(empty($content))
                    return;
               
               if(!preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $content, $match))
                    return;
          
                $output = $this->assets_output();
               
                $autoplay = intval($atts['autoplay']);
                $mute = intval($atts['mute']);
          
                $body = '
                    <div class="video-wrap">
                      <div class="video">
                      <div class="close-video">x</div> 
                        <iframe allowFullScreen="allowFullScreen" src="https://www.youtube.com/embed/%s?ecver=1&amp;autoplay=%d&amp;mute=%d&amp;iv_load_policy=3&amp;rel=0&amp;showinfo=0&amp;yt:stretch=16:9&amp;autohide=1&amp;color=red&amp;width=560&amp;width=560" width="560" height="315" allowtransparency="true" frameborder="0"></iframe>
                      </div>
                    </div>';
              
                $output .= sprintf($body, $match[1], $autoplay, $mute);
                return $output;        
        }

}

new Video_Corner();

?>