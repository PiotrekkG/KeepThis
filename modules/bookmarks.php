        <div class="mainDiv ui container rounded bordered segment">
<?php
    switch ($action) {
        case 'edit':
            # code...
            break;

        case 'add':
            $uniqid = $conn->real_escape_string(uniqid($userId, true));
            if ($uniqid !== false && $uniqid !== '' && $uniqid !== null) {
                $query = $conn->query("INSERT INTO `bookmarkKeys` (`bookmarkKey`,`refUserId`) VALUES ('$uniqid',$userId);");
                if ($query) {
                    echo alert('success', $lang['modules']['bookmarks']['generatingSuccess']);
                } else {
                    echo alert('error', $lang['modules']['bookmarks']['generatingFail']);
                }
            }
        case 'show':
        default:
            $query = $conn->query("SELECT `bookmarkId`,`bookmarkKey`,`createdTime`,IF(`lastUsedDate`=CURRENT_DATE,`lastUsedDateCounter`,0) as `usedToday`,`totalUsedCounter` FROM `bookmarkKeys` WHERE `refUserId`=$userId AND `active`=1");
            if ($query != null && $query->num_rows >= 1) {
                echo '
            <table class="ui celled striped table">
                <thead>
                    <tr>
                    <th colspan="4">
                    '.$lang['modules']['bookmarks']['tableTitle'].'
                        <a href="'.createLink('bookmarks','add').'" class="mini ui button" style="margin-right:0px;float:right;">
                            <i class="plus square outline icon"></i>
                            '.$lang['modules']['bookmarks']['addBookmarkButton'].'
                        </a>
                    </th>
                    </tr>
                </thead>
                <tbody>
                ';
                while ($row = mysqli_fetch_assoc($query)) {
                    $createdTime = new DateTime($row['createdTime']);
            
                    echo '
                    <tr>
                        <td class="collapsing"><i class="bookmark icon"></i> '.$row['bookmarkKey'].'<i class="eye slash icon"></i><i class="eye icon"></i></td>
                        <td>'.$lang['modules']['bookmarks']['tableCreated'].'<br>'.$createdTime->format("d.m.Y").'</td>
                        <td>'.$lang['modules']['bookmarks']['tableUsedTimes'].'<br>'.$row['totalUsedCounter'].' - today:'.$row['usedToday'].'</td>
                        <td>'.$lang['modules']['bookmarks']['tableOptions'].'<br><i class="edit outline icon"></i> <i class="trash alternate icon"></i> <a href=\'javascript:document.location="'.createLink('bookmark','add',['bookmarkKey'=>$row['bookmarkKey']/*,'url'=>'"+encodeURIComponent(document.URL)+"']).'";*/]).'"\' onclick="return false;"><i class="linkify icon"></i><span style="font-size:0em!important;">'.$lang['modules']['bookmarks']['bookmarkDefaultTitle'].'</span></a></td>
                    </tr>';
                }
                echo '
                </tbody>
            </table>
                ';
            } else {
                echo alert('info', $lang['modules']['bookmarks']['noBookmarks']);
            }
            break;
    }
?>
        </div>