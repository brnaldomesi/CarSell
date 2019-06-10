<?php
function motopressCEGetAttachmentThumbnail() {
    require_once dirname(__FILE__).'/../verifyNonce.php';
    require_once dirname(__FILE__).'/../settings.php';
    require_once dirname(__FILE__).'/../access.php';
    require_once dirname(__FILE__).'/../functions.php';
    require_once dirname(__FILE__).'/../getLanguageDict.php';

    global $motopressCELang;

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int) trim($_POST['id']);
        $attachment = get_post($id);
        if (!empty($attachment) && $attachment->post_type === 'attachment') {
            if (wp_attachment_is_image($id)) {

                $srcMedium = wp_get_attachment_image_src($id, 'medium');
                $srcFull = wp_get_attachment_image_src($id, 'full');

                if (isset($srcMedium[0]) && !empty($srcMedium[0])
                        && isset($srcFull[0]) && !empty($srcFull[0])) {
                    $attachmentImageSrc = array();
                    $attachmentImageSrc['medium'] = $srcMedium[0];
                    $attachmentImageSrc['full'] = $srcFull[0];
                    wp_send_json($attachmentImageSrc);
                } else {
                    motopressCESetError($motopressCELang->CEAttachmentImageSrc);
                }
            } else {
                motopressCESetError($motopressCELang->CEAttachmentNotImage);
            }
        } else {
            motopressCESetError($motopressCELang->CEAttachmentEmpty);
        }
    } else {
        motopressCESetError($motopressCELang->CEAttachmentThumbnailError);
    }
    exit;
}