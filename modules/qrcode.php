<?php
if (isset($_GET['showId'])) {
    $showId = (int)$_GET['showId'];
    $query = $conn->query("SELECT `url`,`description`,`pageTitle`,`linkKey` FROM `linkList` WHERE `refUserId`=$userId AND `posId`=$showId");
    if ($query != null && $query->num_rows == 1) {
        $row = mysqli_fetch_assoc($query);
        $uniqid = $row['showKey'];

        if ($uniqid == null) {
            
            $uniqid = $conn->real_escape_string(uniqid($showId, true));
            if ($uniqid !== false) {
                $query = $conn->query("UPDATE `linkList` SET `linkKey`='$uniqid' WHERE `refUserId`=$userId AND `posId`=$showId;");
                if (!$query) {
                    //echo alert('error', 'Error occurs whilst displaying QR code:(', 'Error', false, true);
                    $uniqid = null;
                }
            } else {
                $uniqid = null;
            }
        }

        if ($uniqid != null) {
            echo '
    <div class="ui container rounded bordered segment" style="max-width:500px !important;text-align:center;">
        <p style="margin:15px 0;font-size:1.1em;">'.$row['description'].'</p>
        <p style="margin:10px 0;">'.$row['pageTitle'].'</p>
        <img style="border-radius:5px;border:1px solid #333;" src="https://api.qrserver.com/v1/create-qr-code/?data='.urlencode(createLink('visit', 'key', ['key'=>urlencode($uniqid)])).'&size=220x220&margin=10&ecc=L&color=020202&bgcolor=efefef" alt="QRCode - please turn on images">
        <p style="opacity:0.4;margin:5px 0;font-size:0.7em;">'.createLink('visit', 'key', ['key'=>urlencode($uniqid)]).'<br>(orig: '.$row['url'].')</p>
        <p style="opacity:0.6;margin:10px 0;"><a href="javascript:history.back();">'.$lang['informations']['backText'].'</a></p>
    </div>';
        } else {
            echo alert('error', $lang['modules']['qrcode']['errorOccurs'], $lang['informations']['errorTitle'], false, true);
        }
    } else {
        echo alert('warning', $lang['modules']['qrcode']['noPermission'], $lang['informations']['errorTitle'], false, true);
    }
}
?>