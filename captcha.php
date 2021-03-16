<?php

session_start();
// to generate a random number
$strlength = rand(4, 4);
$captchastr = "";
for ($i = 1; $i <= $strlength; $i++) {
    $textornumber = rand(1, 3);
    if ($textornumber == 1) {
        $captchastr .= chr(rand(49, 57));
    }
    if ($textornumber == 2) {
        $captchastr .= chr(rand(65, 78));
    }
    if ($textornumber == 3) {
        $captchastr .= chr(rand(80, 90));
    }
}

//random RGB colors

$randcolR = rand(100, 230);
$randcolG = rand(100, 230);
$randcolB = rand(100, 230);

//initialize image $captcha is handle dimensions 200,50
$captcha = imagecreate(130, 40);
$backcolor = imagecolorallocate($captcha, $randcolR, $randcolG, $randcolB);

$txtcolor = imagecolorallocate($captcha, ($randcolR - 20), ($randcolG - 20), ($randcolB - 20));
for ($i = 1; $i <= $strlength; $i++) {

    $clockorcounter = rand(1, 2);
    if ($clockorcounter == 1) {
        $rotangle = rand(0, 45);
    }
    if ($clockorcounter == 2) {
        $rotangle = rand(315, 360);
    }

//$i*25 spaces the characters 25 pixels apart
    @imagettftext($captcha, rand(14, 20), $rotangle, ($i * 25), 30, $txtcolor, "./fonts/arialbd.ttf", substr($captchastr, ($i - 1), 1));
}
//Send the headers (at last possible time)
header('Content-type: image/png');

//Output the image as a PNG
imagepng($captcha);

//Delete the image from memory
imagedestroy($captcha);

//save the string in a session variable
$_SESSION['captchastr'] = substr($captchastr, 0, 4);
$_SESSION['captchastr_low'] = strtolower(substr($captchastr, 0, 4));
?>
