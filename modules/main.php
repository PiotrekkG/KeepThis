        <div class="mainDiv ui container rounded bordered segment">
<?php
/*
            <div class="ui grid">
                <div class="ten wide column">.</div>
                <div class="six wide column">.</div>
            </div>
*/
showLinks('','`addTime` desc',$lang['modules']['main']['lastAdded'],$lang['modules']['main']['noLinks'],5);
showLinks('`favorite`=1','`addTime` desc',$lang['modules']['main']['lastFavorite'],'',3);
showLinks('`rate`>0','`rate` desc,`addTime` desc',$lang['modules']['main']['lastTopRated'],'',3);
?>
        </div>