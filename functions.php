<?php
/**
 * Generate and shows (using echo) list of links for given parameters
 */
function showLinks($whereClause='',$orderClause='',$title='',$ifErrorOrNull='',$limit=[5,0],$showViews=false,$showAdditionalOptions=false){
    global $conn, $userId, $lang;
    
    $whereClause = ($whereClause!='')?'AND '.$whereClause:'';
    $orderClause = ($orderClause!='')?'ORDER BY '.$orderClause:'';

    if(!is_array($limit)){
        if(!is_int($limit)){
            $limit = [];
            $limit[0] = 10;
            $limit[1] = 0;
        } else {
            $temp = $limit;
            $limit = [];
            $limit[0] = $temp;
            $limit[1] = 0;
        }
    } else {
        if(count($limit) == 1){
            $limit[1] = 0;
        } else
        if(count($limit) == 0){
            $limit[0] = 10;
            $limit[1] = 0;
        }
    }

    $query = $conn->query("SELECT `posId`,`url`,`pageTitle`,`favorite`,`rate`,`description`,`addTime`".($showViews?",(`clickCounterId`+`clickCounterKey`) as `clickCounterTotal`":'')." FROM `linkList` WHERE `refUserId`=$userId $whereClause $orderClause LIMIT {$limit[1]},{$limit[0]}");
    if ($query != null && $query->num_rows >= 1) {
        echo '
                <div class="mainElement ui segment">
                    '.($title!=''?'<h3 class="ui dividing header">'.$title.'</h3>':'').'
                    <div class="ui relaxed divided list small">';
        while ($row = mysqli_fetch_assoc($query)) {
            $datetime1 = new DateTime();
            $interval = $datetime1->diff(new DateTime($row['addTime']));
            $displayDateFormat = '';
            if ($interval->y != 0) {
                if ($interval->y == 1) {
                    $displayDateFormat .= $lang['modules']['list']['year'].' ';//%y
                } else {
                    $displayDateFormat .= $lang['modules']['list']['years'].' ';//%y
                }
            }
            if ($interval->m != 0) {
                if ($interval->m == 1) {
                    $displayDateFormat .= $lang['modules']['list']['month'].' ';//%m
                } else {
                    $displayDateFormat .= $lang['modules']['list']['months'].' ';//%m
                }
            }
            if ($interval->d != 0) {
                if ($interval->d == 1) {
                    $displayDateFormat .= $lang['modules']['list']['day'].' ';//%d
                } else {
                    $displayDateFormat .= $lang['modules']['list']['days'].' ';//%d
                }
            }
            if ($interval->days != $interval->d) {
                $displayDateFormat .= $lang['modules']['list']['totalDays'].' ';//%days
            }
                
            if ($displayDateFormat == '') {
                if ($interval->h != 0) {
                    if ($interval->h == 1) {
                        $displayDateFormat .= $lang['modules']['list']['hour'].' ';//%h
                    } else {
                        $displayDateFormat .= $lang['modules']['list']['hours'].' ';//%h
                    }
                }
                
                if ($interval->i == 1) {
                    $displayDateFormat .= $lang['modules']['list']['minute'].' ';//%i
                } else {
                    $displayDateFormat .= $lang['modules']['list']['minutes'].' ';//%i
                }
            }
            

            if ($row['favorite'] === null) {
                $row['favorite'] = 0;
            }

            if ($row['rate'] === null) {
                $row['rate'] = 0;
            }

            if ($row['pageTitle']=='' && $row['description']=='') {
                $row['pageTitle'] = '--- ??? ---';
            }
            
            echo '
                        <div class="item">
                            <i class="large linkify icon"></i>
                            <div class="content">
                                <a class="header" href="'.createLink('visit', 'id', ['id'=>$row['posId']]).'">'.$row['pageTitle'].($row['description']!=''?' - '.$row['description']:'').'</a> 
                                <div class="description"><a href="'.createLink('qrcode', 'show', ['showId'=>$row['posId']]).'"><i class="qrcode icon"></i></a>'.($showAdditionalOptions?' <a href="'.createLink('position', 'edit', ['pId'=>$row['posId']]).'"><i class="edit outline icon"></i></a> <a href="'.createLink('position', 'delete', ['pId'=>$row['posId']]).'"><i class="trash alternate icon"></i></a>':'').' <div class="ui mini heart rating disabled" data-rating="'.$row['favorite'].'" data-max-rating="1"></div> <div class="ui mini star rating disabled" data-rating="'.$row['rate'].'" data-max-rating="5"></div> <span title="'.$row['addTime'].'">'.$lang['modules']['list']['added'].' '.$interval->format($displayDateFormat).$lang['modules']['list']['ago'].'</span>'.($showViews?' <small><i class="mouse pointer icon"></i>'.$row['clickCounterTotal'].'</small>':'').'</div>
                            </div>
                        </div>';
        }
        echo '
                    </div>
                </div>';
    } else {
        if($ifErrorOrNull != '')
            echo alert('info', $ifErrorOrNull);
    }
}

/**
 * Returns array with translation for given lang
 */
function validLangType($lang)
{
    // if ($lang == 'gb') {
    //     $lang = 'en';
    // }

    //$acceptLang = ['en','fr','de','es','pl'];
    $acceptLang = ['en','pl'];
    $lang = in_array($lang, $acceptLang) ? $lang : 'en';

    return $lang;
}

/**
 * Returns array with translation for given lang
 */
function returnLangArray($changeToLang = null)
{
    if ($changeToLang == null) {
        if (isset($_SESSION['user']['lang'])) {
            $changeToLang = $_SESSION['user']['lang'];
        } else if (isset($_COOKIE['lang'])) {
            $changeToLang = $_COOKIE['lang'];
        } else {
            $changeToLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
    }
    
    $changeToLang = validLangType($changeToLang);

    require $modulesDir.'lang/en.php';
    if ($changeToLang != 'en') {
        $temp = $lang;
        require $modulesDir."lang/$changeToLang.php";
        $lang = array_replace((array)$temp, (array)$lang);
        foreach ($temp['informations'] as $key=>$val) {
            if (!isset($lang['informations'][$key])) {
                $lang['informations'][$key] = $temp['informations'][$key];
            }
        }
        foreach ($temp['modules'] as $key=>$val) {
            if (!isset($lang['modules'][$key])) {
                $lang['modules'][$key] = $temp['modules'][$key];
            } else {
                $lang['modules'][$key] = array_merge($temp['modules'][$key], $lang['modules'][$key]);
            }
        }
    }

    return $lang;
}

/**
 * Returns link for given parameters
 */
function createLink($module, $action=null, $parameters=null)
{
    global $webRoot;
    if ($action == null && $parameters == null) {
        return $webRoot."$module/";
    } else {
        if ($action == null) {
            $action = 'show';
        }

        $parametersLink = '';
        foreach ($parameters as $key=>$val) {
            $parametersLink .= $key;
            $val .= '';
            if ($val != null && $val != '') {
                $parametersLink .= ','.$val;
            }
            $parametersLink .= '/';
        }

        return $webRoot."$module/$action/$parametersLink";
    }
}

/**
 * Returns page title of given url
 */
function getTitle($url)
{
	//set_time_limit(8);
    $urlContents = file_get_contents($url, false, stream_context_create(array("http" => array("user_agent" => "any"))), 0, 2800);
    preg_match("/<title>(.*)<\/title>/i", $urlContents, $matches);
    
    $title = $matches[1];
    
    return $title;
    // $dom = new DOMDocument();
    // @$dom->loadHTML($urlContents);
    // $title = $dom->getElementsByTagName('title');
    // return $title->item(0)->nodeValue;
}

/**
 * Returns html alert of defined type
 */
function alert($alertType, $content, $title='', $closeButton=false, $backButton=false)
{
    global $lang;
    if (!isset($lang)) {
        $lang['informations']['backText'] = 'Back to previous page';
    }

    switch ($alertType) {
        case 'error':
        case 'danger':
        case 'negative':
            $alertType = 'negative';
            break;
            
        case 'info':
            $alertType = 'info';
            break;
            
        case 'ok':
        case 'success':
            $alertType = 'success';
            break;
            
        case 'warning':
            $alertType = 'warning';
            break;
            
        default:
            $alertType = '';
            break;
    }

    $title = $title==''?'':'<div class="header">'.$title.'</div>';
    $closeButton = $closeButton==true?'<i class="close icon"></i>':'';
    $backButton = $backButton==true?'<p style="opacity:0.5;margin-top:5px;"><a href="javascript:history.back();">'.$lang['informations']['backText'].'</a></p>':'';
    
    return '<div class="ui '.$alertType.' message">'.$closeButton.$title.'<p>'.$content.'</p>'.$backButton.'</div>'.PHP_EOL;
}
?>