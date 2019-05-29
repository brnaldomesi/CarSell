<?php

//require_once dirname(__FILE__) . "/update_fixes/MPSL_Fix_Factory.php";

class MPSLSliderImporter {      
    
    private $mpslUploadsDir;
    private $mpslUploadsUrl;
    private $isVerbose = true;
    private $httpAuth = false;
    private $httpAuthLogin = '';
    private $httpAuthPassword = '';
    
    public function __construct() {
        global $mpsl_settings;
        $this->mpsl_settings = $mpsl_settings;
        $upload = wp_upload_dir();       
        $uploadDirPostfix = '/' .$mpsl_settings['plugin_name'] . '/import';
        $this->mpslUploadsDir = $upload['basedir'] . $uploadDirPostfix;
        $this->mpslUploadsUrl = $upload['baseurl'] . $uploadDirPostfix;                
    }
    
    public function importFromFile($path, $isVerbose = false){
        $this->isVerbose = $isVerbose;
        if (is_readable($path)) {
            $data = file_get_contents($path);
            if (false !== $data) {
                $this->importData($data);
            }
        } else {
            if($this->isVerbose){
                _e( 'Import file is not readable.', 'motopress-slider-lite') . "<br/>";
            }
            return false;
        }        
    }
    
    public function renderImportPage(){  
        global $mpsl_settings;
        echo '<h1>' . sprintf(__('Import %s', 'motopress-slider-lite'), $mpsl_settings['product_name']) . '</h1>';
        $step = isset( $_REQUEST['step'] ) && !empty( $_REQUEST['step'] ) ? (int)$_REQUEST['step'] : 1;
        switch($step) { 
            case '1' : 
                $this->renderImportForm();
                break;           
            case '2' :                
                if (isset($_REQUEST['mpsl_http_auth']) && $_REQUEST['mpsl_http_auth'] === 'true'){
                    $this->enableHttpAuth();
                }
                $this->processImport();
                break;
        }
    }
    
    public function enableHttpAuth(){        
        $this->httpAuth = true;
        if (isset($_REQUEST['mpsl_http_auth_login'])) $this->httpAuthLogin = $_REQUEST['mpsl_http_auth_login'];
        if (isset($_REQUEST['mpsl_http_auth_password'])) $this->httpAuthPassword = $_REQUEST['mpsl_http_auth_password'];
    }       
    
    private function renderImportForm(){       
        $bytes = wp_max_upload_size();
        $size  = size_format( $bytes );
        wp_enqueue_script('mpsl-importer', $this->mpsl_settings['plugin_dir_url'] . "js/importer.js", array('jquery'), $this->mpsl_settings['plugin_version'], true);
    ?>                
        <form action="<?php echo admin_url( 'admin.php?import=mpsl-importer&step=2'); ?>" method="post" enctype="multipart/form-data" >
            <p><?php printf(__('To import sliders select previously exported %s file and click the Import button.', 'motopress-slider-lite'), $this->mpsl_settings['product_name']); ?></p>
            <?php wp_nonce_field('mpsl-import', 'mpsl-import-nonce'); ?>
            <input type="hidden" name="mpsl-import-type" value="manual">
            <input type="hidden" name="max_file_size" value="<?php echo $bytes; ?>" />
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e('Import File: ', 'motopress-slider-lite'); ?>
                        </th>
                        <td>
                            <input type="file" name="mpsl-import-file" id="mpsl-import-file" required="required">
                            <small><?php printf( __( 'Maximum size: %s', 'motopress-slider-lite' ), $size ); ?></small>
                        </td>
                    </tr>                    
                    <tr>
                        <th scope="row">                            
                        </th>
                        <td>
                            <input type="checkbox" name="mpsl_http_auth" id="mpsl_http_auth" value="true" autocomplete="off">
                            <label for="mpsl_http_auth"><?php _e('Enable HTTP authentication', 'motopress-slider-lite');?></label>
                        </td>
                    </tr>   
                    <tr class="need-mpsl_http_auth" style="display: none;">
                        <th scope="row">
                            <?php _e('Login:', 'motopress-slider-lite');?>
                        </th>
                        <td>
                            <input type="text" name="mpsl_http_auth_login" id="mpsl_http_auth_login" disabled="disabled" autocomplete="off"/>
                        </td>
                    </tr>   
                    <tr class="need-mpsl_http_auth" style="display: none;">
                        <th scope="row">
                            <?php _e('Password:', 'motopress-slider-lite');?>
                        </th>
                        <td>
                            <input type="password" name="mpsl_http_auth_password" id="mpsl_http_auth_password" disabled="disabled" autocomplete="off"/>
                        </td>
                    </tr>   
                </tbody>
            </table>           
            <button type="submit" class="button-primary"><?php esc_attr_e( 'Import', 'motopress-slider-lite' ); ?></button>
            <br/>            
        </form>      
    <?php
    }

	private function processImport() {
		$this->isVerbose = true;
		if (isset($_POST['mpsl-import-type']) && $_POST['mpsl-import-type'] === 'manual') {
			if (check_admin_referer('mpsl-import', 'mpsl-import-nonce')) {
				$action = isset($_GET['action']) && $_GET['action'] ? $_GET['action'] : 'file';
				$data = false;

				if ($action === 'file') {
					if ($_FILES['mpsl-import-file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['mpsl-import-file']['tmp_name'])) {
						$data = file_get_contents($_FILES['mpsl-import-file']['tmp_name']);
					}

				} elseif ($action === 'preset') {
					$presetId = isset($_POST['preset_id']) ? $_POST['preset_id'] : false;
					if ($presetId) {
						$presetFile = $this->mpsl_settings['plugin_dir_path'] . "defaults/slider-presets/{$presetId}.json";
						if (file_exists($presetFile)) {
							$data = file_get_contents($presetFile);
						}
					}
				}

				if (false !== $data) {
					$importResult = $this->importData($data, $action);

					if ($importResult && $action === 'preset') {
                        $menuUrl = menu_page_url($this->mpsl_settings['plugin_name'], false);
                        $sliderUrl = add_query_arg(array('view'=>'slider', 'id'=> $importResult), $menuUrl);

						echo '<br/><a href="' . $sliderUrl . '" class="button button-primary">' . __('Go to slider settings page', 'motopress-slider-lite') . '</a>';
//						wp_safe_redirect($sliderUrl);
					}
				}
			}
		}
	}

    private function importData($data, $action = 'file'){
        
        if( empty( $data ) )
            return false;

        $slider = null;
        $import_data  = json_decode( $data, true );

        if (! is_array($import_data)) {            
            if ($this->isVerbose)  
                _e( 'Import data is not valid.', 'motopress-slider-lite') . "<br/>";
            return false;
        }

	    global $wpdb;

        if (isset($import_data['sliders'])) {
	        global $mpslAdmin;

            $importedMedia = $this->importUploads($import_data['uploads']);

            if (!empty($importedMedia)) {
                if ($this->isVerbose)
                    echo '<br/>' . __('Uploads imported', 'motopress-slider-lite') . '<br/><hr/><br/>';
            }

            // replace placeholders in data with new attachment ids
            $this->updateAttachmentIds($import_data, $importedMedia);

	        // Presets
	        $layerPresetsObj = MPSLLayerPresetOptions::getInstance();
	        $presetsExists = isset($import_data['presets']) && is_array($import_data['presets']) && count($import_data['presets']);
			$newPresetClasses = $presetsExists ? $layerPresetsObj->loadNewPresets($import_data['presets']) : array();

//	        if ($isNeed200Fix = version_compare($import_data['info']['mpsl-ver'], '2.0.0', '<')) {
//		        /** @var MPSL_Fix_v2_0_0 $fixer200 */
//		        $fixer200 = MPSL_Fix_Factory::getFixer('2.0.0');
//	        }

            foreach($import_data['sliders'] as $slider_data) {
//                $slider = new MPSLSliderOptions();
                $slider = new MPSLSliderOptions(array(
			        'grouped' => false,
			        'options' => $slider_data['options']
		        ));
//                $slider->overrideOptions($slider_data['options'], false);
                $slider->makeAliasUnique();
                $sliderId = $slider->create(false);
                if (false !== $sliderId) {
                    if ($this->isVerbose) {
                        printf(__('Slider "%s" options imported.', 'motopress-slider-lite'), $slider->getAlias());
                        echo '<br/>';
                    }
                    foreach($slider_data['slides'] as $slide_data) {
	                    $layers = is_array($slide_data['layers']) ? $slide_data['layers'] : array();

	                    // Fix v2.0.0
	                    /*if ($isNeed200Fix) {
							$layers = $fixer200->fixLayers($layers);
	                    }*/

                        $slide = new MPSLSlideOptions(); 
                        $slide->overrideOptions($slide_data['options'], false);
                        $slide->prepareLayersForImport($layers, $newPresetClasses);
                        $slide->setLayers($layers);
                        $result = $slide->import($sliderId);
                        if (false !== $result) {
                            if ($this->isVerbose) {
                                printf(__('Slide "%s" of slider "%s" imported.', 'motopress-slider-lite'), $slide->getTitle(), $slider->getAlias());
                                echo '<br/>';
                            }                                
                        } else {                            
                            if ($this->isVerbose) {
                                printf(__('Slide "%s" of slider "%s" is not imported. Error: %s', 'motopress-slider-lite') , $slide->getTitle(), $slider->getAlias(), $wpdb->last_error);
                                echo '<br/>';
                            }                                
                        }
                    }
                } else {                    
                    if ($this->isVerbose) {
                        printf(__('Slider "%s" is not imported. Error: %s', 'motopress-slider-lite'), $slider->getAlias(), $wpdb->last_error);
                        echo '<br/>';
                    }
                }                         
            }

	        // Update presets
	        $presetsUpdateResult = $layerPresetsObj->update();
	        $layerPresetsObj->updatePrivateStyles();
			if ($presetsUpdateResult) {
				if ($this->isVerbose) {
					print(__('Presets imported.', 'motopress-slider-lite'));
					echo '<br/>';
				}
			} else {
				if ($this->isVerbose) {
					printf(__('Presets not imported. Error: %s', 'motopress-slider-lite'), $wpdb->last_error);
					echo '<br/>';
				}
			}
        }

	    /** @todo: Check condition */
        if ($action === 'preset') {
	        if (!is_null($slider)) {
		        return $slider->getId();
	        } else {
				printf(__('Slider preset import error: %s', 'motopress-slider-lite'), $wpdb->last_error);
				echo '<br/>';
		        return false;
	        }
        }

        return true;
    }
    
    private function importUploads($mediaForImport){
        $importedMedia = array();
        foreach($mediaForImport as $id => $mediaUrl) {
            $newId = $this->downloadMedia($mediaUrl['value']); 
            if (false !== $newId) {
                $importedMedia[$id] = $newId;
            }
        }
        return $importedMedia;
    }
    
    /**
    * Change id for media attachments in exported data.
    *
    * @param array $data exported data array.
    * @param array $importedMedia array of old ids and urls of attachments.
    * @return void
    */
    private function updateAttachmentIds(&$data, $importedMedia){
        foreach ($data['sliders'] as &$slider) {
            foreach($slider['slides'] as &$slide) {
                foreach ($slide['options'] as &$option) {
                    if (is_array($option) && isset($option['need_update'])){                        
                        $option = $this->getNewImportedMediaId($option, $importedMedia);                        
                    }
                }
                foreach ($slide['layers'] as &$layer) {
                    foreach($layer as &$layerOption) {
                        if (is_array($layerOption) && isset($layerOption['need_update'])) {
                            $layerOption = $this->getNewImportedMediaId($layerOption, $importedMedia);
                        }
                    }
                }
            }
        }
    }
    
    private function getNewImportedMediaId($option, $importedMedia){
        $oldId = $option['old_value'];
        return isset($importedMedia[$oldId]) && false !== $importedMedia[$oldId] && !empty($importedMedia[$oldId]) ? $importedMedia[$oldId] : '';
    }
    
    private function downloadMedia($url) {
        
        if( ! isset( $url ) || empty( $url ) )  return false;

        $file_name = basename( $url );

        $upload = $this->fetchRemoteFile( $url);

        if( is_wp_error( $upload ) || $upload['error'] ) {
            if ($this->isVerbose) {
                printf(__('Failed to import media "%s" : ', 'motopress-slider-lite'), $url);
                if (is_wp_error($upload)){
                    foreach ($upload->get_error_messages() as $error) {
                        echo $error;
                        echo '<br/>';
                    }                    
                } else {
                    echo $upload['error'];
                    echo '<br/>';
                }                                            
            }
            return false;            
        }

        // Prepare an array of post data for the attachment.
        $attachment = array(
            'guid'           => '', 
            'post_mime_type' => '',
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $upload['file'] ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        $info = wp_check_filetype($upload['file']);
        $attachment['post_mime_type'] = $info['type'];
        $attachment['guid'] = $upload['url'];
        $attachment_id = wp_insert_attachment($attachment, $upload['file']);
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $upload['file']));
        
        if ($this->isVerbose) {
            printf(__('<a href="%s" target="_blank" >%s</a> imported successfully.', 'motopress-slider-lite'), $upload['url'], $file_name) . "<br />";
            echo '<br/>';
        }
        
        return $attachment_id;
    }
    
    private function fetchRemoteFile( $url ) {
        $file_name = basename( $url );
        $upload = $this->createFileInUploads( $file_name );

        if ( $upload['error'] )
                return new WP_Error( 'upload_dir_error', $upload['error'] );                                                      
            
        $requestArgs = array(
            'timeout' => 60,
            'sslverify' => false,                            
        );
        
        if ($this->httpAuth) {
            $requestArgs['headers'] = array(
                'Authorization' => 'Basic ' . base64_encode($this->httpAuthLogin . ':' . $this->httpAuthPassword)
            );
        }
        
        $response = wp_remote_request($url, $requestArgs);
        
        if (is_wp_error($response)) {
            @unlink($upload['file']);
            return $response;
        }
        
        // Check headers
        $headers = wp_remote_retrieve_headers($response);                   

        if (wp_remote_retrieve_response_code($response) != '200' ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', sprintf( __('Remote server returned error response %1$d %2$s', 'motopress-slider-lite'), esc_html( wp_remote_retrieve_response_code($response) ), get_status_header_desc( wp_remote_retrieve_response_code($response) ) ) );
        }
        
        // Write file
        $out_fp = fopen($upload['file'], 'w');
        
        if ( !$out_fp ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Unable to write to file.', 'motopress-slider-lite') );
        }            
	fwrite( $out_fp,  wp_remote_retrieve_body( $response ) );
	fclose($out_fp);
	clearstatcache();

        $filesize = filesize( $upload['file'] );
        if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {            
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Remote file is incorrect size', 'motopress-slider-lite') );
        }

        if ( 0 == $filesize ) {
            @unlink( $upload['file'] );
            return new WP_Error( 'import_file_error', __('Zero size file downloaded', 'motopress-slider-lite') );
        }                        

        return $upload;
    }
    
    private function createFileInUploads( $name ) {
        if ( empty( $name ) )
            return array('error' => __( 'Empty filename', 'motopress-slider-lite' ));

        $wp_filetype = wp_check_filetype( $name );
        if ( ! $wp_filetype['ext'] && ! current_user_can( 'unfiltered_upload' ) )
            return array('error' => __('Invalid file type', 'motopress-slider-lite'));

        $upload = wp_upload_dir();
        if ( $upload['error'] !== false )
            return $upload;

        $upload_path = $this->mpslUploadsDir;
        $filename = wp_unique_filename( $upload_path, $name );
        $new_file = $upload_path . "/$filename";            

        if ( !wp_mkdir_p($upload_path) ) {            
            $message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?', 'motopress-slider-lite' ), dirname($upload_path) );
            return array( 'error' => $message );
        }

        $isFileWritten = $this->writeFile($new_file);
        if (!$isFileWritten) 
            return array( 'error' => sprintf( __( 'Could not write file %s', 'motopress-slider-lite' ), $new_file ) );
        
        $url = $this->mpslUploadsUrl . '/' . $filename;

        return array( 'file' => $new_file, 'url' => $url, 'error' => false );
    }    
    
    private function writeFile($file, $content = ''){
        $fileResource = @ fopen( $file, 'wb' );
        if ( ! $fileResource )
            return false;
            
        @fwrite( $fileResource, '' );
        fclose( $fileResource );
        clearstatcache();

        // Set correct file permissions
        $stat = @ stat( dirname( $file ) );
        $perms = $stat['mode'] & 0007777;
        $perms = $perms & 0000666;
        @chmod( $file, $perms );
        clearstatcache();
        
        return true;
    }
        
}