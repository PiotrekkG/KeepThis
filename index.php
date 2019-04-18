<?php
session_start();

require_once 'configuration.php';
require_once 'functions.php';
require_once 'connection.php';

if (isset($_SERVER['PATH_INFO'])) {
    $pathInfo = trim($_SERVER['PATH_INFO'], '/'); //usuwamy znak / z końca
    if (!empty($pathInfo)) { //pusta ścieżka

        $arr = explode('/', $pathInfo); //rozbijamy naszą ścieżkę na podstawie /
        $count = count($arr);

        $_GET['module'] = $arr[0]; //moduł
        $_GET['action'] = isset($arr[1]) ? $arr[1] : ''; //akcja

        //następne elementy to wartości parametrów get
        for ($i=2; $i < $count;$i++) {
            $par = explode(',', $arr[$i]);
            $_GET[$par[0]] = isset($par[1]) ? $par[1] : '';//wartość parametru do geta
        }
    }
    //link przykladowy: /main/show/par1,war1/par2,war2/var,val/
}

$module = (isset($_GET['module']))? $_GET['module'] : $module = 'main';
$action = (isset($_GET['action']) && $_GET['action'] != '')? $_GET['action'] : 'show';


/**
 * Returns array with language which should be displayed using preferences
 */
$lang = returnLangArray();


if ($module == 'bookmark') {
    include $modulesDir.'bookmark.php';
    exit;
}

if($module == 'language' && $action == 'change'){
    if (isset($_GET['name'])) {
        $langName = validLangType($_GET['name']);

        if (isset($_SESSION['user']) && $_SESSION['user']['lang'] != $langName) {
            $userId = $_SESSION['user']['userId'];
            if (!$conn->query("UPDATE `users` SET `lang`='$langName' WHERE `userId`=$userId")) {
                header("Location: ".createLink('main', 'langNotChanged'));
                exit;
            }
            $_SESSION['user']['lang'] = $langName;
        }
        setcookie('lang', $langName, time()+60*60*24*30, '/');
        $_COOKIE['lang'] = $langName;
    
        header("Location: ".createLink('main', 'langChanged'));
        exit;
    } else {
        header("Location: ".createLink('main', 'langNotChanged'));
        exit;
    }
}

if (!isset($_SESSION['user'])) {
    
    if($module == 'visit' && $action == 'key' && isset($_GET['key'])){
        $key = $conn->real_escape_string(urldecode($_GET['key']));
        $query = $conn->query("SELECT `posId`,`url` FROM `linkList` WHERE `linkKey`='$key'");
        if ($query != null && $query->num_rows == 1) {
            $row = mysqli_fetch_assoc($query);

            //redirect and close connection
            header("Location: ".$row['url']);
            header("Connection: close");
            
            $conn->query("UPDATE `linkList` SET `clickCounterKey`=`clickCounterKey`+1 WHERE `posId`=".$row['posId']);
            exit;
        } else {
            header("Location: ".createLink('visit','error'));
        }
    }
    if (($module == 'register' || $module == 'login')) {
        include $modulesDir.'login.php';
        exit;
    }


    header("Location: ".createLink('login'));
    exit;
}

if(isset($_GET['logout']) || $module == 'logout'){
    unset($_SESSION['user']);
    
    header("Location: ".createLink('login','logout'));
	exit;
}



/*****************************************
* MAIN PAGE STARTS - NO REDIRECTS BELOW  *
*****************************************/

$userId = $_SESSION['user']['userId'];
if (session_id()) session_write_close();



$info = [];
switch ($module) {
    case 'visit':
            if($action == 'error'){
                array_push($info, alert('warning', $lang['modules']['visit']['noPermission'], $lang['informations']['errorTitle'], false, true));
            } else {
                $conditional = '';
                if ($action == 'id' && isset($_GET['id'])) {
                    $id = (int)$_GET['id'];
                    $conditional = "`posId`=$id AND `refUserId`=$userId";
                } elseif ($action == 'key' && isset($_GET['key'])) {
                    $key = $conn->real_escape_string(urldecode($_GET['key']));
                    $conditional = "`linkKey`='$key'";
                }

                if ($conditional != '') {
                    $query = $conn->query("SELECT `posId`,`url` FROM `linkList` WHERE $conditional");
                    if ($query != null && $query->num_rows == 1) {
                        $row = mysqli_fetch_assoc($query);

                        //redirect and close connection
                        header("Location: ".$row['url']);
                        header("Connection: close");

                        $conn->query("UPDATE `linkList` SET ".(($action == 'id' && isset($_GET['id']))?'`clickCounterId`=`clickCounterId`+1':'`clickCounterKey`=`clickCounterKey`+1')." WHERE `posId`=".$row['posId']);
                        exit;
                    } else {
                        header("Location: ".createLink('visit', 'error'));
                    }
                } else {
                    header("Location: ".createLink('visit', 'error'));
                }
            }

            $module = 'main';
            $includingModuleFilename = 'main.php';
            break;

    case 'login':
    case 'register':
            array_push($info, alert('info', $lang['modules']['login']['alreadyLogged'], $lang['informations']['infoTitle'], true));
            
            $module = 'main';
            $includingModuleFilename = 'main.php';
            break;
        
    case 'add':
            $includingModuleFilename = 'add.php';
            break;
        
    case 'list':
            $includingModuleFilename = 'list.php';
            break;
        
    case 'qrcode':
            $includingModuleFilename = 'qrcode.php';
            break;
        
    case 'bookmarks':
            $includingModuleFilename = 'bookmarks.php';
            break;

    default:
            if ($module != 'main') {
                array_push($info, alert('info', $lang['informations']['wrongSiteText'], $lang['informations']['wrongSiteTitle'], true));
            }
            if ($action == 'langNotChanged') {
                array_push($info, alert('error', '', $lang['modules']['language']['langNotChanged'], true));
            }
            if ($action == 'langChanged') {
                array_push($info, alert('success', '', $lang['modules']['language']['langChanged'], true));
            }
            if ($action == 'loggedIn') {
                array_push($info, alert('success', '', $lang['modules']['login']['loggedIn'], true));
            }
            
            $module = 'main';
            $includingModuleFilename = 'main.php';
            break;
}
?>
<!DOCTYPE HTML>
<html lang="<?=$lang['currentLangShort'];?>">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		
		<title><?=$lang['globalWebsiteTitle'];?></title>
		
		<link rel="stylesheet" type="text/css" href="/style.css">
		
		<link rel="stylesheet" type="text/css" href="/semantic/dist/semantic.min.css">

		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/reset.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/site.css">

		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/container.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/grid.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/header.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/image.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/menu.css">

		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/divider.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/list.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/segment.css">
		<link rel="stylesheet" type="text/css" href="/semantic/dist/components/dropdown.css">
        <link rel="stylesheet" type="text/css" href="/semantic/dist/components/icon.css">
        
        <link rel="stylesheet" type="text/css" href="/semantic/dist/components/message.css">
        
        <link rel="shortcut icon" href="/images/logo.png" type="image/x-icon">
	</head>
	<body>
		<div id="nav" class="ui fixed menu">
			<div class="ui container">
				<a href="<?=createLink('main');?>" id="logo" class="header item">
					<img class="logo" src="/images/logo.png">
                    <?=$lang['nav']['title'];?>
					<small><?=$lang['nav']['titleSmall'];?></small>
				</a>
				<div class="right menu">
					<a href="<?=createLink('add');?>" class="item">
						<i class="keyboard outline icon"></i> <?=$lang['nav']['addLink'];?>
					</a>
					<a href="<?=createLink('list');?>" class="item">
						<i class="list alternate outline icon"></i> <?=$lang['nav']['list'];?>
					</a>
					<a href="<?=createLink('bookmarks');?>" class="item">
						<i class="bookmark outline icon"></i> <?=$lang['nav']['bookmarks'];?>
					</a>
					<a href="<?=createLink('account');?>" class="item">
						<i class="address book outline icon"></i> <?=$lang['nav']['account'];?>
					</a>
					<a href="<?=createLink('logout');?>" class="item">
						<i class="logout icon"></i> <?=$lang['nav']['logout'];?>
					</a>
				</div>
			</div>
		</div>

		<div id="topDiv"></div>

<?php
if(isset($info) && count($info) != 0){
	echo '<div class="ui container">';
	echo implode('<br>',$info);
	echo '</div>';
}
?>
		
<?php
	include $modulesDir.$includingModuleFilename;
?>
		
		<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
		<script src="/semantic/dist/semantic.min.js"></script>
		<script>
		$('.message .close').on('click',function(){$(this).closest('.message').transition('fade');});
		$('.mainDiv .ui.rating').rating({interactive:false});
		$('form .ui.rating').rating({clearable:true,interactive:true});
		$('form .ui.rating').rating('setting','onRate',function(value){$("input[name="+this.id+"]")[0].value=value;});
		</script>
	</body>
</html>

