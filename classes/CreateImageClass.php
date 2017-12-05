<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class CreateImageClass
{

    public $fontFamily = 'cuprum.ttf';

    public $fontSize = 23;

    public $widthString = 430;

    public $pathImage = 'images/';
    
    public $pathTmpImage = 'images/tmp/';


    /**
     * @return array
     */
    public function categoryNews()
    {
        return [
            '1' => [
                'category' => 'Новость',
                'image' => 'news.jpg',
                'padding' => '50,110',
            ],
            '2' => [
                'category' => 'Интервью',
                'image' => 'interview.jpg',
                'padding' => '170,110',
            ]
        ];
    }


    /**
     * click submit in form
     */
    public function submit()
    {
        if ($_POST['name'] == 'preview') { $this->preview(); }
        elseif ($_POST['name'] == 'create') { $this->save(); }
    }


    /**
     * preview image
     */
    public function preview() 
    {
        $categoryNews = $_POST['category'];
        $title = $_POST['title'];

        $categoryNewsList = $this->categoryNews();

        foreach ($categoryNewsList as $key => $value) {
                if ($key == $categoryNews) {
                    $res = $value['image'];
                    $padding = $value['padding'];
                }
        };

        $this->createImage($res, $title, $file = $_FILES, $padding);
    }
    
    
    public function save()
    {
        
    }

    /**
     * create preview image
     * @param $nameImage
     * @param $title
     * @param array $file
     * @param $padding
     */
    public function createImage($nameImage, $title, $file = [], $padding)
    {
        putenv('GDFONTPATH=' . realpath('.'));
        
        if (empty($file)) {
            $im = imagecreatefromjpeg($this->pathImage . $nameImage);
        }
        elseif(!empty($file) & $nameImage == 'interview.jpg') {            
            $im = imagecreatefromjpeg("images/interview.jpg");
            imagealphablending($im, true);
            imagesavealpha($im, true);
            
            $is = imagecreatefrompng($file['file']['tmp_name']);
            imagealphablending($is, false);
            imagesavealpha($is, true);

            $black = imagecolorallocate($is, 0, 0, 0);
            imagecolortransparent($is, $black);

            imagecopymerge($im, $is, 43, 83, 0, 0, imagesx($im), imagesy($im), 100);        //return $im            
        }
        elseif (!empty($file)) {
            $is = imagecreatefromjpeg("images/news.jpg");
            imagealphablending($is, true);
            imagesavealpha($is, true);

            $im = imagecreatefromjpeg($file['file']['tmp_name']);
            imagealphablending($im, true);

            imagecopymerge($im, $is, 0, 0, 0, 0, imagesx($is), imagesy($is), 70);       //return $im            
        }

        $font = $this->fontSize;

        $fontFamily = $this->fontFamily;

        $textColor = imagecolorallocate ($im, 0, 0,0);

        $titleRes = $this->widthTitle($title);

        $newImage = time() . rand(555, 25478) . '.jpg';

        $xy = explode(',', $padding);

        imagettftext($im, $font, 0, $xy[0], $xy[1], $textColor, $fontFamily, $titleRes);

        imagejpeg($im, $this->pathTmpImage . $newImage);

        echo $newImage;
    }


    /**
     * divide the title by width
     * @param $title
     * @return string
     */
    public function widthTitle($title)
    {
        $fontFamily = $this->fontFamily;

        $fontSize = $this->fontSize;

        $widthString = $this->widthString;

        $arr = explode(' ', $title);

        $res = '';

        foreach ($arr as $word) {

            $tmpString = $res . ' ' . $word;

            $textBox = imagettfbbox($fontSize, 0, $fontFamily, $tmpString);

            if($textBox[2]>$widthString) {
                $res.=($res==""?"":"\n").$word;
            }
            else {
                $res.=($res==""?"":" ").$word;
            }
        }

        return $res;
    }


}