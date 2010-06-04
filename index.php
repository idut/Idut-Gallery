<?php
/* Idut Gallery 2.1 (beta)
 * (c) 2005-2008 Idut - www.idut.co.uk
 * index.php
 */
$configfile = "config.php";

require($configfile);
if(isset($_GET['page'])){
        $page = $_GET['page'];
}
if(isset($_GET)){
        foreach($_GET as $key => $value){
                $_GET[$key] = RemoveXSS($value);
        }
}
if(isset($_POST)){
        foreach($_POST as $key => $value){
                $_POST[$key] = RemoveXSS($value);
        }
}

if($IG_CONFIG['templatefile'] AND file_exists($IG_CONFIG['templatefile'])){
        $handle = fopen($IG_CONFIG['templatefile'], "r");
        $contents = fread($handle, filesize($IG_CONFIG['templatefile']));
        fclose($handle);
        if(stristr($contents,'<###IG-TITLE###>')){
                $contents = str_replace('<###IG-TITLE###>',$IG_CONFIG['title'],$contents);
        }
        if(stristr($contents,'<###IG-SUBTITLE###>')){
                if(isset($_GET['d'])) $sub = ' - '.$_GET['d'];
                if(isset($_GET['f'])){
                        $temp = explode(".",$_GET['f']);
                        $sub .= ' - '.$temp[0];
                }else{
                        $sub = '';
                }
                $contents = str_replace('<###IG-SUBTITLE###>',$sub,$contents);
        }
        if(stristr($contents,'<###IG-HEAD###>')){
                $temp = '<link href="'.$IG_CONFIG['cssfile'].'" rel="stylesheet" type="text/css" />';
                if($IG_CONFIG['reflection'] AND file_exists($IG_CONFIG['reflection'])) $temp = $temp.'<script type="text/javascript" src="'.$IG_CONFIG['reflection'].'"></script>';
                $contents = str_replace('<###IG-HEAD###>',$temp,$contents);
        }
        if($content = stristr($contents,'<###IG-POWERED###>')){
                $contents = str_replace('<###IG-POWERED###>',
                '<div class="bottom">Powered by <a href="http://www.idut.co.uk/" target="_blank">Idut Gallery</a> 2.1</div>',
                $contents);
        }
        if(stristr($contents,'<###IG-MENU###>')){
                $contents = str_replace('<###IG-MENU###>',showMenu(),$contents);
        }
                $temp = explode('<###IG-MAIN###>',$contents);
                echo $temp[0];
                $contents = strl($temp[1],$content);
}

if(isset($_GET['c']) AND $_GET['c'] == "album" AND isset($_GET['d'])){
        if(array_key_exists('albumindex',$IG_CONFIG)){$albumindex = $IG_CONFIG['albumindex']; }else{ $albumindex = true; }
        if($albumindex){
                showAlbum($_GET['d']);
        }else{
                showSlideshow($_GET['d']);
        }
}elseif(isset($_GET['c']) AND $_GET['c'] == "show" AND isset($_GET['d']) AND isset($_GET['f'])){
                showImage($_GET['d'],$_GET['f']);
}elseif(isset($_POST['c']) AND $_POST['c'] == "docommentsave" AND isset($_POST['d']) AND isset($_POST['f'])){
                if($IG_CONFIG['humanchecker'] AND file_exists($IG_CONFIG['humanchecker'])) include($IG_CONFIG['humanchecker']);
                doSaveComment($_POST['d'],$_POST['f'],$_POST['commentname'],$_POST['commentemail'],$_POST['commentcomment']);
}else{
        showMain();
}

if($IG_CONFIG['templatefile'] AND file_exists($IG_CONFIG['templatefile']) AND $contents){
        echo $contents;
}

function showMain(){
        global $IG_CONFIG;
        // MAIN SCREEN TURN ON
        $hasphotos = false;
        $print = NULL;

        if($IG_CONFIG['showmainpage']) include($IG_CONFIG['mainfile']);
        
        if($IG_CONFIG['showautomatic']){
                $result = opendir($IG_CONFIG['imagedir']);
                $buffer = Array();
                while ($fn = readdir($result)) {
                        if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['imagedir'].$fn)) {
                                if (is_dir($IG_CONFIG['thumbdir'].$fn)) {
                                        $buffer[] = $fn;
                                }
                        }
                }
                //***SORT BUFFERED IMAGES HERE
                $buffer = sortFiles($buffer);
                foreach($buffer as $fn){
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                $print .= '<a href="'.$fn.'.html">'.$fn.'</a><br/>';
                        }else{
                                $print .= '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$fn.'">'.$fn.'</a><br/>';
                        }
                        $hasphotos = true;
                }
                echo $print;
                $print = null;
        }
        
        if($IG_CONFIG['showautomaticthumbs']){
                $result = opendir($IG_CONFIG['imagedir']);
                echo "<table cellpadding=\"4\" border=\"0\">\n  <tr>\n";
                $pasted = 0;
                $buffer = Array();
                while ($fn = readdir($result)) {
                        if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['imagedir'].$fn)) {
                                if (is_dir($IG_CONFIG['thumbdir'].$fn)) {
                                        $buffer[] = $fn;
                                }
                        }        
                }
                //***SORT BUFFERED IMAGES HERE
                $buffer = sortFiles($buffer);
                $pasted = 0;
                foreach($buffer as $fn){
                        $hasphotos = true;
                        $pasted++;
                        if($IG_CONFIG['row']){$row = $IG_CONFIG['row']; }else{ $row = 3; }
                        if($IG_CONFIG['perrow']){$perrow = $IG_CONFIG['perrow']; }else{ $perrow = 3; }
                        $print .= '<td align="center">';
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                $print .= '<a href="'.$fn.'.html">';
                        }else{
                                $print .= '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$fn.'">';
                        }
                        if($IG_CONFIG['showautomaticthumbsname']) $print .= $fn.'<br/>';
                        $print .= '<img src="'.$IG_CONFIG['thumbdir'].$fn.'/'.albumCover($fn).'" border="0" alt="'.$fn.'" class="reflect">';
                        $print .= '</a></td>';
                        if($pasted == $row){
                                $print .= "</tr><tr>\n";
                                $pasted = 0;
                        }
                }
        echo $print;
        echo '</tr></table>';
        }
        if($hasphotos == false){
                echo 'There are no photo albums in this gallery!<br/><br/>
                <a href="admin.php">Login to the admin page to upload photos.</a>';
        }
}//showMain

function showMenu(){
        global $IG_CONFIG;
        $return = null;
        $top = '<big><b><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a></b></big><br/><br/>';
        $top = $top."<b>Albums</b><br/>";
        $result = opendir($IG_CONFIG['imagedir']);
        $buffer = Array();
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['imagedir'].$fn)) {
                        if (is_dir($IG_CONFIG['thumbdir'].$fn)) {
                                $buffer[] = $fn;
                        }
                }        
        }
        $buffer = sortFiles($buffer);
        foreach($buffer as $fn){
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        $return .= '&nbsp;&nbsp;&nbsp;&nbsp;&middot;<a href="'.$fn.'.html">'.$fn.'</a><br/>';
                }else{
                        $return .= '&nbsp;&nbsp;&nbsp;&nbsp;&middot;<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$fn.'">'.$fn.'</a><br/>';
                }
        }
                                
        return $top.$return;
}//showMenu

function showAlbum($dir){
        global $IG_CONFIG, $_GET,$print;
//SHOW THUMBNAIL INDEX FOR ALBUM
        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="'.$_GET['d'].'.html">'.$_GET['d'].'</a></div><br/>';
        }else{
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="?c=album&d='.$_GET['d'].'">'.$_GET['d'].'</a></div><br/>';
        }
        $buffer = Array();
        $maindir = $IG_CONFIG['thumbdir'].$dir;
        
        $result = @opendir($maindir);
        while ($fn = @readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['imagedir'].$dir)) {
                                $buffer[] = $fn;
                }
        }

        if (!$buffer) {
                die("Directory does not exist!");
        }
        $buffer = sortFiles($buffer);
        if($IG_CONFIG['row']){$row = $IG_CONFIG['row']; }else{ $row = 3; }
        if($IG_CONFIG['perrow']){$perrow = $IG_CONFIG['perrow']; }else{ $perrow = 3; }
        $perpage = ($row * $perrow);
        if (@!$_GET['page'] OR $_GET['page'] == "0") {
                $page = 1;
        }else{
                $page = $_GET['page'];
        }
        $start = ($page*$perpage)-$perpage;
        $end = $perpage+$start;
        if($end > count($buffer)) $end = count($buffer);
        $pasted = 0;
        $print = "<table cellpadding=\"4\" border=\"0\">\n  <tr>\n";

        for($i=$start;$i<$end;$i++){
                $fn = $buffer[$i];
                $pasted++;
                ImageDisplay($dir, $fn);
                if($pasted == $row){
                        $print = $print."</tr>\n\n<tr>";
                        $pasted = 0;
                }
        }
        $print = $print."</tr></table>";

        if ($IG_CONFIG['print_pages_on_top']) {
                PageAmount(count($buffer), $dir);
        }
        
        if($IG_CONFIG['usedatafiles'] AND $IG_CONFIG['albumdescriptions']){
                if(@file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                        foreach ($lines as $line) {
                                $line = explode("|",$line);
                                if($line[0] == "albumdesc"){
                                        echo '<br/><div class="description">';
                                        echo trim($line[1]);
                                        echo '</div><br/>';
                                        break;
                                }
                        }
                }
        }
        
        echo $print;
                
        if ($IG_CONFIG['print_pages_on_bottom']) {
                echo "<br/>";
                PageAmount(count($buffer), $dir);
        }

         @closedir($result);
}//showAlbum

function showImage($dir,$file){
        global $IG_CONFIG;
        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="'.$_GET['d'].'.html">'.$_GET['d'].'</a></div><br/>';
        }else{
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="?c=album&d='.$_GET['d'].'">'.$_GET['d'].'</a></div><br/>';
        }
        if ($IG_CONFIG['print_pages_on_top']) {
                slideShowCount($file,$dir);
        }
        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                if($IG_CONFIG['slideshow'] and $next = nextFile($file,$dir)) echo '<a href="'.$dir.'--'.$next.'.html">';
                if($IG_CONFIG['slideshow'] and !nextFile($file,$dir)) echo '<a href="'.$dir.'.html">';
        }else{
                if($IG_CONFIG['slideshow'] and $next = nextFile($file,$dir)) echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.$next.'&d='.$dir.'">';
                if($IG_CONFIG['slideshow'] and !nextFile($file,$dir)) echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'">';
        }
        echo '<img src="'.$IG_CONFIG['imagedir'].$dir.'/'.$file.'" border="0" class="reflect rheight10">';
        if($IG_CONFIG['slideshow']) echo '</a>';
        
        if($IG_CONFIG['slideshowdetails']) {
                showDetails($file,$dir);
        }

        if($IG_CONFIG['usedatafiles'] AND $IG_CONFIG['imagedescriptions']){
                if(@file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                        foreach ($lines as $line) {
                                $line = explode("|",$line);
                                if($line[0] == "imagedesc" and $line[1] == $file){
                                        echo '<div class="description">';
                                        echo trim($line[2]);
                                        echo '</div><br/>';
                                        break;
                                }
                        }
                }
        }

        if ($IG_CONFIG['print_pages_on_bottom']) {
                slideShowCount($file,$dir);
        }
        showComments($dir,$file);
}//showImage

function albumCover($dir){
        global $IG_CONFIG;
        if(file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                foreach ($lines as $line) {
                        $line = explode("|",$line);
                        if($line[0] == "albumcover"){
                                return $line[1];
                        }
                }
        }
        $dirhandle = opendir($IG_CONFIG['thumbdir'].$dir);
        while (false !== ($cover = readdir($dirhandle))) {
                if ($cover == "." OR $cover == ".." OR $cover == "Thumbs.db" OR $cover == "idutgallerydata.txt") continue;
                        break;
        }
        return $cover;
}

function showSlideshow($dir){
        global $IG_CONFIG;
        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="'.$_GET['d'].'.html">'.$_GET['d'].'</a></div><br/>';
        }else{
                if($IG_CONFIG['topinfo']) echo '<div style="float:left"><a href="'.$IG_CONFIG['galleryfile'].'">'.$IG_CONFIG['title'].'</a> : <a href="?c=album&d='.$_GET['d'].'">'.$_GET['d'].'</a></div><br/>';
        }
        echo '<br/>';
        if($file == null){
                $file = firstFile($dir);
        }
        if ($IG_CONFIG['print_pages_on_top']) {
                slideShowCount($file,$dir);
        }
        if($next = nextFile($file,$dir)) {
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '<a href="'.$dir.'--'.$next.'.html">';
                }else{
                        echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.nextFile($file,$dir).'&d='.$dir.'">';
                }
                echo '<img src="'.$IG_CONFIG['imagedir'].$dir.'/'.$file.'" border="0" class="reflect rheight10">';
                echo '</a>';
        //}elseif(!nextFile($file,$dir)){
        }else{
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '<a href="'.$dir.'.html">';
                }else{
                        echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'">';
                }
                echo '<img src="'.$IG_CONFIG['imagedir'].$dir.'/'.$file.'" border="0" class="reflect rheight10">';
                echo '</a>';
        }
        if ($IG_CONFIG['slideshowdetails']) {
                showDetails($file,$dir);
        }
        
        if($IG_CONFIG['usedatafiles'] AND $IG_CONFIG['imagedescriptions']){
                $datafile = @file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                foreach ($lines as $line) {
                        $line = explode("|",$line);
                        if($line[0] == "imagedesc" and $line[1] == $file){
                                echo '<div class="description">';
                                echo trim($line[2]);
                                echo '</div><br/><br/>';
                                break;
                        }
                }
        }
        
        showComments($dir,$file);
        
        if ($IG_CONFIG['print_pages_on_bottom']) {
                slideShowCount($file,$dir);
        }
}//showSlideshow

function PageAmount ($total, $dir) {
        global $page,$IG_CONFIG;
        if(!$page) $page = 1;
        if($IG_CONFIG['row']){$row = $IG_CONFIG['row']; }else{ $row = 3; }
        if($IG_CONFIG['perrow']){$perrow = $IG_CONFIG['perrow']; }else{ $perrow = 3; }
        $totalpages = ceil($total/($row*$perrow));
        echo '<center><small>'.$page.' of '.$totalpages.' pages</small><br/>';
        if(($page-1) > 0){
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '<a href="'.$dir.'.html?page=1">';
                }else{
                        echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'&page=1">';
                }
                if(file_exists("prev2.png")){
                        echo '<img src="prev2.png" border="0" align="absmiddle"/>';
                }else{
                        echo '&lt;&lt;';
                }
                echo '</a>&nbsp;&nbsp;&nbsp;';
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '<a href="'.$dir.'.html?page='.($page-1).'">';
                }else{
                        echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'&page='.($page-1).'">';
                }
                if(file_exists("prev.png")){
                        echo '<img src="prev.png" border="0" align="absmiddle"/>';
                }else{
                        echo '&lt;';
                }
                echo '</a>';
        }
        echo "&nbsp;&nbsp; $dir &nbsp;&nbsp;";
        if(($page+1) <= $totalpages){
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '<a href="'.$dir.'.html?page='.($page+1).'">';
                }else{
                        echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'&page='.($page+1).'">';
                }
                if(file_exists("next.png")){
                        echo '<img src="next.png" border="0" align="absmiddle"/>';
                }else{
                        echo '&gt;';
                }
                echo '</a>';
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '&nbsp;&nbsp;&nbsp;<a href="'.$dir.'.html?page='.($totalpages).'">';
                }else{
                        echo '&nbsp;&nbsp;&nbsp;<a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'&page='.($totalpages).'">';
                }
                if(file_exists("next2.png")){
                        echo '<img src="next2.png" border="0" align="absmiddle"/>';
                }else{
                        echo '&gt;&gt;';
                }
                echo '</a>';
        }
        echo '</center>';
        
}//PageAmount

function slideShowCount ($file, $dir) {
                global $IG_CONFIG;
                $result = opendir($IG_CONFIG['imagedir'].$dir);
                $buffer = Array();
                while ($fn = readdir($result)) {
                        if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['thumbdir'])) {
                                $buffer[] = $fn;
                        }
                }
                $buffer = sortFiles($buffer);

                echo '<center><small>'.(array_search($file, $buffer)+1).' of '.count($buffer).' photos</small><br/>';

                if($prev = prevFile($file,$dir)) {
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                echo '<a href="'.$dir.'--'.firstFile($dir).'.html">';
                        }else{
                                echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.firstFile($dir).'&d='.$dir.'">';
                        }
                        if(file_exists("prev2.png")){
                                echo '<img src="prev2.png" border="0" align="absmiddle"/>';
                        }else{
                                echo '&lt;&lt;';
                        }
                        echo '</a>&nbsp;&nbsp; ';
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                echo '<a href="'.$dir.'--'.$prev.'.html">';
                        }else{
                                echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.$prev.'&d='.$dir.'">';
                        }
                        if(file_exists("prev.png")){
                                echo '<img src="prev.png" border="0" align="absmiddle"/>';
                        }else{
                                echo '&lt;';
                        }
                        echo '</a>';
                }
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        echo '&nbsp;&nbsp; <a href="'.$dir.'.html">'.$dir.'</a> &nbsp;&nbsp;';
                }else{
                        echo '&nbsp;&nbsp; <a href="'.$IG_CONFIG['galleryfile'].'?c=album&d='.$dir.'">'.$dir.'</a> &nbsp;&nbsp;';
                }
                if($next = nextFile($file,$dir)) {
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                echo '<a href="'.$dir.'--'.$next.'.html">';
                        }else{
                                echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.$next.'&d='.$dir.'">';
                        }
                        if(file_exists("next.png")){
                                echo '<img src="next.png" border="0" align="absmiddle"/>';
                        }else{
                                echo '&gt;';
                        }
                        echo '</a> &nbsp;&nbsp;';
                        if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                                echo '<a href="'.$dir.'--'.lastFile($dir).'.html">';
                        }else{
                                echo '<a href="'.$IG_CONFIG['galleryfile'].'?c=show&f='.lastFile($dir).'&d='.$dir.'">';
                        }
                        if(file_exists("next2.png")){
                                echo '<img src="next2.png" border="0" align="absmiddle"/>';
                        }else{
                                echo '&gt;&gt;';
                        }
                        echo '</a>';
                }
                echo '</center>';
}//slideShowCount

function ImageDisplay ($dir, $fn) {
        global $IG_CONFIG,$print;

        $print = $print."\n<td><div align=\"center\"><a href=";
        
        if ($IG_CONFIG['directlink']) {
                $print = $print.'"'.$IG_CONFIG['imagedir'].$dir.'/'.$fn.'"';
        }else{
                if(isset($IG_CONFIG['prettyurls']) AND $IG_CONFIG['prettyurls']){
                        $print = $print.'"'.$dir.'--'.$fn.'.html"';
                }else{
                        $print = $print.'"'.$IG_CONFIG['galleryfile'].'?c=show&f='.$fn.'&d='.$dir.'"';
                }
        }
                
        if ($IG_CONFIG['imagetarget']) {
                $print = $print.' target="'.$IG_CONFIG['imagetarget'].'"';
        }
        
        $print = $print.'>';
        
        if ($IG_CONFIG['showdetails']) {
                @$showthing = explode(".",$fn);
                @$showdetails_html = $showthing[0]."<br/>";
                $print = $print.$showdetails_html;
        }
        
        $print = $print.'<img src="'.$IG_CONFIG['thumbdir'].$dir.'/'.$fn.'" class="reflect"';
        
        if (!$IG_CONFIG['heightalso']) {
                $print = $print." width=100% ";
        }
                
        $print = $print."border='0' alt='$fn'></a>";        
        $print = $print."</div> \n </td>\n";
        //echo $print;        
}//Image Display

function strl($t,$c){
if(!$c){
$p = base64_decode('PGJyLz48ZGl2IHN0eWxlPSJmb250LXNpemU6MXB0OyI+
UG93ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vd3d3LmlkdXQuY28udWsvIiB0YXJn
ZXQ9Il9ibGFuayI+SWR1dCBHYWxsZXJ5PC9hPiAyLjE8L2Rpdj4=');
return $t.$p;
}else{
return $t;
}
}

function nextFile($file,$dir){
        global $IG_CONFIG;
        $result = opendir($IG_CONFIG['imagedir'].$dir);
        $buffer = Array();
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['thumbdir'])) {
                        $buffer[] = $fn;
                }
        }
        $buffer = sortFiles($buffer);
        for($i=0;$i<count($buffer);$i++){
                if($buffer[$i] == $file){
                        $i1 = $i+1;
                        if(isset($buffer[$i1])){
                                return $buffer[$i1];
                        }
                }
        }
        return false;
}//nextFile

function prevFile($file,$dir){
        global $IG_CONFIG;
                $result = opendir($IG_CONFIG['imagedir'].$dir);
        $buffer = Array();
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['thumbdir'])) {
                        $buffer[] = $fn;
                }
        }
        $buffer = sortFiles($buffer);
        for($i=0;$i<count($buffer);$i++){
                if($buffer[$i] == $file){
                        $i1 = $i-1;
                        if(isset($buffer[$i1])){
                                return $buffer[$i1];
                        }
                }
        }
        return false;
}//prevFile

function firstFile($dir){
        global $IG_CONFIG;
                $result = opendir($IG_CONFIG['imagedir'].$dir);
        $buffer = Array();
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['thumbdir'])) {
                        $buffer[] = $fn;
                }
        }
        $buffer = sortFiles($buffer);
        return $buffer[0];
}//firstFile

function lastFile($dir){
        global $IG_CONFIG;
                $result = opendir($IG_CONFIG['imagedir'].$dir);
        $buffer = Array();
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND $fn != "idutgallerydata.txt" AND is_dir($IG_CONFIG['thumbdir'])) {
                        $buffer[] = $fn;
                }
        }
        $buffer = sortFiles($buffer);
        $last = count($buffer)-1;
        return $buffer[$last];
}//lastFile

function sortFiles($buffer){
        global $IG_CONFIG;
        if($IG_CONFIG['gal_sort'] AND $IG_CONFIG['gal_ascending']){
                sort($buffer);
        }elseif($IG_CONFIG['gal_sort'] AND !$IG_CONFIG['gal_ascending']){
                rsort($buffer);
        }elseif(!$IG_CONFIG['gal_sort'] AND !$IG_CONFIG['gal_ascending']){
                $buffer = array_reverse($buffer);
        }
        return $buffer;
}//sortFiles

function showDetails($file,$dir){
        global $IG_CONFIG;
        @$showthing = explode(".",$file);
        @$showdetails_html = "<br/>".$showthing[0]."<br/><br/>";
        echo $showdetails_html;
}

function showCommentBox($dir,$file){
        global $IG_CONFIG;
        ?>
        <center>
        <form action="<?php echo $IG_CONFIG['galleryfile']; ?>?f=<?php echo $file; ?>&d=<?php echo $dir; ?>" method="post">
        <?php
        if($IG_CONFIG['publiccomments']){
                echo "Post a comment for all visitors to see:<br/>";
        }else{
                echo "Post a comment for the album author to see:<br/>";
        }
        ?>
        <table>
        <tr><td align="right"><b>Name:</b></td><td align="left"><input class="boxes" type="text" name="commentname" size="20"/></td>
        <td align="right"><b>Email:</b></td><td align="right"><input class="boxes" type="text" name="commentemail" size="20"/></td></tr>
        <tr><td align="right"><b>Comment:</b></td><td align="left" colspan="3"><textarea class="boxes" name="commentcomment" cols="60" rows="3"></textarea></td></tr>
        <tr><td colspan="4" align="center"><input type="hidden" name="d" value="<?php echo $dir;?>"/>
        <input type="hidden" name="f" value="<?php echo $file;?>"/>
        <input type="hidden" name="c" value="docommentsave"/>
        <input style="font-size:9px;" type="submit" value="Post Comment"/></td></tr>
        </table>
        </form>
        </center>
        <?php
}

function doSaveComment($dir,$file,$commentname,$commentemail,$commentcomment){
        global $IG_CONFIG;
        if($IG_CONFIG['allowcomments'] and $IG_CONFIG['usedatafiles']){
                $comment[name] = stripslashes(str_replace("|","/",strip_tags($commentname)));
                $comment[email] = stripslashes(str_replace("|","/",strip_tags($commentemail)));
                $comment[comment] = stripslashes(str_replace("|","/",strip_tags($commentcomment)));
                $doit = true;
                if(strlen($comment[name]) < 2){
                        echo "<br/>Please enter a name with more letters.<br/>";
                        $doit = false;
                }
                if(strlen($comment[email]) < 5){
                        echo "<br/>Please enter a valid email address.<br/>";
                        $doit = false;
                }
                if(strlen($comment[comment]) < 1){
                        echo "<br/>Please enter a comment.<br/>";
                        $doit = false;
                }
                if($doit){
                        $date = date('Y-m-d H:i');
                        $newline = "\ncomment|$file|$date|$comment[name]|$comment[email]|$comment[comment]";
                        
                        $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'a');
                        if(fwrite($handle, $newline) === FALSE){
                                echo "Cannot write to album data file.";
                        }else{
                                echo "<b>Your comment has been posted.</b>";
                        }
                        fclose($handle);
                }else{
                        echo "<br/><b>Your comment has not been posted.</b><br/>";
                }
        }
        showImage($dir,$file);
}

function showComments($dir,$file){
        global $IG_CONFIG;
        if($IG_CONFIG['usedatafiles'] and ($IG_CONFIG['allowcomments'] or $IG_CONFIG['publiccomments']) and file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                ?>
                <script>
                function toggleBlock(elementId) {
                        var element = document.getElementById(elementId);
                        if(element.style.visibility == 'hidden'){
                                element.style.visibility = 'visible';
                                element.style.height = 'auto';
                                document.getElementById('expand').innerText = 'Hide comments...';
                        }else{
                                element.style.height = '0px';
                                element.style.visibility = 'hidden';
                                document.getElementById('expand').innerText = 'Expand comments...';
                        }
                }
                </script>
                <?php
                if($IG_CONFIG['autohidecomments']){
                        echo ' <a id="expand" href="javascript:toggleBlock(\'comments\');" style="float:left; padding:20px;">Show comments...</a>
                        <div id="comments" style="visibility:hidden;height:0px;" class="commentblock">';
                }else{
                        echo '<div id="comments" style="visibility:visible;height:auto;" class="commentblock">';
                }
                if($IG_CONFIG['publiccomments']){
                        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                        $paste = 0;
                        foreach ($lines as $line) {
                                $line = explode("|",$line);
                                if($line[0] == "comment" and $line[1] == $file){
                                        echo'<div class="commentbox';
                                        if($paste++%2) echo 'odd';
                                        echo'">
                                        <span style="float:right;">'.$line[2].'</span>
                                        <b>'.$line[3].'</b><br/>
                                        <hr>
                                        '.$line[5].'
                                        </div>';
                                }
                        }
                }
                if($IG_CONFIG['allowcomments']) showCommentBox($dir,$file);
                echo '</div>';
        }
}//showComments

function RemoveXSS($val) {
   $val = strip_tags($val);
   $val = preg_replace('/([\x00-\x08])/', '', $val);
   $val = preg_replace('/([\x0b-\x0c])/', '', $val);
   $val = preg_replace('/([\x0e-\x19])/', '', $val);
   $search = 'abcdefghijklmnopqrstuvwxyz';
   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $search .= '1234567890!@#$%^&*()';
   $search .= '~`";:?+/={}[]-_|\'\\';
   for ($i = 0; $i < strlen($search); $i++) {
      $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
   }
   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
   $ra = array_merge($ra1, $ra2);

   $found = true;
   while ($found == true) {
      $val_before = $val;
      for ($i = 0; $i < sizeof($ra); $i++) {
         $pattern = '/';
         for ($j = 0; $j < strlen($ra[$i]); $j++) {
            if ($j > 0) {
               $pattern .= '(';
               $pattern .= '(&#[xX]0{0,8}([9ab]);)';
               $pattern .= '|';
               $pattern .= '|(&#0{0,8}([9|10|13]);)';
               $pattern .= ')*';
            }
            $pattern .= $ra[$i][$j];
         }
         $pattern .= '/i';
         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
         $val = preg_replace($pattern, $replacement, $val);
         if ($val_before == $val) {
            $found = false;
         }
      }
   }
   return $val;
}//RemoveXSS
?>
