<?php
/* Idut Gallery 2.1 (beta)
 * (c) 2005-2008 Idut - www.idut.co.uk
 * admin.php
 */
$configfile = "config.php";

require($configfile);
session_start();
doLogin();
adminHeader();
//print_r($_POST);
if((!isset($_GET['c']) AND !isset($_POST['c'])) OR (isset($_GET['c']) AND $_GET['c'] == "main")){
        showManage();
}elseif($_GET['c'] == "reindex" and $_GET['d']){
        doReindex($_GET['d']);
}elseif($_GET['c'] == "delete" and $_GET['d']){
        doDeleteDir($_GET['d']);
}elseif($_GET['c'] == "fdelete" and $_GET['d'] and $_GET['f']){
        doDeleteFile($_GET['d'],$_GET['f']);
}elseif($_GET['c'] == "setcover" and $_GET['d'] and $_GET['f']){
        doSetCover($_GET['d'],$_GET['f']);
}elseif($_GET['c'] == "upload" and $_GET['d']){
        showUpload($_GET['d']);
}elseif($_POST['c'] == "doupload" and $_POST['d'] and isset($_FILES['file'])){
        doUpload($_POST['d'],$_FILES['file']);
}elseif($_GET['c'] == "new"){
        showNew();
}elseif($_GET['c'] == "donew" and $_GET['d']){
        doNew($_GET['d']);
}elseif($_GET['c'] == "newdatafile" and $_GET['d']){
        doNewDataFile($_GET['d']);
}elseif($_GET['c'] == "albumdescription" and $_GET['d']){
        showAlbumDescription($_GET['d']);
}elseif($_POST['c'] == "doalbumdescription" and $_POST['d'] and $_POST['description']){
        doAlbumDescription($_POST['d'],$_POST['description']);
}elseif($_GET['c'] == "imagedescription" and $_GET['d'] and $_GET['f']){
        showImageDescription($_GET['d'],$_GET['f']);
}elseif($_POST['c'] == "doimagedescription" and $_POST['d'] and $_POST['f'] and $_POST['description']){
        doImageDescription($_POST['d'],$_POST['f'],$_POST['description']);
}elseif($_GET['c'] == "comments" and $_GET['d'] and $_GET['f']){
        showComments($_GET['d'],$_GET['f']);
}elseif($_GET['c'] == "deletecomment" and $_GET['d'] and $_GET['f'] and $_GET['date'] and $_GET['name']){
        delComment($_GET['d'],$_GET['f'],$_GET['date'],$_GET['name']);
}elseif($_GET['c'] == "editcomment" and $_GET['d'] and $_GET['f'] and $_GET['date'] and $_GET['name']){
        editComment($_GET['d'],$_GET['f'],$_GET['date'],$_GET['name']);
}elseif($_POST['c'] == "doeditcomment" and $_POST['d'] and $_POST['f'] and $_POST['date'] and $_POST['name'] and $_POST['comment']){
        doEditComment($_POST['d'],$_POST['f'],$_POST['date'],$_POST['name'],$_POST['comment']);
}elseif($_GET['c'] == "index"){
        showIndex();
}elseif($_GET['c'] == "settings"){
        showSettings();
}elseif($_POST['c'] == "dosettings" AND is_array($_POST['con'])){
        saveSettings($_POST['con']);
}elseif($_GET['c'] == "about"){
        showAbout();
}else{
        showManage();
}
echo '</table>';
exit;

function doLogin(){
        global $IG_CONFIG;
        if(isset($_SESSION['user']) AND isset($_SESSION['pass'])){
                if($_SESSION['user'] != $IG_CONFIG['admin_user'] OR $_SESSION['pass'] != md5($IG_CONFIG['admin_pass'])){
                        unset($_SESSION['user']);
                        unset($_SESSION['pass']);
                        doLogin();
                        exit;
                }
        }elseif(isset($_POST['username']) AND isset($_POST['password'])){
                if($_POST['username'] == $IG_CONFIG['admin_user'] AND $_POST['password'] == $IG_CONFIG['admin_pass']){
                        $_SESSION['user'] = $_POST['username'];
                        $_SESSION['pass'] = md5($_POST['password']);
                }else{
                        echo "<b>Error:</b> your username and password were not recognised. Please try again";
                        exit;
                }
        }else{
                $form_to = "http://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
                if(isset($_SERVER["QUERY_STRING"]))
                $form_to = $form_to ."?". $_SERVER["QUERY_STRING"];
                adminHeader();
                ?>
                <table class="canvas" align="center"><tr><td align="center">
                <form method="post" action="<?php echo $form_to; ?>">
                <table border=0 width=350 align="center" >
                <TR>
                <TD>User Name:</TD>
                <TD><input type="text" name="username" size=20></TD></TR>
                <TR>
                <TD>Password:</TD>
                <TD><input type="password" name="password" size=20></TD>
                </TR>
                </table>
                <input type="submit" value="Login"><br/><br/>To log out, simply close this browser window.</form>
                </table>
                <?php
                exit;
        }
}//doLogin

function showManage(){
        global $IG_CONFIG;
        ?><br/>
        <b>Manage Albums</b><br/>
        <div style="text-align:right;">
        <a href="?c=new">Create new album</a><br/>
        <a href="?c=index">Index an existing directory</a>
        </div>
        <?php
                $result = opendir($IG_CONFIG['imagedir']);
                $counter = 1;
                while ($fn = readdir($result)) {
                        if ($fn != "." AND $fn != ".." AND $fn != "Thumbs.db" AND is_dir($IG_CONFIG['imagedir'].$fn)) {
                                if (is_dir($IG_CONFIG['thumbdir'].$fn)) {
                                        $datafile = @file_exists($IG_CONFIG['imagedir'].$fn.'/idutgallerydata.txt');
                                        echo '<table cellpadding="4" width="100%">';
                                        echo '<tr><td bgcolor="#cccccc"><table width="100%"><tr><td><b><a href="javascript:toggleBlock(\''.$fn.'\');">+ '.$fn.'</a></b></td>';
                                        if(!$IG_CONFIG['usedatafiles']){
                                                echo '<td width="300">&nbsp;</td>';                                                
                                        }elseif(!$datafile){
                                                echo '<td width="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="?c=newdatafile&d='.$fn.'" style="color:red;">Missing album data file</a></small></td>';
                                        }else{
                                                $lines = file($IG_CONFIG['imagedir'].$fn.'/idutgallerydata.txt');
                                                $nodesc = true;
                                                foreach ($lines as $line){
                                                        $line = explode("|",$line);
                                                        if(trim($line[0]) == "albumdesc" and $IG_CONFIG['albumdescriptions']){
                                                                echo '<td width="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="?c=albumdescription&d='.$fn.'" title="'.trim($line[1]).'">'.(substr(trim($line[1]), 0, 50)).'</a></small></td>';
                                                                $nodesc = false;
                                                                break;
                                                        }
                                                }
                                                $albumcover = null;
                                                foreach ($lines as $line){
                                                        $line = explode("|",$line);
                                                        if(trim($line[0]) == "albumcover"){
                                                                $albumcover = trim($line[1]);
                                                                break;
                                                        }
                                                }
                                                if($nodesc AND $IG_CONFIG['albumdescriptions']) echo '<td width="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="?c=albumdescription&d='.$fn.'"><i>Edit Description</i></a></small></td>';
                                        }
                                        echo '<td width="70"><a href="?c=upload&d='.$fn.'">Upload</a></td><td width="70"><a href="?c=reindex&d='.$fn.'">Reindex</a></td><td width="70"><a href="#" onclick="javascript:delAlbum(\''.$fn.'\');">Delete</a></td></tr></table></td></tr>';
                                        echo '</table>';
                                        $dir = $fn;                                                
                                        $maindir = $IG_CONFIG['imagedir'].$fn;
                                        $mydir = @opendir($maindir);
                        
                                        $fn = @readdir($mydir);        
                                        if (!$fn) {
                                                die("Directory does not exist!");
                                        }
                                        
                                        $action = closedir($mydir);
                                        $mydir = opendir($maindir);
                                        echo '<div id="'.$dir.'" style="visibility:hidden; height:0px;"><table cellpadding="4" width="100%">';
                                        while ($img = readdir($mydir)) {
                                        if($img != "." AND $img != ".." AND $img != "Thumbs.db"  AND $img != "idutgallerydata.txt"){
                                                        echo '<tr><td bgcolor="#ffffff"><table width="100%"><tr>';
                                                        if($img == $albumcover){
                                                                echo '<td><img src="'.$IG_CONFIG['thumbdir'].$dir.'/'.$img.'" width="30" height="20" class="thumb" style="border:3px double red;"/> <b>'.$img.'</b> <small>[<a href="#" onclick="javascript:alert(\'This photo is set as the album cover. To change the cover, click on the title of another photo in this album.\')">current cover</a>]</small></td>';
                                                        }else{
                                                                echo '<td><img src="'.$IG_CONFIG['thumbdir'].$dir.'/'.$img.'" width="30" height="20" class="thumb"/> <a href="#" onclick="javascript:setCover(\''.$dir.'\',\''.$img.'\');">'.$img.'</a></td>';
                                                        }
                                                        if($datafile AND $IG_CONFIG['usedatafiles']){
                                                                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                                                                $nodesc = true;
                                                                foreach ($lines as $line) {
                                                                        $line = explode("|",$line);
                                                                        if($line[0] == "imagedesc" AND $line[1] == $img AND $IG_CONFIG['imagedescriptions']){
                                                                                echo '<td width="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="?c=imagedescription&d='.$dir.'&f='.$img.'" title="'.trim($line[2]).'">'.(substr(trim($line[2]), 0, 50)).'</a></small></td>';
                                                                                $nodesc = false;
                                                                                break;
                                                                        }
                                                                }
                                                                if($nodesc AND $IG_CONFIG['imagedescriptions']) echo '<td width="300">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small><a href="?c=imagedescription&d='.$dir.'&f='.$img.'"><i>Edit Description</i></a></small></td>';
                                                        }
                                                        if($datafile AND $IG_CONFIG['usedatafiles']){
                                                                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                                                                $comments = 0;
                                                                foreach ($lines as $line) {
                                                                        $line = explode("|",$line);
                                                                        if($line[0] == "comment" AND $line[1] == $img AND $IG_CONFIG['allowcomments']){
                                                                                $comments++;
                                                                        }
                                                                }
                                                                if($comments > 0){
                                                                        echo '<td width="90"><a href="?c=comments&d='.$dir.'&f='.$img.'">Comments</a><small> ('.$comments.')</small></td>';
                                                                }else{
                                                                        echo '<td width="90"></td>';
                                                                }
                                                        }else{
                                                                echo '<td width="90"></td>';
                                                        }
                                                        //if($datafile) echo '<td width="90"><a href="?c=comments&d='.$dir.'&f='.$img.'">Comments</a><small> (10)</small></td>';
                                                        echo '<td width="70"><a href="#" onclick="javascript:delImage(\''.$dir.'\',\''.$img.'\');">Delete</a></td>';
                                                        echo '</tr></table></td></tr>';
                                                }
                                    }
                                        $action = closedir($mydir);
                                        echo "</table></div><br/>";
                                }
                        }        
                }
}//showMain

function doDeleteDir($dir){
        global $IG_CONFIG;
        //REMOVE IMAGE AND THUMBNAIL DIRS
        ?>
        <a href="javascript:toggleBlock('IGmoredetails');">Click here to see results of album deletion</a>
        <div id="IGmoredetails" style="visibility:hidden; height:0px; padding-left:30px">
        <?php
        $tdir = $IG_CONFIG['thumbdir'].$dir;
        if (is_dir($tdir)) {
                $mydir = opendir($tdir);
                while (false !== ($fn = readdir($mydir))) {
                        if ($fn == "." || $fn == "..") continue; 
                        if(unlink($tdir."/".$fn)){
                                echo $fn." thumbnail deleted.<br/>";
                        }else{
                                echo "<b>Could not delete thumbnail $fn.</b><br/>";
                        }                
                }
        
                $action = closedir($mydir);
                if(rmdir($tdir)){
                        echo "Thumbnail directory deleted.<br/>";
                }else{
                        echo "<b>Could not delete thumbnail directory.</b><br/>";
                }
        } else {
                 echo "Thumbnail directory does not exist.<br/>";
        }
        
        $tdir = $IG_CONFIG['imagedir'].$dir;
        if (is_dir($tdir)) {
                $mydir = opendir($tdir);
                while (false !== ($fn = readdir($mydir))) {
                        if ($fn == "." || $fn == "..") continue; 
                        if(unlink($tdir."/".$fn)){
                                echo $fn." deleted.<br/>";
                        }else{
                                echo "<b>Could not delete $fn.</b><br/>";
                        }                
                }
        
                $action = closedir($mydir);
                if(rmdir($tdir)){
                        echo "Directory deleted.<br/>";
                }else{
                        echo "<b>Could not delete directory.</b><br/>";
                }
        } else {
                 echo "Directory does not exist.<br/>";
        }
        echo '</div>';
        showManage();
}//doDeleteDir

function doDeleteFile($dir,$file){
        global $IG_CONFIG;
        //REMOVE IMAGE AND THUMBNAIL DIRS
        ?>
        <a href="javascript:toggleBlock('IGmoredetails');">Click here to see results of deleting file</a>
        <div id="IGmoredetails" style="visibility:hidden; height:0px; padding-left:30px">
        <?php
        if(unlink($IG_CONFIG['thumbdir'].$dir."/".$file)){
                echo "$file thumbnail deleted.<br/>";
        }else{
                echo "<b>Unable to delete $file thumbnail</b><br/>";
        }
        
        if(unlink($IG_CONFIG['imagedir'].$dir."/".$file)){
                echo "$file deleted.<br/>";
        }else{
                echo "<b>Unable to delete $file</b><br/>";
        }
        echo '</div>';
        showManage();
}//doDeleteFile

function doSetCover($dir,$file){
        global $IG_CONFIG;
        //REMOVE IMAGE AND THUMBNAIL DIRS
        ?>
        <?php
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $newlines = null;
        $done = false;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "albumcover"){
                        $newlines .= "albumcover|$file\n";
                        $done = true;
                }else{
                        $newlines .= $line;
                }
        }
        if($done == false){
                $newlines = "albumcover|$file\n" . $newlines;
        }

        $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'w');

    if(fwrite($handle, $newlines) === FALSE){
        echo "Cannot write to album data file.";
    }else{
            echo "<b>Album cover has been changed.</b>";
        }
    fclose($handle);
        showManage();
}//doDeleteFile

function doReindex($dir){
        global $IG_CONFIG;
        //REMOVE THUMBNAIL DIR AND REMAKE IT
        ?>

        <a href="javascript:toggleBlock('IGmoredetails');">Click here to see results of reindex</a>
        <div id="IGmoredetails" style="visibility:hidden; height:0px; padding-left:30px">
        <?php
        $tdir = $IG_CONFIG['thumbdir'].$dir;
        if (is_dir($tdir)) {
                $mydir = opendir($tdir);
                while (false !== ($fn = readdir($mydir))) {
                        if ($fn == "." || $fn == "..") continue; 
                        $action = unlink($tdir."/".$fn);                
                }
        
                $action = closedir($mydir);
                $action = rmdir($tdir);
        }
        
        $opend = $IG_CONFIG['thumbdir'].$dir;
        if (is_dir($IG_CONFIG['thumbdir'])) {
                @$opend_result = mkdir($opend , 0777);
        } else {
                die("Thumbnail directory does not exist.");
        }
                
        if ($opend_result) {
                $result = opendir($IG_CONFIG['imagedir'].$dir);
                echo "<b>Creating thumbnails of the following files</b>:<br/><br/>";
                while ($fn = readdir($result)) {
                        if ($fn != "." AND $fn != ".." AND !is_dir($fn) AND (
                                                                                                                                stristr($fn,'jpg') OR
                                                                                                                                stristr($fn,'jpeg') OR
                                                                                                                                stristr($fn,'gif') OR
                                                                                                                                stristr($fn,'png'))) {
                                
                                echo $fn."<br/><small>";
                                $size = getimagesize($IG_CONFIG['imagedir'].$dir."/".$fn);
                                $size2 = filesize($IG_CONFIG['imagedir'].$dir."/".$fn)/1024;
                                $size2 = round($size2, 1);
                                echo $size2."kb &nbsp;&nbsp;&nbsp;";
                                echo $size[0]." x ".$size[1]."</small><br/><br/>";
                                
                                if(!$IG_CONFIG['crop']){
                                        if ($size[0] <= $IG_CONFIG['width']) {
                        
                                                //copy
                                                $copyfile = $opend."/".$fn;
                                                $original_file = $IG_CONFIG['imagedir'].$dir."/".$fn;
                                                if(!copy($original_file , $copyfile)){
                                                        echo "<font color=red>The previous file was unsucessful</font>";
                                                }
                                        
                                        } else {
                                        
                                                $factor = $size[0] / $IG_CONFIG['width'];
                                                $new_length = intval($size[1] / $factor);
                                                $width2 = $IG_CONFIG['width'];
                                                        
                                                if ($IG_CONFIG['heightalso'] && ($new_length > $IG_CONFIG['height'])) {
                                                        $factor = $IG_CONFIG['height'] / $size[1];
                                                        $width2 = (int)($size[0] * $factor);
                                                        $new_length = $IG_CONFIG['height'];                                        
                                                }
                                                $ex = explode(".",$fn);
                                                $ex = $ex[(count($ex)-1)];
                                                if($ex == "jpg" or $ex == "jpeg"){
                                                        $src_img = imagecreatefromjpeg($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                                }elseif($ex == "gif"){
                                                        $src_img = imagecreatefromgif($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                                }elseif($ex == "png"){
                                                        $src_img = imagecreatefrompng($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                                }else{
                                                        $src_img = imagecreatefromjpeg($IG_CONFIG['imagedir'].$dir."/".$fn);
                                                }
                                                $dst_img = imagecreatetruecolor($width2,$new_length); 
                                
                                                $src_all = getimagesize($IG_CONFIG['imagedir'].$dir."/".$fn);
                                                $src_width = $src_all[0];
                                                $src_height = $src_all[1];
                                                if(!imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0,
                                                        $width2, $new_length, $src_width, $src_height)){
                                                        echo "<font color=red>The previous file was unsucessful</font>";
                                                }
                                                
                                                if($ex == "jpg" or $ex == "jpeg"){
                                                        imagejpeg($dst_img, $opend."/".$fn, $IG_CONFIG['largequality']); 
                                                }elseif($ex == "gif"){
                                                        imagegif($dst_img, $opend."/".$fn);
                                                }elseif($ex == "png"){
                                                        imagepng($dst_img, $opend."/".$fn);
                                                }else{
                                                        imagejpeg($dst_img, $opend."/".$fn, $IG_CONFIG['largequality']);
                                                }
                                                imagedestroy($src_img); 
                                                imagedestroy($dst_img);
                                        }
                                }else{
                                        //CROP        
                                        $src_all = getimagesize($IG_CONFIG['imagedir'].$dir."/".$fn);
                                        $src_width = $src_all[0];
                                        $src_height = $src_all[1];
                                        $ex = explode(".",$fn);
                                        $ex = $ex[(count($ex)-1)];
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                $src_img = imagecreatefromjpeg($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                        }elseif($ex == "gif"){
                                                $src_img = imagecreatefromgif($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                        }elseif($ex == "png"){
                                                $src_img = imagecreatefrompng($IG_CONFIG['imagedir'].$dir."/".$fn); 
                                        }else{
                                                $src_img = imagecreatefromjpeg($IG_CONFIG['imagedir'].$dir."/".$fn);
                                        }
                                        $dst_img = imagecreatetruecolor($IG_CONFIG['width'],$IG_CONFIG['height']); 
                                        
                                        $ratio = (double)($src_height / $IG_CONFIG['height']);
                                        $cpy_width = round($IG_CONFIG['width'] * $ratio);
                                        if ($cpy_width > $src_width){
                                           $ratio = (double)($src_width / $IG_CONFIG['width']);
                                           $cpy_width = $src_width;
                                           $cpy_height = round($IG_CONFIG['height'] * $ratio);
                                           $xOffset = 0;
                                           $yOffset = round(($src_height - $cpy_height) / 2);
                                        } else {
                                           $cpy_height = $src_height;
                                           $xOffset = round(($src_width - $cpy_width) / 2);
                                           $yOffset = 0;
                                        }
                                        
                                        if(!imagecopyresampled($dst_img, $src_img, 0, 0, $xOffset, $yOffset, $IG_CONFIG['width'], $IG_CONFIG['height'], $cpy_width, $cpy_height)){
                                                echo "<font color=red>The previous file was unsucessful</font>";
                                        } 
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                imagejpeg($dst_img, $opend."/".$fn, $IG_CONFIG['largequality']); 
                                        }elseif($ex == "gif"){
                                                imagegif($dst_img, $opend."/".$fn);
                                        }elseif($ex == "png"){
                                                imagepng($dst_img, $opend."/".$fn);
                                        }else{
                                                imagejpeg($dst_img, $opend."/".$fn, $IG_CONFIG['largequality']);
                                        }
                                        imagedestroy($src_img); 
                                        imagedestroy($dst_img);

                                }
                        }
                }        
                closedir($result);
        }
        echo '</div>';
        showManage();
}//doReindex

function showUpload($d){
        ?>
        <form action="?" method="post" enctype="multipart/form-data" >
        Upload an image to the <?php echo $d; ?> album.<br/>
        <br/>
        <input type="file" name="file" /><br/>
        <input type="submit" value="upload" /><br/>
        <input type="hidden" name="c" value="doupload" />
        <input type="hidden" name="d" value="<?php echo $d; ?>" />
        <br/>Album thumbnails will be re-generated once file has been uploaded.
        </form>
        <?php
        if(file_exists("thinupload.php")){
                if(!file_exists("thinupload.jar")){
                        echo 'thinupload.jar is missing so the bulk upload won\'t be offered.';
                }else{
                        echo '<a href="thinupload.php?c=step1" target="_blank">Bulk Upload and Re-size - Part 1</a> (upload photos to temporary folder)<br/>
                        <a href="thinupload.php" target="_blank">Bulk Upload and Re-size - Part 2</a> (move photos from temp folder to the photo album)';
                }
        }
}//showUpload

function doUpload($dir,$file){
        global $IG_CONFIG;
        echo "<b>Upload results: </b><br/>";
        if(is_dir($IG_CONFIG['imagedir'].$dir)){
                $uploadfile = $IG_CONFIG['imagedir'].$dir."/".basename($file['name']);
                if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
                        echo $file['name']." has been uploaded to $dir album.<br/>";
                } else {
                        echo "<b>".$file['name']." could not be uploaded to the $dir album.</b><br/>";
                        switch ($file['error']) {
                           case UPLOAD_ERR_INI_SIZE:
                                   echo "The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
                                   break;
                           case UPLOAD_ERR_FORM_SIZE:
                                   echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                                   break;
                           case UPLOAD_ERR_PARTIAL:
                                   echo "The uploaded file was only partially uploaded.";
                                   break;
                           case UPLOAD_ERR_NO_FILE:
                                   echo "No file was uploaded.";
                                   break;
                           case UPLOAD_ERR_NO_TMP_DIR:
                                   echo "Missing a temporary folder.";
                                   break;
                           case UPLOAD_ERR_CANT_WRITE:
                                   echo "Failed to write file to disk";
                                   break;
                           default:
                                   echo "Unknown File Error";
                        }
                        showManage();
                        exit;
                }
                $file = $uploadfile;
                if($IG_CONFIG['modifyimage']){
                        if($IG_CONFIG['largechangesize']){
                                //RESIZE IMAGE
                                $size = getimagesize($file);
                                $size2 = filesize($file)/1024;
                                $size2 = round($size2, 1);
                                if (($size[0] >= $IG_CONFIG['largewidth'] or $size[1] >= $IG_CONFIG['largeheight']) and !$IG_CONFIG['largecrop']) {
                                        //needs to be reduced
                                        $factor = $size[0] / $IG_CONFIG['largewidth'];
                                        $new_length = intval($size[1] / $factor);
                                        $width2 = $IG_CONFIG['largewidth'];
                                        //if height is still to big
                                        if ($new_length > $IG_CONFIG['largeheight']) {
                                                $factor =  $size[1] / $IG_CONFIG['largeheight'];
                                                $width2 = intval($size[0] / $factor);
                                                $new_length = $IG_CONFIG['largeheight'];                                        
                                        }
                                        $ex = explode(".",$file);
                                        $ex = $ex[(count($ex)-1)];
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                $src_img = imagecreatefromjpeg($file); 
                                        }elseif($ex == "gif"){
                                                $src_img = imagecreatefromgif($file); 
                                        }elseif($ex == "png"){
                                                $src_img = imagecreatefrompng($file); 
                                        }else{
                                                $src_img = imagecreatefromjpeg($file); 
                                        }
                                        $dst_img = imagecreatetruecolor($width2,$new_length); 
                        
                                        $src_all = getimagesize($file);
                                        $src_width = $src_all[0];
                                        $src_height = $src_all[1];
                                        if(!imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0,
                                                $width2, $new_length, $src_width, $src_height)){
                                                echo "<font color=red>The file was unsucessful.</font>";
                                        }
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                imagejpeg($dst_img, $file, $IG_CONFIG['largequality']); 
                                        }elseif($ex == "gif"){
                                                imagegif($dst_img, $file);
                                        }elseif($ex == "png"){
                                                imagepng($dst_img, $file);
                                        }else{
                                                imagejpeg($dst_img, $file, $IG_CONFIG['largequality']);
                                        }
                                        imagedestroy($src_img); 
                                        imagedestroy($dst_img);
                                }elseif($IG_CONFIG['largecrop']){
                                        //CROP        
                                        $src_all = getimagesize($file);
                                        $src_width = $src_all[0];
                                        $src_height = $src_all[1];
                                        
                                        $ex = explode(".",$file);
                                        $ex = $ex[(count($ex)-1)];
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                $src_img = imagecreatefromjpeg($file); 
                                        }elseif($ex == "gif"){
                                                $src_img = imagecreatefromgif($file); 
                                        }elseif($ex == "png"){
                                                $src_img = imagecreatefrompng($file); 
                                        }else{
                                                $src_img = imagecreatefromjpeg($file); 
                                        }
                                        $dst_img = imagecreatetruecolor($IG_CONFIG['largewidth'],$IG_CONFIG['largeheight']); 
                                        
                                        $ratio = (double)($src_height / $IG_CONFIG['largeheight']);
                                        $cpy_width = round($IG_CONFIG['largewidth'] * $ratio);
                                        if ($cpy_width > $src_width){
                                           $ratio = (double)($src_width / $IG_CONFIG['largewidth']);
                                           $cpy_width = $src_width;
                                           $cpy_height = round($IG_CONFIG['largeheight'] * $ratio);
                                           $xOffset = 0;
                                           $yOffset = round(($src_height - $cpy_height) / 2);
                                        } else {
                                           $cpy_height = $src_height;
                                           $xOffset = round(($src_width - $cpy_width) / 2);
                                           $yOffset = 0;
                                        }
                                        
                                        if(!imagecopyresampled($dst_img, $src_img, 0, 0, $xOffset, $yOffset, $IG_CONFIG['largewidth'], $IG_CONFIG['largeheight'], $cpy_width, $cpy_height)){
                                                echo "<font color=red>The file was unsucessful</font>";
                                        } 
                                        if($ex == "jpg" or $ex == "jpeg"){
                                                imagejpeg($dst_img, $file, $IG_CONFIG['largequality']); 
                                        }elseif($ex == "gif"){
                                                imagegif($dst_img, $file);
                                        }elseif($ex == "png"){
                                                imagepng($dst_img, $file);
                                        }else{
                                                imagejpeg($dst_img, $file, $IG_CONFIG['largequality']);
                                        }
                                        imagedestroy($src_img); 
                                        imagedestroy($dst_img);
                
                                }
                        }//change size
                        if($IG_CONFIG['largewatermark'] and $IG_CONFIG['largewatermarklocation'] and file_exists($file)){
                                $ex = explode(".",$file);
                                $ex = $ex[(count($ex)-1)];
                                if($ex == "jpg" or $ex == "jpeg"){
                                        $background = imagecreatefromjpeg($file); 
                                }elseif($ex == "gif"){
                                        $background = imagecreatefromgif($file); 
                                }elseif($ex == "png"){
                                        $background = imagecreatefrompng($file); 
                                }else{
                                        $background = imagecreatefromjpeg($file); 
                                }
                                $foreground = imagecreatefrompng($IG_CONFIG['largewatermarklocation']);
                                $insertWidth = imagesx($foreground);
                                $insertHeight = imagesy($foreground);
                                
                                $imageWidth = imagesx($background);
                                $imageHeight = imagesy($background);
                                
                                $overlapX = $imageWidth-$insertWidth-5;
                                $overlapY = $imageHeight-$insertHeight-5;
                                imagecolortransparent($foreground,imagecolorat($foreground,0,0));
                                imagecopymerge($background,$foreground,$overlapX,$overlapY,0,0,$insertWidth,$insertHeight,$IG_CONFIG['largewatermarkalpha']);
                                
                                if($ex == "jpg" or $ex == "jpeg"){
                                        if(!imagejpeg($background, $file, $IG_CONFIG['largequality'])){
                                                echo "Could not add watermark.<br/>";
                                        }else{
                                                echo "Watermark added.<br/>";
                                        } 
                                }elseif($ex == "gif"){
                                        if(!imagegif($background, $file)){
                                                echo "Could not add watermark.<br/>";
                                        }else{
                                                echo "Watermark added.<br/>";
                                        } 
                                }elseif($ex == "png"){
                                        if(!imagepng($background, $file)){
                                                echo "Could not add watermark.<br/>";
                                        }else{
                                                echo "Watermark added.<br/>";
                                        } 
                                }else{
                                        if(!imagejpeg($background, $file, $IG_CONFIG['largequality'])){
                                                echo "Could not add watermark.<br/>";
                                        }else{
                                                echo "Watermark added.<br/>";
                                        } 
                                }
                                imagedestroy($background); 
                                imagedestroy($foreground);
                        }else{
                                echo "Watermark not added.<br/>";
                        }
                }//modify image
        }else{
                echo "The $dir album does not exist.<br/>";
        }
        doReindex($dir);
}//doUpload


function showNew(){
        ?>
        <form action="?" method="GET">
        Create a new album.
        <br/><br/>
        Album name: <input type="text" name="d" /> <small>(A-Z, a-z, 0-9)</small><br/><br/>
        <input type="submit" value="create" />
        <input type="hidden" name="c" value="donew" />
        </form>
        <?php
}//showNew

function doNew($dir){
        global $IG_CONFIG;
        if(@is_dir($IG_CONFIG['imagedir'])){
                if (@mkdir($IG_CONFIG['imagedir'].$dir)) {
                        echo "$dir album has been created.<br/>";
                        if (@mkdir($IG_CONFIG['thumbdir'].$dir)) {
                                echo "$dir thumbnail directory has been created.<br/>";
                                if (!$handle = @fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'a')) {
                                         echo "Could not create an <i>idutgallerydata.txt</i> data file in the ".$IG_CONFIG['imagedir'].$dir." directory. This album will therefore not support descriptions or comments.";
                                }
                                @fclose($handle);        
                        } else {
                                echo "<b>Could not create $dir thumbnail directory </b><br/>";
                        }
                } else {
                        echo "<b>Could not create $dir album</b><br/>";
                }
        }else{
                echo "The gallery image folder does not exist. Please check the config file.<br/>";
        }
        showManage();
}//doNew

function doNewDataFile($dir){
        global $IG_CONFIG;
        if(@is_dir($IG_CONFIG['imagedir'].$dir)){
                $handle = @fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'a');
                if ($handle) {
                        echo "Data file in $dir album has been created or already exists. This album will now support discriptions and comments.<br/><br/>";
                }else{
                        echo "Could not create an <i>idutgallerydata.txt</i> data file in the ".$IG_CONFIG['imagedir'].$dir." directory. This album will therefore not support descriptions or comments.";
                        echo '<br/><br/>Make sure that '.$IG_CONFIG['imagedir'].$dir.' is CHMOD 0777 so that the data file can be created';
                }
                @fclose($handle);        
        }else{
                echo "$dir album does not exist.<br/>";
        }
        showManage();
}//doNewDataFile

function showIndex(){
        global $IG_CONFIG;
        echo 'You can index albums that you have uploaded by another method like FTP.
                  Just place them in a new folder in the '.$IG_CONFIG['imagedir'].' directory and they will appear below.<br/><br/>';
        $nodirs = true;
        echo '&nbsp;&nbsp;&nbsp;<b>Choose a folder to make into an album:</b><br/>';
        $result = opendir($IG_CONFIG['imagedir']);
        while (false !== ($fn = readdir($result))) {
                if ($fn != "." AND $fn != ".." AND is_dir($IG_CONFIG['imagedir'].$fn)) {
                        if (!is_dir($IG_CONFIG['thumbdir'].$fn)) {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?c=reindex&d='.$fn.'">Index '.$fn.' directory</a><br/>';
                                $nodirs = false;
                        }
                }
        }
        closedir($result);
        if($nodirs) echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;There are no folders in the '.$IG_CONFIG['imagedir'].' directory that do not already have thumbnail directories in '.$IG_CONFIG['thumbdir'].'.';
}//showIndex

function showAlbumDescription($dir){
        global $IG_CONFIG;
        if(file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                ?>
                <form action="?" method="POST">
                <?php echo $dir;?> album description.
                <br/><br/>
                <input type="text" name="description" size="100" value="<?php
                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                foreach ($lines as $line) {
                        $line = explode("|",$line);
                        if($line[0] == "albumdesc"){
                                echo $line[1];
                                break;
                        }
                }
                
                ?>"/>
                <br/><br/>
                <input type="submit" value="save" /> or <input type="submit" name="description" value="Clear Description" />
                <input type="hidden" name="c" value="doalbumdescription" />
                <input type="hidden" name="d" value="<?php echo $dir;?>" />
                </form>
                <?php
        }else{
                echo "The data file for this album is missing.";
        }
}//showAlbumDescription

function doAlbumDescription($dir,$description){
        global $IG_CONFIG;
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $newlines = null;
        $done = false;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "albumdesc"){
                        if($description != "Clear Description"){
                                $newlines .= "albumdesc|$description\n";
                        }
                        $done = true;
                }else{
                        $newlines .= $line;
                }
        }
        if($done == false AND $description != "Clear Description"){
                $newlines = "albumdesc|$description\n" . $newlines;
        }
        
        $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'w');

    if(fwrite($handle, $newlines) === FALSE){
        echo "Cannot write to album data file.";
    }else{
            echo "Album description has been changed.";
        }
    fclose($handle);
        showManage();
}//doAlbumDescription

function showImageDescription($dir,$file){
        global $IG_CONFIG;
        if(file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                ?>
                <form action="?" method="POST">
                <?php echo $dir;?> album, <?php echo $file;?> description.
                <br/><br/>
                <input type="text" name="description" size="100" value="<?php
                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                foreach ($lines as $line) {
                        $line = explode("|",$line);
                        if($line[0] == "imagedesc" and $line[1] == $file){
                                echo $line[2];
                                break;
                        }
                }
                
                ?>"/>
                <br/><br/>
                <input type="submit" value="save" /> or <input type="submit" name="description" value="Clear Description" />
                <input type="hidden" name="c" value="doimagedescription" />
                <input type="hidden" name="d" value="<?php echo $dir;?>" />
                <input type="hidden" name="f" value="<?php echo $file;?>" />
                </form>
                <?php
        }else{
                echo "The data file for this album is missing.";
        }
}//showImageDescription

function doImageDescription($dir,$file,$description){
        global $IG_CONFIG;
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $newlines = null;
        $done = false;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "imagedesc" and $linex[1] == $file){
                        if($description != "Clear Description"){
                                $newlines .= "\nimagedesc|$file|$description";
                        }
                        $done = true;
                }else{
                        $newlines .= $line;
                }
        }
        if($done == false AND $description != "Clear Description"){
                $newlines .= "\nimagedesc|$file|$description";
        }
        
        $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'w');

    if(fwrite($handle, $newlines) === FALSE){
        echo "Cannot write to album data file.";
    }else{
            echo "Image description has been changed.";
        }
    fclose($handle);
        showManage();
}//doImageDescription

function showComments($dir,$file){
        global $IG_CONFIG;
        ?>
        <script>
        function delComment(dir,file,date,name){
                var conf = confirm("This will delete this comment.");
                if (conf == true){
                   window.location="?c=deletecomment&d="+dir+"&f="+file+"&date="+date+"&name="+name;
                 }
        }
        </script>
        <?php
        if(file_exists($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt')){
                $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
                echo "Comments on the image $file from the $dir album.<br/><br/>";
                echo '<table cellpadding=5>';
                echo '<tr><td><b>Date</b></td><td><b>Name</b></td><td><b>Email</b></td><td><b>Action</b></td></tr>';
                foreach ($lines as $line) {
                        $line = explode("|",$line);
                        if($line[0] == "comment" and $line[1] == $file){
                                        echo "<tr bgcolor=\"#CCCCCC\"><td>$line[2]</td><td>$line[3]</td><td><a href=\"mailto:$line[4]\">$line[4]</a></td><td><a href=\"?c=editcomment&d=$dir&f=$file&date=$line[2]&name=$line[3]\">Edit</a> <a href=\"javascript:delComment('$dir','$file','$line[2]','$line[3]');\">Delete</a></td></tr><tr><td colspan=4><small>$line[5]</small><br/><br/></td></tr>";
                        }
                }
        }else{
                echo "The data file for this album is missing.";
        }
}//showComments

function delComment($dir,$file,$date,$name){
        global $IG_CONFIG;
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $newlines = null;
        $done = false;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "comment" and $linex[1] == $file and $linex[2] == $date and $linex[3] == $name){
                        $done = true;
                }else{
                        $newlines .= $line;
                }
        }

        $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'w');

    if(fwrite($handle, $newlines) === FALSE){
        echo "Cannot write to album data file.<br/>";
    }else{
            echo "Comment has been deleted.<br/><br/>";
        }
    fclose($handle);
        showComments($dir,$file);
}//delComment

function editComment($dir,$file,$date,$name){
        global $IG_CONFIG;
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $commentdata = null;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "comment" and $linex[1] == $file and $linex[2] == $date and $linex[3] == $name){
                        $commentdata = $linex;
                        break;
                }else{
                        $newlines .= $line;
                }
        }
        
        ?>
                <form action="?" method="POST">
                <?php echo $dir;?> album, <?php echo $file;?> image comment.
                <br/><br/>
                <table>
                <tr><td>Date:</td><td><?php echo $linex[2];?></td></tr>
                <tr><td>Name:</td><td><input type="text" name="comment[name]" size="30" value="<?php echo $linex[3];?>"/></td></tr>
                <tr><td>Email:</td><td><input type="text" name="comment[email]" size="30" value="<?php echo $linex[4];?>"/></td></tr>
                <tr><td>Comment:</td><td><textarea name="comment[comment]" cols="30" rows="3"><?php echo $linex[5];?></textarea></td></tr>
                <br/>
                </table><br/>
                <input type="submit" value="save" />
                <input type="hidden" name="c" value="doeditcomment" />
                <input type="hidden" name="d" value="<?php echo $dir;?>" />
                <input type="hidden" name="f" value="<?php echo $file;?>" />
                <input type="hidden" name="date" value="<?php echo $linex[2];?>" />
                <input type="hidden" name="name" value="<?php echo $linex[3];?>" />
                </form>
                <?php
}//editComment

function doEditComment($dir,$file,$date,$name,$comment){
        global $IG_CONFIG;
        $lines = file($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt');
        $newlines = null;
        $done = false;
        foreach ($lines as $line) {
                $linex = explode("|",$line);
                if($linex[0] == "comment" and $linex[1] == $file and $linex[2] == $date and $linex[3] == $name){
                        $newlines .= "\ncomment|$file|$date|$comment[name]|$comment[email]|$comment[comment]";
                        $done = true;
                }else{
                        $newlines .= $line;
                }
        }
        if($done == false){
                echo "Could not locate comment in album data file.";
        }else{        
                $handle = fopen($IG_CONFIG['imagedir'].$dir.'/idutgallerydata.txt', 'w');
        
                if(fwrite($handle, $newlines) === FALSE){
                        echo "Cannot write to album data file.";
                }else{
                        echo "Comment has been changed.";
                }
                fclose($handle);
        }
        showComments($dir,$file);
}//doEditComment

function showSettings(){
        global $configfile;
        require($configfile);
        ?>
        <script>
        function toggleTab(elementId,elementIdDiv) {
                var element = document.getElementById(elementId);
                document.getElementById('a').className = 'taboff';
                document.getElementById('b').className = 'taboff';
                document.getElementById('c1').className = 'taboff';
                document.getElementById('d').className = 'taboff';
                document.getElementById('e').className = 'taboff';
                document.getElementById('f').className = 'taboff';
                element.className = 'tabon'
                
                var elementDiv = document.getElementById(elementIdDiv);
                document.getElementById('ta').className = 'boxoff';
                document.getElementById('tb').className = 'boxoff';
                document.getElementById('tc').className = 'boxoff';
                document.getElementById('td').className = 'boxoff';
                document.getElementById('te').className = 'boxoff';
                document.getElementById('tf').className = 'boxoff';
                elementDiv.className = 'boxon';
        }
        function e(field){
                for(i=0;i<field.length;i++) {
                        var field2 = document.getElementsByName(field[i]);
                        for(j=0;j<field2.length;j++) {
                                field2[j].disabled=false;
                        }
                }
        }
        function d(field){
                for(i=0;i<field.length;i++) {
                        var field2 = document.getElementsByName(field[i]);
                        for(j=0;j<field2.length;j++) {
                                field2[j].disabled=true;
                        }
                }
        }

        </script>
        <form action="?" method="post" name="settings">
        <input type="hidden" name="c" value="dosettings"/>
        <input type="submit" name="sub" value="Save All Settings"/>
        <input type="reset" value="Undo All"/><br/><br/>
        <div class="tabs">
        <span id="a" class="tabon" onclick="javascript:toggleTab('a','ta');">Main Settings</span> 
        <span id="b" class="taboff" onclick="javascript:toggleTab('b','tb');">Viewing</span>
        <span id="c1" class="taboff" onclick="javascript:toggleTab('c1','tc');">Image</span>
        <span id="d" class="taboff" onclick="javascript:toggleTab('d','td');">Descriptions + Comments</span>
        <span id="e" class="taboff" onclick="javascript:toggleTab('e','te');">Templates</span>
        <span id="f" class="taboff" onclick="javascript:toggleTab('f','tf');">Plug-ins</span>
        </div>
        <div id="ta" class="boxon">
        <b>Main Settings</b><br/><br/>
        <table>
        <tr><td>Gallery homepage</td><td><input type="text" name="con[galleryfile]" value="<?php echo $IG_CONFIG['galleryfile'];?>" size="20" /></td><td><small>index.php</small></td></tr>
        <tr><td>Thumbnail directory</td><td><input type="text" name="con[thumbdir]" value="<?php echo $IG_CONFIG['thumbdir'];?>" size="20" /></td><td><small>./thumbnails/</small></td></tr>
        <tr><td>Image directory</td><td><input type="text" name="con[imagedir]" value="<?php echo $IG_CONFIG['imagedir'];?>" size="20" /></td><td><small>./images/</small></td></tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr><td>Gallery Title</td><td><input type="text" name="con[title]" value="<?php echo $IG_CONFIG['title'];?>" size="20" /></td><td><small>Idut Gallery</small></td></tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr><td>Admin username</td><td><input type="text" name="con[admin_user]" value="<?php echo $IG_CONFIG['admin_user'];?>" size="20" /></td><td><small>demo</small></td></tr>
        <tr><td>Admin password</td><td><input type="password" name="con[admin_pass]" value="<?php echo $IG_CONFIG['admin_pass'];?>" size="20" /></td><td><small>demo</small></td></tr>
        </table>
        </div>
        <div id="tb" class="boxoff">
        <b>Viewing Settings</b><br/><br/>
        <table>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>General Settings</b></td></tr>
        <tr><td>Show pretty URLs using HTACCESS</td><td><input type="radio" name="con[prettyurls]" value="true" <?php if($IG_CONFIG['prettyurls']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[prettyurls]" value="false" <?php if(!$IG_CONFIG['prettyurls']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Show menu on left hand side of all pages?</td><td><input type="radio" name="con[showmenu]" value="true" <?php if($IG_CONFIG['showmenu']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[showmenu]" value="false" <?php if(!$IG_CONFIG['showmenu']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Show breadcrumbs at top of all pages?</td><td><input type="radio" name="con[topinfo]" value="true" <?php if($IG_CONFIG['topinfo']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[topinfo]" value="false" <?php if(!$IG_CONFIG['topinfo']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Show number and navigation of pages/images at top of all pages?</td><td><input type="radio" name="con[print_pages_on_top]" value="true" <?php if($IG_CONFIG['print_pages_on_top']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[print_pages_on_top]" value="false" <?php if(!$IG_CONFIG['print_pages_on_top']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Show number and navigation of pages/images at bottom of all pages?</td><td><input type="radio" name="con[print_pages_on_bottom]" value="true" <?php if($IG_CONFIG['print_pages_on_bottom']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[print_pages_on_bottom]" value="false" <?php if(!$IG_CONFIG['print_pages_on_bottom']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Gallery Index</b></td></tr>
        <tr><td>Show a text list of albums on gallery main page?</td><td><input type="radio" name="con[showautomatic]" value="true" <?php if($IG_CONFIG['showautomatic']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[showautomatic]" value="false" <?php if(!$IG_CONFIG['showautomatic']) echo "checked";?> />No</td><td><small>no</small></td></tr>
        <tr><td>Show a thumbnail of each album on gallery main page?</td><td><input type="radio" name="con[showautomaticthumbs]" value="true" <?php if($IG_CONFIG['showautomaticthumbs']) echo "checked";?> onclick="javascript:e(Array('con[showautomaticthumbsname]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[showautomaticthumbs]" value="false" <?php if(!$IG_CONFIG['showautomaticthumbs']) echo "checked";?>  onclick="javascript:d(Array('con[showautomaticthumbsname]'));"/>No</td><td><small>yes</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Show name of gallery below thumbnail?</td><td><input type="radio" name="con[showautomaticthumbsname]" value="true" <?php if($IG_CONFIG['showautomaticthumbsname']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[showautomaticthumbsname]" value="false" <?php if(!$IG_CONFIG['showautomaticthumbsname']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Album Index</b></td></tr>
        <tr><td>Show thumbnails of the album on the album index?</td><td><input type="radio" name="con[albumindex]" value="true" <?php if($IG_CONFIG['albumindex']) echo "checked";?>   onclick="javascript:e(Array('con[showdetails]','con[row]','con[perrow]','con[directlink]','con[imagetarget]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[albumindex]" value="false" <?php if(!$IG_CONFIG['albumindex']) echo "checked";?>  onclick="javascript:d(Array('con[showdetails]','con[row]','con[perrow]','con[directlink]','con[imagetarget]'));"/>No</td><td><small>Yes: show index. No: go to first image. Default: yes</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Show name of image (without file extension) below thumbnail?</td><td><input type="radio" name="con[showdetails]" value="true" <?php if($IG_CONFIG['showdetails']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[showdetails]" value="false" <?php if(!$IG_CONFIG['showdetails']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Number of columns of thumbnails</td><td><input type="text" name="con[row]" value="<?php echo $IG_CONFIG['row'];?>" size="3" /></td><td><small>3</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Number of rows of thumbnails</td><td><input type="text" name="con[perrow]" value="<?php echo $IG_CONFIG['perrow'];?>" size="3" /></td><td><small>3</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Link thumbnails directly to image files?</td><td><input type="radio" name="con[directlink]" value="true" <?php if($IG_CONFIG['directlink']) echo "checked";?> onclick="javascript:e(Array('con[imagetarget]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[directlink]" value="false" <?php if(!$IG_CONFIG['directlink']) echo "checked";?> onclick="javascript:d(Array('con[imagetarget]'));"/>No</td><td><small>no</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Target direct links</td><td><input type="text" name="con[imagetarget]" value="<?php echo $IG_CONFIG['imagetarget'];?>" size="20" /></td><td><small>_blank</small></td></tr>

        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Album Order</b></td></tr>
        <tr><td>Sorting method</td><td><input type="radio" name="con[gal_sort]" value="true" <?php if($IG_CONFIG['gal_sort']) echo "checked";?> />Alphabetical <br/>
                                                          <input type="radio" name="con[gal_sort]" value="false" <?php if(!$IG_CONFIG['gal_sort']) echo "checked";?> />File System</td><td><small>Alphabetical</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Sort order of images</td><td><input type="radio" name="con[gal_ascending]" value="true" <?php if($IG_CONFIG['gal_ascending']) echo "checked";?> />Ascending <br/>
                                                          <input type="radio" name="con[gal_ascending]" value="false" <?php if(!$IG_CONFIG['gal_ascending']) echo "checked";?> />Descending</td><td><small>Ascending</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Image Viewing</b></td></tr>
        <tr><td>Slideshow (clicking image will proceed to next image)</td><td><input type="radio" name="con[slideshow]" value="true" <?php if($IG_CONFIG['slideshow']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[slideshow]" value="false" <?php if(!$IG_CONFIG['slideshow']) echo "checked";?> />No</td><td><small>Yes</small></td></tr>
        <tr><td>Show name of image (without extension) below image?</td><td><input type="radio" name="con[slideshowdetails]" value="true" <?php if($IG_CONFIG['slideshowdetails']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[slideshowdetails]" value="false" <?php if(!$IG_CONFIG['slideshowdetails']) echo "checked";?> />No</td><td><small>Yes</small></td></tr>
        </table>
        </div>
        <div id="tc" class="boxoff">
        <b>Image and Thumbnail Settings</b><br/><br/>
        <table>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Image Settings</b></td></tr>
        <tr><td>Modify image when uploaded using settings below?</td><td><input type="radio" name="con[modifyimage]" value="true" <?php if($IG_CONFIG['modifyimage']) echo "checked";?> onclick="javascript:e(Array('con[largecrop]','con[largewidth]','con[largeheight]','con[largewatermark]','con[largewatermarkalpha]','con[largewatermarklocation]','con[largequality]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[modifyimage]" value="false" <?php if(!$IG_CONFIG['modifyimage']) echo "checked";?> onclick="javascript:d(Array('con[largecrop]','con[largewidth]','con[largeheight]','con[largewatermark]','con[largewatermarkalpha]','con[largewatermarklocation]','con[largequality]'));"/>No</td><td><small>Yes</small></td></tr>
        <tr><td>Modify image size?</td><td><input type="radio" name="con[largechangesize]" value="true" <?php if($IG_CONFIG['largechangesize']) echo "checked";?> onclick="javascript:e(Array('con[largecrop]','con[largewidth]','con[largeheight]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[largechangesize]" value="false" <?php if(!$IG_CONFIG['largechangesize']) echo "checked";?> onclick="javascript:d(Array('con[largecrop]','con[largewidth]','con[largeheight]'));"/>No</td><td><small>Yes</small></td></tr>
        <tr><td>Maximum width of images</td><td><input type="text" name="con[largewidth]" value="<?php echo $IG_CONFIG['largewidth'];?>" size="3" />pixels</td><td><small>640</small></td></tr>
        <tr><td>Maximum height of images</td><td><input type="text" name="con[largeheight]" value="<?php echo $IG_CONFIG['largeheight'];?>" size="3" />pixels</td><td><small>640</small></td></tr>
        <tr><td>Resize images and crop longer edge to fit width and height above?</td><td><input type="radio" name="con[largecrop]" value="true" <?php if($IG_CONFIG['largecrop']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[largecrop]" value="false" <?php if(!$IG_CONFIG['largecrop']) echo "checked";?> />No</td><td><small>No</small></td></tr>
        <tr><td>Quality percentage of JPEG images (1-100)</td><td><input type="text" name="con[largequality]" value="<?php echo $IG_CONFIG['largequality'];?>" size="3" />%</td><td><small>90</small></td></tr>
        <tr><td>Watermark all images when they are uploaded?</td><td><input type="radio" name="con[largewatermark]" value="true" <?php if($IG_CONFIG['largewatermark']) echo "checked";?> onclick="javascript:e(Array('con[largewatermarklocation]','con[largewatermarkalpha]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[largewatermark]" value="false" <?php if(!$IG_CONFIG['largewatermark']) echo "checked";?> onclick="javascript:d(Array('con[largewatermarklocation]','con[largewatermarkalpha]'));" />No</td><td><small>No</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Location of watermark PNG file</td><td><input type="text" name="con[largewatermarklocation]" value="<?php echo $IG_CONFIG['largewatermarklocation'];?>" size="20" /></td><td><small>watermark.png</small></td></tr>
        <tr><td>&nbsp;&nbsp;&nbsp;&nbsp;Transparency percentage of watermark</td><td><input type="text" name="con[largewatermarkalpha]" value="<?php echo $IG_CONFIG['largewatermarkalpha'];?>" size="3" />%</td><td><small>50</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Thumbnail Settings</b></td></tr>
        <tr><td>Width of thumbnails</td><td><input type="text" name="con[width]" value="<?php echo $IG_CONFIG['width'];?>" size="3" />pixels</td><td><small>165</small></td></tr>
        <tr><td>Height of thumbnails</td><td><input type="text" name="con[height]" value="<?php echo $IG_CONFIG['height'];?>" size="3" />pixels</td><td><small>110</small></td></tr>
        <tr><td>Resize images and crop longer edge to fit width and height above?</td><td><input type="radio" name="con[crop]" value="true" <?php if($IG_CONFIG['crop']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[crop]" value="false" <?php if(!$IG_CONFIG['crop']) echo "checked";?> />No</td><td><small>Yes</small></td></tr>
        <tr><td>If crop is off, which dimension should be fixed?</td><td><input type="radio" name="con[heightalso]" value="true" <?php if($IG_CONFIG['heightalso']) echo "checked";?> />Fixed height, variable width<br/>
                                                          <input type="radio" name="con[heightalso]" value="false" <?php if(!$IG_CONFIG['heightalso']) echo "checked";?> />Fixed width, variable height</td><td><small>Fixed height, variable width</small></td></tr>
        <tr><td>Quality percentage of JPEG thumbnails (1-100)</td><td><input type="text" name="con[quality]" value="<?php echo $IG_CONFIG['quality'];?>" size="3" />pixels</td><td><small>90</small></td></tr>
        </table>
        </div>
        <div id="td" class="boxoff">
        <b>Descriptions + Comments Settings</b><br/><br/>
        <table>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Data Files</b></td></tr>
        <tr><td>Use data files to store descriptions and comments?</td><td><input type="radio" name="con[usedatafiles]" value="true" <?php if($IG_CONFIG['usedatafiles']) echo "checked";?>  onclick="javascript:e(Array('con[albumdescriptions]','con[imagedescriptions]','con[allowcomments]','con[publiccomments]'));"/>Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[usedatafiles]" value="false" <?php if(!$IG_CONFIG['usedatafiles']) echo "checked";?> onclick="javascript:d(Array('con[albumdescriptions]','con[imagedescriptions]','con[allowcomments]','con[publiccomments]'));"/>No</td><td><small>yes</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Descriptions</b></td></tr>
        <tr><td>Show album descriptions (if any)?</td><td><input type="radio" name="con[albumdescriptions]" value="true" <?php if($IG_CONFIG['albumdescriptions']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[albumdescriptions]" value="false" <?php if(!$IG_CONFIG['albumdescriptions']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Show image descriptions (if any)?</td><td><input type="radio" name="con[imagedescriptions]" value="true" <?php if($IG_CONFIG['imagedescriptions']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[imagedescriptions]" value="false" <?php if(!$IG_CONFIG['imagedescriptions']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td colspan="3" bgcolor="#e8e8f4"><b>Comments</b></td></tr>
        <tr><td>Allow commenting on images?</td><td><input type="radio" name="con[allowcomments]" value="true" <?php if($IG_CONFIG['allowcomments']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[allowcomments]" value="false" <?php if(!$IG_CONFIG['allowcomments']) echo "checked";?> />No</td><td><small>yes</small></td></tr>
        <tr><td>Allow all visitors to see these comments?</td><td><input type="radio" name="con[publiccomments]" value="true" <?php if($IG_CONFIG['publiccomments']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[publiccomments]" value="false" <?php if(!$IG_CONFIG['publiccomments']) echo "checked";?> />No (just via admin page)</td><td><small>yes</small></td></tr>
        <tr><td>Automatically collapse comments to start with?</td><td><input type="radio" name="con[autohidecomments]" value="true" <?php if($IG_CONFIG['autohidecomments']) echo "checked";?> />Yes &nbsp;&nbsp;&nbsp;
                                                          <input type="radio" name="con[autohidecomments]" value="false" <?php if(!$IG_CONFIG['autohidecomments']) echo "checked";?> />No (just via admin page)</td><td><small>yes</small></td></tr>
        </table>
        </div>
        <div id="te" class="boxoff">
        <b>Template Settings</b><br/><br/>
        <table>
        <tr><td>Homepage text file</td><td><input type="text" name="con[mainfile]" value="<?php echo $IG_CONFIG['mainfile'];?>" size="20" /></td><td><small>mainpage.html</small></td></tr>
        <tr><td>Template file</td><td><input type="text" name="con[templatefile]" value="<?php echo $IG_CONFIG['templatefile'];?>" size="20" /></td><td><small>template.php</small></td></tr>
        <tr><td>CSS file</td><td><input type="text" name="con[cssfile]" value="<?php echo $IG_CONFIG['cssfile'];?>" size="20" /></td><td><small>idutgallery.css</small></td></tr>
        </table>
        </div>
        <div id="tf" class="boxoff">
        <b>Plug-in Settings</b><br/><br/>
        <table>
        <tr><td>Human Checker file</td><td><input type="text" name="con[humanchecker]" value="<?php echo $IG_CONFIG['humanchecker'];?>" size="20" /></td><td><small>iduthc.php - not got the Idut Human Checker? <a href="http://www.idut.co.uk/humanchecker/">Download it here</a>!</small></td></tr>
        <tr><td>Reflection effect file</td><td><input type="text" name="con[reflection]" value="<?php echo $IG_CONFIG['reflection'];?>" size="20" /></td><td><small>reflection.js - add reflections to your images</small></td></tr>
        <tr><td>Bulk uploading (Java)</td><td><input type="text" value="automatic" size="20" disabled="true"/></td><td><small>If thinupload.php and thinupload.jar are in your gallery folder, bulk upload is available from the regular upload page</small></td></tr>
        <tr><td colspan="3" align="center"><br/><br/>Visit <a href="http://www.idut.co.uk/idutgallery/">idut.co.uk/idutgallery</a> to download more plugins!</td></tr>
        </table>
        </div>
        </form>
        <?php
}//showSettings

function saveSettings($con){
        global $configfile;
        $content = '<?php
/* Idut Gallery 2.1 (beta)
 * (c) 2005-2008 Idut - www.idut.co.uk
 * config.php
 */

// THESE SETTINGS HAVE BEEN SAVED USING THE ADMINISTRATION PAGE
// Not all available settings will be shown below
// Descriptions of settings will also not be shown

';

        foreach ($con as $key => $value) {
        $content = $content . "\n";
        if($value === "false" or $value === "true"){
                $content = $content . '$IG_CONFIG[\''.$key.'\'] = '.$value.';';
        }else{
                $content = $content . '$IG_CONFIG[\''.$key.'\'] = "'.$value.'";';
        }
        }
        
                $content = $content.'
?>';
        $handle = fopen($configfile, 'w');
        
        if(fwrite($handle, $content) === FALSE){
                echo "<b>Cannot write to config file.</b><br/><br/>Make sure that the file is CHMOD 777.";
        }else{
                echo "<b>Config file has been saved.</b>";
        }
        fclose($handle);
        
        showSettings();        
}

function showAbout(){
?>
<b>About Idut Gallery</b><br/>
Idut Gallery is a powerful, yet simple, image gallery for your website.
<br/><br/>
<b>Version Checker</b><br/>
You are currently running version <b>2.1 beta</b>, the latest version is <img src="http://www.idut.co.uk/idutgallery/latest.php?t=img&v=2.1"/>.<br/>
You can upgrade by going to <a href="http://www.idut.co.uk/idutgallery/" target="_blank">idut.co.uk/idutgallery</a>.
<br/><br/>
<b>Support</b><br/>
You can get free user-to-user support, suggest features and report bugs at the Idut support forums. Visit <a href="http://www.idut.co.uk/support/" target="_blank">idut.co.uk/support</a>.
<br/><br/>
<b>Latest News</b><br/>
<iframe src="http://www.idut.co.uk/idutgallery/latest.php?t=iframe&v=2.1" width="700" height="200" frameborder="0" scrolling="auto"></iframe><br/>
<?php
}//showAbout

function adminHeader(){
        global $IG_CONFIG, $PHP_SELF;
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title><?php echo $IG_CONFIG['title']; ?> Admin</title>
        <head>
        <style type="text/css">
        .logo {
        font-family: "Arial Narrow", Arial, Helvetica, sans-serif;
        font-size: 36px;
        color: #e8e8f4;
        letter-spacing: 5pt;
        text-align: left;
}
body {
        background-color: #eeeeee;
}
.albumlist {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #336699;
        word-spacing: 10px;
        text-decoration: none;
}
.canvas {
        font-family: Arial, Helvetica, sans-serif;
        color: #333333;
        background-color: #ffffff;
        border: 1px solid #cccccc;
        font-size: 12px;
        padding:10px;
        width:780px;
}
.tabs {
        margin-left:2px;
        margin-bottom:0px;
        border-bottom: 1px solid #cccccc;
}
.boxon {
        border-bottom: 1px solid #cccccc;
        border-left: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
        height:auto;
        visibility:visible;
        background:#ffffff;
        padding:10px;
        margin-left:2px;
        margin-top:0px;
}
.boxoff {
        height:0px;
        visibility:hidden;
        margin-left:2px;
        margin-top:0px;
}
.tabon {
        background-color: #ffffff;
        border-bottom: 1px solid #ffffff;
        border-top: 1px solid #cccccc;
        border-left: 1px solid #cccccc;
        border-right: 1px solid #cccccc;
        margin-bottom:0px;
        padding-left:5px;
        padding-right:5px;
}
.taboff {
        background-color: #cccccc;
        border: 1px solid #cccccc;
        padding-left:5px;
        padding-right:5px;
}
.taboff:hover {
        background-color: #A4B4FF;
        padding-left:5px;
        padding-right:5px;
        cursor: hand;
}
a {
        text-decoration: none;
        color: #3333ff;
}
a:hover {
        text-decoration: none;
        color: #A4B4FF;
}

.bottom {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8px;
        color: #A4B4FF;
}
.thumb {
        border: 1px solid #000000;
}

.fullimg {
        border: 1px solid #000000;
}        
        input,textarea, select {
                font: normal 11px Verdana, Arial, Helvetica, sans-serif;
        }

        .odd{
                background-color: #dce8f4;
        }
                </style>
        </head>
        <body>
        
        <?php
        echo "<div class=\"logo\"><a href=\"http://www.idut.co.uk/\" target=\"_blank\"><img src=\"http://www.idut.co.uk/idutgallery/lblogo2-1.jpg\" border=\"0\" align=\"right\"/></a><a href=\"$PHP_SELF\">".$IG_CONFIG['title']." Admin</font></a></div>\n"
                ."<div class=\"bottom\">Powered by <a href=\"http://www.idut.co.uk/\" target=\"_blank\">Idut Gallery</a> 2.1</div><br/>";
        ?>
        <script>
        function delImage(dir,file){
                var conf = confirm("This will delete this image and associated thumbnail, description and comments.");
                if (conf == true){
                   window.location="?c=fdelete&d="+dir+"&f="+file;
                 }
        }
        function delAlbum(dir){
                var conf = confirm("This will delete this album and associated images, thumbnails, descriptions and comments.");
                if (conf == true){
                   window.location="?c=delete&d="+dir;
                 }
        }
        function setCover(dir,file){
                var conf = confirm("This will set the selected photo as the cover for this album.");
                if (conf == true){
                   window.location="?c=setcover&d="+dir+"&f="+file;
                 }
        }
        function toggleBlock(elementId) {
                var element = document.getElementById(elementId);
                if(element.style.visibility == 'hidden'){
                        element.style.visibility = 'visible';
                        element.style.height = 'auto';
                }else{
                        element.style.height = '0px';
                        element.style.visibility = 'hidden';
                }
        }
        </script>
        <?php
        echo '<table class="canvas" align="center"><tr><td valign="top">';
        echo '<table cellspacing="5" cellpadding="5"align="center"><tr><td bgcolor="#CCCCCC"><a href="?c=manage">MANAGE ALBUMS</a></td><td bgcolor="#CCCCCC"><a href="?c=settings">SETTINGS</a></td><td bgcolor="#CCCCCC"><a href="?c=about">ABOUT</a></td></tr></table>
        </td></tr><tr><td>';
}//adminHeader
?>
