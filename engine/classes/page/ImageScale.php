<?php
/**
 * Scaled pictures with GD library
 *
 * @package     Engine
 * @subpackage  Page
 * @see         EngineCore
 * @author      AlexK
 * @version     1.0
 */
class ImageScale {

    /**
     * Constructor of class ImageScale
     */
    public function  __construct() {
        ;
    }

    /**
     * Scale picture
     *
     * @param string $sourceFile source file name
     * @param string $targetFile target file name
     * @param string $sourceExtention source file extention
     * @param string $targetSize target picture size
     * @param int    $scaleType scale type: 1 - scale, 2 - scalecrop
     * @param int    $useBlackWhiteColor if 1 - use black and white picture
     * @throws  if source file not exists or extention incorrect
     * @return  nothing
     */
    public static function scalePicture($sourceFile, $targetFile, $sourceExtention, $targetSize, $scaleType = 1, $useBlackWhiteColor = 0){

        $allowedExtentions = array('jpeg','jpg','gif','png');
        $scale = '';

        if (!is_file($sourceFile)){
            throw new ExceptionExt("Source file not exists");
        }

        if (!in_array($sourceExtention, $allowedExtentions) ){
            throw new ExceptionExt("Extention not allowed");
        }

        $scaleType = (integer)$scaleType;

        switch($scaleType){
            case 1:
                $scale = "scale";
                break;
            case 0:
                $scale = "scalecrop";
                break;
            default:
                $scale = "scale";
        }

        if ($useBlackWhiteColor){
            self::smallphoto_bw($scale, $targetSize, $targetFile, $sourceFile, $sourceExtention);
        } else {
            self::smallphoto   ($scale, $targetSize, $targetFile, $sourceFile, $sourceExtention);
        }
    }

    /**
     * Copy image picture and convert it to jpeg
     *
     * @param string $sourceFile source file name
     * @param string $targetFile target file name
     * @param string $sourceExtention source file extention
     * @throws  if source file not exists or extention incorrect
     * @return  nothing
     */
    public static function copyPicture($sourceFile, $targetFile, $sourceExtention) {

        switch($sourceExtention){
            case "jpeg":
            case "jpg":
                $im = imagecreatefromjpeg($sourceFile);
                break;
            case "gif":
                $im = imagecreatefromgif($sourceFile);
                break;
            case "png":
                $im = imagecreatefrompng($sourceFile);
                break;
            default:
                throw new ExceptionExt("Extention not allowed");
        }

        imagejpeg($im, $targetFile, 100);

    }

    /**
     * Decrease picture to diven size. Old function, taken from old engine
     *
     * @param string $Type "scale" or "scalecrop"
     * @param array $Sizes result picture size
     * @param string $url_to_save target picture file
     * @param string $Source source picture file
     * @throws  no throws
     * @return  nothing
     */
    private static function smallphoto($Type,$Sizes,$url_to_save,$Source,$extention)
    {

        if($Type=="scalecrop")
        {
            $Sizes=explode("x",$Sizes);
            $th_width=$Sizes["0"];
            $th_height=$Sizes["1"];
            $id2=$Source;
            switch($extention){
                case "jpeg":
                case "jpg":
                    $im = imagecreatefromjpeg($id2);
                    break;
                case "gif":
                    $im = imagecreatefromgif($id2);
                    break;
                case "png":
                    $im = imagecreatefrompng($id2);
                    break;
                default:
                    throw new ExceptionExt("Extention not allowed");
            }
            $width=imageSX($im);
            $height=imageSY($im);

            if (($width/$height) < ($th_width/$th_height))
            {
                $im1=imagecreatetruecolor($th_width, $th_width*($height/$width));
                imagecopyresampled($im1, $im, 0, 0, 0, 0, $th_width, $th_width*($height/$width), $width, $height);
                $im2=imagecreatetruecolor($th_width, $th_height);
                imagecopy($im2, $im1, 0,0,0,0,$th_width,$th_height);

            }
            else
            {
                $im1=imagecreatetruecolor(($width*$th_height)/$height, $th_height);
                imagecopyresampled($im1, $im, 0, 0, 0, 0, ($width*$th_height)/$height, $th_height, $width, $height);
                $im2=imagecreatetruecolor($th_width, $th_height);
                imagecopy($im2, $im1, 0,0,0,0,$th_width,$th_height);
            }

            imagejpeg($im2, $url_to_save, 100);
            imagedestroy($im);
            imagedestroy($im1);
            imagedestroy($im2);
        }

        if($Type=="scale")
        {
            $sourcesize=list($width, $height, $type, $attr)=getimagesize($Source);
            $it=$sourcesize["0"]/$Sizes;
            $ownheight=$sourcesize["1"]/$it;
            switch($extention){
                case "jpeg":
                case "jpg":
                    $im = imagecreatefromjpeg($Source);
                    break;
                case "gif":
                    $im = imagecreatefromgif($Source);
                    break;
                case "png":
                    $im = imagecreatefrompng($Source);
                    break;
                default:
                    throw new ExceptionExt("Extention not allowed");
            }
            $target=imagecreatetruecolor($Sizes,$ownheight);
            imagecopyresampled($target,$im,0,0,0,0,$Sizes,$ownheight,$width,$height);

            imagejpeg($target,$url_to_save,100);
            imagedestroy($im);
            imagedestroy($target);
        }
    }

    /**
     * Decrease picture to diven size and make it black-and-white. Old function, taken from old engine
     *
     * @param string $Type "scale" or "scalecrop"
     * @param array $Sizes result picture size
     * @param string $url_to_save target picture file
     * @param string $Source source picture file
     * @throws  no throws
     * @return  nothing
     */
    private static function smallphoto_bw($Type,$Sizes,$url_to_save,$Source,$extention)
    {
        if($Type=="scalecrop")
        {
            $Sizes=explode("x",$Sizes);
            $th_width=$Sizes["0"];
            $th_height=$Sizes["1"];
            $id2=$Source;
            switch($extention){
                case "jpeg":
                case "jpg":
                    $im = imagecreatefromjpeg($id2);
                    break;
                case "gif":
                    $im = imagecreatefromgif($id2);
                    break;
                case "png":
                    $im = imagecreatefrompng($id2);
                    break;
                default:
                    throw new ExceptionExt("Extention not allowed");
            }
            $width=imageSX($im);
            $height=imageSY($im);

            $source=$im;
            if (($width/$height) < ($th_width/$th_height))
            {
                $im1=imagecreatetruecolor($th_width, $th_width*($height/$width));
                imagecopyresampled($im1, $im, 0, 0, 0, 0, $th_width, $th_width*($height/$width), $width, $height);

                $im2=imagecreatetruecolor($th_width, $th_height);
                imagecopy($im2, $im1, 0,0,0,0,$th_width,$th_height);

            }
            else
            {
                $im1=imagecreatetruecolor(($width*$th_height)/$height, $th_height);
                imagecopyresampled($im1, $im, 0, 0, 0, 0, ($width*$th_height)/$height, $th_height, $width, $height);

                $im2=imagecreatetruecolor($th_width, $th_height);
                imagecopy($im2, $im1, 0,0,0,0,$th_width,$th_height);
            }


            $image = $im2;
            $x_dimension = imagesx($image);
            $y_dimension = imagesy($image);
            $new_image = imagecreatetruecolor($x_dimension, $y_dimension);

            if ($operation_callback == 'contrast') {
                $average_luminance = $this->getAverageLuminance($image);
            } else {
                $average_luminance = false;
            }

            for ($x = 0; $x < $x_dimension; $x++) {
                for ($y = 0; $y < $y_dimension; $y++) {

                    $rgb = imagecolorat($image, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;

                    $pixel_average=($r+$g+$b)/3;

                    $pixel = new Pixel($pixel_average, $pixel_average, $pixel_average);

                    $color = imagecolorallocate(
                        $image,
                        $pixel->r,
                        $pixel->g,
                        $pixel->b
                    );

                    imagesetpixel($new_image, $x, $y, $color);
                }

            }

            imagejpeg($new_image, $url_to_save, 100);

            imagedestroy($im);
            imagedestroy($im1);
            imagedestroy($im2);
        }

        if($Type=="scale")
        {
            $sourcesize=list($width, $height, $type, $attr)=getimagesize($Source);
            if($sourcesize["0"]>$sourcesize["1"])
            {
                $it=$sourcesize["0"]/$Sizes;
                $ownheight=$sourcesize["1"]/$it;
                switch($extention){
                    case "jpeg":
                    case "jpg":
                        $im = imagecreatefromjpeg($Source);
                        break;
                    case "gif":
                        $im = imagecreatefromgif($Source);
                        break;
                    case "png":
                        $im = imagecreatefrompng($Source);
                        break;
                    default:
                        throw new ExceptionExt("Extention not allowed");
                }
                $target=imagecreatetruecolor($Sizes,$ownheight);
                imagecopyresampled($target,$im,0,0,0,0,$Sizes,$ownheight,$width,$height);
            }
            else
            {
                $it=$sourcesize["1"]/$Sizes;
                $ownwidth=$sourcesize["0"]/$it;
                switch($extention){
                    case "jpeg":
                    case "jpg":
                        $im = imagecreatefromjpeg($Source);
                        break;
                    case "gif":
                        $im = imagecreatefromgif($Source);
                        break;
                    case "png":
                        $im = imagecreatefrompng($Source);
                        break;
                    default:
                        throw new ExceptionExt("Extention not allowed");
                }
                $target=imagecreatetruecolor($ownwidth,$Sizes);
                imagecopyresampled($target,$im,0,0,0,0,$ownwidth,$Sizes,$width,$height);
            }

            imagecolorset($target,12,32,32,23);
            imagejpeg($target,$url_to_save,100);
            imagedestroy($im);
            imagedestroy($target);
        }
    }
}
?>