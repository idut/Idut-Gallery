<?php
/* Idut Human Checker Plugin
 * (c) 2007-2008 Idut - www.idut.co.uk
 * iduthc.php
 * ------------------------------
 * To use this human checker, place:
 *     include("humanchecker.php");
 * at the begining of the page you wish to restrict access to.
 * Nothing below the inclusion of this page will be displayed.
 * E.g. just before you save to the database
 */
//CONFIG SETTINGS
$IHC_CONFIG['thispage'] = "iduthc.php";
$IHC_CONFIG['noisefile'] = "iduthcnoise.png";        //PNG file of noise
$IHC_CONFIG['fontfile'] = "iduthcfont.ttf";        //TTF font file
$IHC_CONFIG['textenter'] = "Please enter the following code to continue: ";


@session_start();
if(isset($_POST['IHCcode'])){
        if(md5($_POST['IHCcode']) != $_SESSION['IHCvalue']){
                die("The confirmation code was incorrect. Please go back and try again");
        }
}elseif(isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != "IHCimage" AND !isset($_POST['IHCcode'])){
        echo '<form method="post"';
        if(isset($_GET)){
                echo 'action="?';
                foreach($_GET as $key => $value){
                        if(is_array($value)){
                                foreach($value as $key2 => $value2){
                                        echo $key.'['.$key2.']='.$value2.'&';
                                }
                        }else{
                                echo $key.'='.$value.'&';
                        }
                }
                echo '"';
        }
        echo '>';
        echo $IHC_CONFIG['textenter'].'<br/>
        <img src="'.$IHC_CONFIG['thispage'].'?IHCimage"> <b>&gt;</b>
        <input name="IHCcode" type="text" id="code" size="5"><br/>';
        foreach($_POST as $key => $value){
                if(is_array($value)){
                        foreach($value as $key2 => $value2){
                                echo '<input type="hidden" name="'.$key.'['.$key2.']" value="'.$value2.'" />';
                        }
                }else{
                        echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
                }
        }
        echo '<input type="submit" value="Post Comment"/>
        </form>';
        exit;
}elseif(isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] == "IHCimage"){
        //Get random numbers
        $str = rand(0, 9);
        $str = $str.rand(0, 9);
        $str = $str.rand(0, 9);
        $str = $str.rand(0, 9);
        $str = $str.rand(0, 9);

        $_SESSION['IHCvalue'] = md5($str);
        $image = imagecreatefrompng($IHC_CONFIG['noisefile']);
        
        //Colours
        $colors[0]=array(122,229,112);
        $colors[1]=array(85,178,85);
        $colors[2]=array(226,108,97);
        $colors[3]=array(141,214,210);
        $colors[4]=array(214,141,205);
        $colors[5]=array(100,138,204);
        
        $color1=rand(0, 5);
        $color2=rand(0, 5);
        $color3=rand(0, 5);
        $color4=rand(0, 5);
        $color5=rand(0, 5);
        
        //Allocate colors for letters.
        $textColor1 = imagecolorallocate($image, $colors[$color1][0],$colors[$color1][1], $colors[$color1][2]);
        $textColor2 = imagecolorallocate($image, $colors[$color2][0],$colors[$color2][1], $colors[$color2][2]);
        $textColor3 = imagecolorallocate($image, $colors[$color3][0],$colors[$color3][1], $colors[$color3][2]);
        $textColor4 = imagecolorallocate($image, $colors[$color4][0],$colors[$color4][1], $colors[$color4][2]);
        $textColor5 = imagecolorallocate($image, $colors[$color5][0],$colors[$color5][1], $colors[$color5][2]);
        
        //Write text to the image using TrueType fonts.
        imagettftext($image, 20, rand(-20, 20), 10, 35, $textColor1, $IHC_CONFIG['fontfile'], $str{0});
        imagettftext($image, 20, rand(-20, 20), 35, 35, $textColor2, $IHC_CONFIG['fontfile'], $str{1});
        imagettftext($image, 20, rand(-20, 20), 60, 35, $textColor3, $IHC_CONFIG['fontfile'], $str{2});
        imagettftext($image, 20, rand(-20, 20), 85, 35, $textColor4, $IHC_CONFIG['fontfile'], $str{3});
        imagettftext($image, 20, rand(-20, 20), 110, 35, $textColor5, $IHC_CONFIG['fontfile'], $str{4});
        
        //header('Content-type: image/jpeg');
        imagejpeg($image);
        imagedestroy($image);
        exit;
}else{
        die('You cannot access this file directly.<br/>
        <hr/><a href="http://www.idut.co.uk/">www.idut.co.uk</a>');
}
?>
