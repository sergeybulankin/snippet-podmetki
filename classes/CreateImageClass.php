<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class CreateImageClass
{

    public $fontFamily = 'cuprum.ttf';

    public $fontSize = '25';

    public $widthString = 430;

    public $pathImage = 'images/';


    /**
     * @return array
     */
    public function categoryNews()
    {
        return [
            '1' => [
                'category' => 'Новость',
                'image' => 'news.jpg',
            ],
            '2' => [
                'category' => 'Интервью',
                'image' => 'interview.jpg'
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
        $file = $_FILES;

        $categoryNewsList = $this->categoryNews();

        foreach ($categoryNewsList as $key => $value) {
                if ($key == $categoryNews) {
                    $res = $value['image'];
                }
        };

        $this->createImage($res, $title, $file);
    }
    
    
    public function save()
    {
        
    }

    /**
     * @param $nameImage
     * @param $title
     * @param $file
     */
    public function createImage($nameImage, $title, $file)
    {
        putenv('GDFONTPATH=' . realpath('.'));
        
        if (empty($file)) {
            $im = imagecreatefromjpeg($this->pathImage . $nameImage);
        }
        else {
            move_uploaded_file($file, 'images/' . $file);

            $im = imagecreatefromjpeg($file);
        }

        $font = $this->fontSize;

        $fontFamily = $this->fontFamily;

        $textColor = imagecolorallocate ($im, 0, 0,0);

        $titleRes = $this->widthTitle($title);

        $newImage = time() . rand(555, 25478) . '.jpg';

        imagettftext($im, $font, 0, 50, 110, $textColor, $fontFamily, $titleRes);

        imagejpeg($im, $this->pathImage . $newImage);

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