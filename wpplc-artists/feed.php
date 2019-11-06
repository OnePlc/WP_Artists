<?php

require_once ('../../../wp-load.php');

$iPostID = $_REQUEST['filter'];
$iPage = $_REQUEST['page'];

$aGalleryIDs = get_post_meta($iPostID, 'vdw_gallery_id', true);
if (is_array($aGalleryIDs)) {
    $iCount = 1;
    $iRuns = 0;
    echo 'images between '.(($iPage-1)*5).' and '.($iPage*5);
    foreach ($aGalleryIDs as $iImgID) {
        if($iCount >= (($iPage-1)*5) && $iCount < ($iPage*5)) {
            $sImg = wp_get_attachment_image_src($iImgID,'full');
            ?>
            <div class="infinite_scroll_item">
                <a href="<?= $sImg[0] ?>" data-lightbox="roadtrip" title="Bild">
                    <img src="<?= $sImg[0] ?>"/>
                </a>
            </div>
            <?php
            $iRuns++;
        }
        $iCount++;
    }
    echo $iRuns.' runs';
} else {
    echo 'no images';
}
?>

