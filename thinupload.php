<?php
/* Idut Gallery 2.1 (beta)
 * (c) 2005-2008 Idut - www.idut.co.uk
 * thinupload.php
 * This plugin uses Thin Image Upload. The full version is available from http://upload.thinfile.com
 */
include("config.php");
if($_GET['c'] == "step1"){
        step1();
}elseif($_GET['c'] == "properties"){
        properties();
}elseif($_GET['c'] == "upload"){
        $file = $_FILES['userfile'];
        $k = count($file['name']);
        for($iglobal=0 ; $iglobal < $k ; $iglobal++){
                doUpload("temp",$file);

        }
        echo "<br><br>Remember to follow step 2 to complete the upload!";
}elseif($_POST['c'] == "doupload"){
        doUpload();
}elseif($_GET['c'] == "showupload"){
        showUpload();
}elseif($_GET['c'] == "showfiles"){
        showFiles();
}elseif($_POST['c'] == "getfiles"){
        getFiles($_POST['files'],$_POST['dir']);
}else{
        showFiles();
        //echo '<a href="?c=showupload">Upload Files</a><br/><br/><br/>';
        //echo '<a href="?c=showfiles">Get Existing Files</a>';
}

function step1(){
?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Idut Gallery Admin</title>
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

        <div class="logo"><a href="http://www.idut.co.uk/" target="_blank"><img src="http://www.idut.co.uk/idutgallery/lblogo2-1.jpg" border="0" align="right"/></a><a href="admin.php">Idut Gallery Admin</font></a></div>
<div class="bottom">Powered by <a href="http://www.idut.co.uk/" target="_blank">Idut Gallery</a> 2.1</div><br/>

<br/>                Drag and drop your images onto the elephants below. This will resize them and upload them to a temp folder, you will then need to open part 2 of this script to put them into your desired album.<br/><br/>
                   <br/>
                   <applet
                           archive  = "thinupload.jar"
                           code     = "com.thinfile.upload.ThinImageDemo"
                           name     = "Thin Image Upload"
                           hspace   = "0"
                           vspace   = "0"
                           width = "300"
                           height = "312"
                           align    = "middle"
                           props_file = "http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>?c=properties"
                        MAYSCRIPT="yes"
                   >
                   </applet>
 </body>
</html>
<?php
}

function properties(){
include("config.php");
?>
# Configuration file for Thin Image Upload
#
# Online documentation is available at http://upload.thinfile.com/docs/
# Lines begining with the '#' symbol denote comments. They will not
# be processed.
#

#
# The url is the upload destination. It should point to the script on
# your server that will accept the uploaded files.
#
# Example:
#     url=http://upload.thinfile.com/demo/upload.php
#     url=http://upload.thinfile.com/cgi-bin/upload.cgi
#

url=http://<?php echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; ?>?c=upload

#
# To change the welcome message displayed when the applet starts up,
# change the message property. It should be a valid url and should point
# to a web page.
#
# Example:
#     message=http://upload.thinfile.com/demo/init.php
#

#
# If you want to impose a limit on the total size of the file upload
# enter a value in kilobytes for the max_upload parameter. A value
# of 0, the default means unlimited. Please make sure that the server
# side configuration does not impose a lower limit than what you choose
# for max_upload
#
# Example:
#     max_upload=10240
#     will impose a limit of 10 Mega Bytes
#

max_upload=0

#
# The max_upload property checks the sum of file sizes. You can impose
# a limit on the size of individual files using the max_file property.
# The value is in kilobytes. 0 means no limit.
#
# Example:
#     max_file=2048
#     for a limit of 2 Mega Bytes
#

max_file=0

#
# max_upload_message message will be displayed when either the max_upload or
# max_file setting has been exceeded. If you enter a text message it
# will be displayed as a popup. If you enter a URL, the chosen page be
# loaded with in the applet. (Note: size_exceeded is an alias for max_upload_message)
#
# Example:
#     max_upload_message=http://upload.thinfile.com/demo/exceed.html
#

#
# The next section is used to configure client side filtering.
#
#
# Enter a comma separated list of file extensions in the allow_types
# field. The applet will refuse to go ahead with the upload if any of
# the selected files do not match the list of extensions.
#
# Please use only lower case extensions. The applet will test for both
# cases as well as mixed case.
#
# The default behaviour of the applet when it encounters an unwanted file
# can be changed by editing the filter_action property.
#
# Example:
#     allow_types=jpg,gif,png,tif,xcf,psd
#
#
# The filter_action property tells the applet what action to take if it
# encounters a file type that is not listed in the allow_types
# parameter. If you enter a value of 'reject' here the applet will
# refuse to go ahead with the file upload. Enter any other value and
# the applet will silently ignore the offending files.
#
# This setting takes effect only if the allow_types property is set
#
# Example:
#     filter_action=reject
#

#
# The reject_message will be shown when the user attempts to upload
# files that should not be allowed and the filter_action is set to
# reject.
#
# If you enter a text message here it will be displayed as a popup.
# A URl, will result in the a page being loaded inside the applet.
#
# Example:
#     reject_message=http://upload.thinfile.com/demo/reject.html
#

#
# As the name suggests the full_path setting determines if absolute pathnames
# should be sent to the server. If you switch this off, folder information will
# be stripped from the filenames.
#

full_path=yes
#
# When the translate_path setting is switched on, windows style pathnames will
# be converted to unix style paths. In other words '\' becomes '/'.  This
# setting is required for Resumable file upload.
#

translate_path=yes
#
# When encode_path setting is switched on, pathnames are URLEncoded. This is
# usefull if you are dealing with filenames that contain special characters.
# this setting is required for resumable file upload.
#

encode_path=yes

#
# The progress indicator can display a thumbnail of each image as it is being
# uploaded. To enable this feature uncomment the show_thumb property below.
#
# Example:
     show_thumb=1
#

#
# If you need to disable the multiple upload feature, and to upload files
# one at a time, switch to bachelor mode. When bachelor property is set
# the applet will complain if you try to upload more than one file. Use
# the angry_bachelor property to set the error message to be displayed.
#
# Example:
#     bachelor=1
#     angry_bachelor=http://upload.thinfile.com/demo/single.html
#

#
# If you switch on the browse setting the applet listens for mouse clicks
# and brings up a file selection dialog. If instead of clicking on the drop
# target you wish to display a browse button set the browse_button
# property as well.
#
# Example:
#     browse=1
#     browse_button=1
#

browse=1
browse_button=1

#
# The next bit is for image scaling. Images that are either wider than
# the img_max_width or taller than img_max_height will be scaled down.
# If scale_images=yes, you must set valid integer values for
# img_max_width and img_max_height.
#
# It should be noted that the java language does not support creating
# GIF files as such all scaled images will be in the JPG format. You
# will need to set the allow_types to match gif,jpg and png if you wish
# to make use of this feature.
#
# Example:
#     scale_images=yes
#     img_max_width=100
#     img_max_height=100
#

scale_images=yes
img_max_width=<?php echo $IG_CONFIG['largewidth'];?>

img_max_height=<?php echo $IG_CONFIG['largeheight'];?>

#
# If you wish to create several images of varying sizes you can make
# use of array notation.
#
# Example:
#     scale_images=yes
#     img_max_width[0]=100
#     img_max_height[0]=100
#     img_max_width[1]=200
#     img_max_height[1]=200
#

#
# By default the progress indicator will be hidden (closed) when the upload
# completes. By uncommenting the following line you can continue to keep the
# progress bar visible even after upload has been completed. The user will
# then have to manually close the progress bar.
#
# Example:
#     monitor_keep_visible=yes
#

#
# The applet or the entire browser can be redirected to another page
# when upload completes. Select the destination URL with the
# external_redir parameter.
#
# If you do not enter a value for the external_target property, the URL
# given external_target will be loaded with in the applet. Otherwise
# the page will be loaded in the target frame. To redirect the entire
# browser window use '_top' as the target.
#
# If you wish to delay the redirect, enter a value for the
# redirect_delay property (in milliseconds).
#
# Example:
#     external_redir=http://upload.thinfile.com
#     external_target=_top
     redirect_delay=100000
#
<?php
}

function showFiles(){
        echo "Existing Files:";
        echo '<form action="?" method="post">';
        if ($handle = opendir('temp/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." AND
                        $file != ".." AND
                        $file != ".htaccess" AND
                        $file != "index.php" AND
                        $file != "admin.php"
                        ) {
                    echo "<input type=\"checkbox\" name=\"files[$file]\" value=\"true\" checked/> $file<br/>\n";
                }
            }
            closedir($handle);
        }
        echo '<br/><br/>Move to folder: <select name="dir"/>';
        $result = opendir('images/');
        while ($fn = readdir($result)) {
                if ($fn != "." AND $fn != ".." AND is_dir("images/".$fn)) {
                        echo '<option>'.$fn.'</option>';
                }
        }
        echo '</select><br/><input type="hidden" name="c" value="getfiles"/>';
        echo '<input type="submit" value="Move photos to folder"/>';
        echo '</form>';


}//showFiles


function getFiles($files,$dir){
        echo "Moving files to: ".$dir;
        echo "<br/>Files:<br/><br/>";
        //print_r($files);

        foreach($files as $key => $value){
                if (!copy("temp/".$key, "images/".$dir."/".$key)) {
                            echo "failed to copy $key...\n";
                }else{
                        unlink("temp/".$key);
                }


        }
        doReindex($dir);
echo '<b>Your files have been moved, you can now close this window.';
}//getFiles


function doReindex($dir){
        global $IG_CONFIG;
        //REMOVE THUMBNAIL DIR AND REMAKE IT
        include("config.php");
        ?>

        <div id="IGmoredetails">

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
                @$opend_result = mkdir($opend , 0755);
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
}//doReindex


function doUpload($dir,$file){
        global $IG_CONFIG,$iglobal;
        echo "<b>Idut Gallery Bulk Upload Results: </b><br>";
        $name = split('/',urldecode($file['name'][$iglobal]));
        $file['name'][$iglobal] = $name[count($name)-1];
        if(is_dir($dir)){
                $uploadfile = $dir."/".basename($file['name'][$iglobal]);
                if (move_uploaded_file($file['tmp_name'][$iglobal], $uploadfile)) {
                        chmod($uploadfile, 0644);
                        echo $file['name'][$iglobal]." has been uploaded to $dir album.<br>";
                } else {
                        echo "<b>".$file['name'][$iglobal]." could not be uploaded to the $dir album.</b><br>";
                        switch ($file['error'][$iglobal]) {
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
                                                echo "Could not add watermark.<br>";
                                        }else{
                                                echo "Watermark added.<br>";
                                        }
                                }elseif($ex == "gif"){
                                        if(!imagegif($background, $file)){
                                                echo "Could not add watermark.<br>";
                                        }else{
                                                echo "Watermark added.<br>";
                                        }
                                }elseif($ex == "png"){
                                        if(!imagepng($background, $file)){
                                                echo "Could not add watermark.<br>";
                                        }else{
                                                echo "Watermark added.<br>";
                                        }
                                }else{
                                        if(!imagejpeg($background, $file, $IG_CONFIG['largequality'])){
                                                echo "Could not add watermark.<br>";
                                        }else{
                                                echo "Watermark added.<br>";
                                        }
                                }
                                imagedestroy($background);
                                imagedestroy($foreground);
                        }else{
                                echo "Watermark not added.<br>";
                        }
                }//modify image
        }else{
                echo "The $dir album does not exist.<br>";
        }
        //doReindex($dir);
}//doUpload
?>




