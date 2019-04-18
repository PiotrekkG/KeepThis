<?php
if (isset($_POST['url']) && isset($_POST['description']) && isset($_POST['rate']) && isset($_POST['favorite'])) {
    $url = $_POST['url'];
    //foreach(explode("\n",$_POST['url']) as $url)
    {
        $url = trim($url);
        // Remove all illegal characters from a url
        $url = filter_var($url, FILTER_SANITIZE_URL);
        // Validate url
        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            //$info .= "$url is a valid URL";

            $url = $conn->real_escape_string($url);
            $description = $conn->real_escape_string($_POST['description']);
            $pageTitle = getTitle($url);

            $rate = (int)$_POST['rate'];
            if (!($rate <= 5 && $rate >= 0)) {
                $rate = 0;
            }
                    
            $favorite = (int)$_POST['favorite'];
            if ($favorite != 0 && $favorite != 1) {
                $favorite = 0;
            }
                    
            if ($conn->query("INSERT INTO `linkList` (`refUserId`,`refBookmarkId`,`url`,`pageTitle`,`description`,`rate`,`favorite`) VALUES ($userId, NULL, '$url', '$pageTitle', '$description', $rate, $favorite)")) {
                $addingInfo = $lang['modules']['add']['addingSuccess'];
                $addSuccessfully = true;
            } else {
                $addingInfo = $lang['modules']['add']['addingFail'];
            }
        } else {
            $addingInfo = $lang['modules']['add']['notValidUrl'];
        }
    }
}
?>
<div class="mainElement ui text container rounded bordered segment">
    <?=isset($addingInfo)?'<div class="ui '.(isset($addSuccessfully) && $addSuccessfully == true?'success':'error').' message">'.$addingInfo.'</div>':'';?>
    <form action="<?=createLink('add');?>" method="post" class="ui form">
        <div class="ui fluid labeled input menu">
            <div class="ui label">http://</div>
            <input type="text" name="url" placeholder="Paste URL here">
        </div>
        <div class="ui grid" style="margin-bottom:0px;">
            <div class="three wide column">
                <label><?=$lang['modules']['add']['TextRate'];?></label>
                <input hidden type="hidden" name="rate" value="0"><div id="rate" class="ui huge star rating" data-rating="0" data-max-rating="5"></div>
            </div>
            <div class="two wide column">
                <label><?=$lang['modules']['add']['TextFavorite'];?></label>
                <input hidden type="hidden" name="favorite" value="0"><div id="favorite" class="ui huge heart rating" data-rating="0" data-max-rating="1"></div>
            </div>
            <div class="eleven wide column">
                <div class="ui fluid labeled input menu">
                    <div class="ui label"><?=$lang['modules']['add']['TextDescription'];?></div>
                    <input type="text" name="description" placeholder="<?=$lang['modules']['add']['TextDescriptionPlaceholder'];?>">
                </div>
            </div>
        </div>
<?php //<!--<textarea name="url" style="width:550px;height:150px;"></textarea>--> ?>
        <button class="fluid ui button" type="submit"><?=$lang['modules']['add']['TextSubmit'];?></button>
    </form>
</div>