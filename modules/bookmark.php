<?php
if(isset($_SESSION['user'])){
    $userId = $_SESSION['user']['userId'];
}
if (session_id()) session_write_close();
?>
<!DOCTYPE HTML>
<html lang="<?=$lang['currentLangShort'];?>">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	
	<title><?=$lang['globalWebsiteTitle'];?></title>
	
	<link rel="stylesheet" type="text/css" href="/semantic/dist/semantic.min.css">
	
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/reset.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/site.css">

	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/container.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/grid.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/header.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/image.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/menu.css">

	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/divider.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/segment.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/list.css">
	<link rel="stylesheet" type="text/css" href="/semantic/dist/components/dropdown.css">
    <link rel="stylesheet" type="text/css" href="/semantic/dist/components/icon.css">
    
    <link rel="stylesheet" type="text/css" href="/semantic/dist/components/message.css">
    
    <link rel="shortcut icon" href="/images/logo.png" type="image/x-icon">
</head>
<body>
	
	<div class="ui middle aligned center aligned grid" style="margin-top:120px;">
<?php
if (isset($_GET['bookmarkKey']) && (isset($_GET['url']) || isset($_SERVER['HTTP_REFERER']))) {
    $bookmarkKey = $_GET['bookmarkKey'];
    $bookmarkKey = $conn->real_escape_string($bookmarkKey);
    
    $url = '';
    if (isset($_GET['url'])) {
        $url = $_GET['url'];
        $url = urldecode($url);
        $url = trim($url);

        if (strlen($url)<7) {
            if (isset($_SERVER['PATH_INFO'])) {
                $pathInfo = trim(urldecode($_SERVER['PATH_INFO']), '/'); //usuwamy znak / z końca
                if (!empty($pathInfo)) {
                    $url = mb_strstr($pathInfo, '/url,');
                    $url = trim($url, '/');
                    $url = substr($url,4);
                }
            }
        }
    } else {
        $url = $_SERVER['HTTP_REFERER'];
    }
    
    // Remove all illegal characters from a url
    $url = filter_var($url, FILTER_SANITIZE_URL);
    // Validate url
    if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
        $bookmarkKey = $conn->real_escape_string($bookmarkKey);
        if ($bookmarkKey == $_GET['bookmarkKey']) {
            $query = $conn->query("SELECT `bookmarkId`,`refUserId`,`allowUnlogged`,IF(`lastUsedDate`=CURRENT_DATE,1,0) as `lastUsedToday` FROM `bookmarkKeys` WHERE `bookmarkKey`='$bookmarkKey' AND `active`=1");
            if ($query != null && $query->num_rows == 1) {
                $row = mysqli_fetch_assoc($query);

                $url = $conn->real_escape_string($url);
                $pageTitle = getTitle($url);

                if ($row['lastUsedToday'] == 1) {
                    $updateCounter = '`lastUsedDateCounter`=`lastUsedDateCounter`+1,`totalUsedCounter`=`totalUsedCounter`+1';
                } else {
                    $updateCounter = '`lastUsedDate`=CURRENT_DATE,`lastUsedDateCounter`=1,`totalUsedCounter`=`totalUsedCounter`+1';
                }

                if ($row['allowUnlogged'] == 1 || ($row['allowUnlogged'] == 0 && $userId == $row['refUserId'])) {
                    if ($conn->query("INSERT INTO `linkList` (`refUserId`,`refBookmarkId`,`url`,`pageTitle`) VALUES ({$row['refUserId']}, {$row['bookmarkId']}, '$url', '$pageTitle')")) {
                        $conn->query("UPDATE `bookmarkKeys` SET $updateCounter WHERE `bookmarkId`=".$row['bookmarkId']);
                        echo alert('success', $lang['modules']['bookmark']['addingSuccess']);
                    } else {
                        echo alert('error', $lang['modules']['bookmark']['addingError']);
                    }
                } else {
                    echo alert('info', $lang['modules']['bookmark']['unloggedInfo']);
                }
            } else {
                echo alert('error',$lang['modules']['bookmark']['invalidKeyOrInactive']);
            }
        } else {
            echo alert('error',$lang['modules']['bookmark']['invalidKey']);
        }
    } else {
        echo alert('error',$lang['modules']['bookmark']['invalidUrl']);//.$url.var_dump($_GET));
    }
}
else
	echo alert('error',$lang['modules']['bookmark']['missingData']);
?>
    </div>
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
	<script src="/semantic/dist/components/form.js"></script>
    <script src="/semantic/dist/components/transition.js"></script>

</body>
</html>