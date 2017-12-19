<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class CreateImageClass
{
    /*====== DATABASE PARAMETERS ======*/

    private $dbhost = "localhost";

    private $dbname = "pdo";

    private $dbusername = "root";

    private $dbpassword = "123";

    /*====== DATABASE PARAMETERS ======*/

    public $fontFamily = 'cuprum.ttf';

    public $fontSize = 27;

    public $widthString = 430;

    public $pathImage = 'images/blanks/';
    
    public $pathTmpImage = 'images/tmp/';

    public $pathResult = 'images/results/';

    public $success = 0;


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


    /**
     * copy file to images and save to DB, delete preview file
     */
    public function save()
    {
        $dir = $this->pathTmpImage;

        $files = scandir($dir);

        foreach ($files as $file) {
            if ($file == $_POST['image']) {
                copy($this->pathTmpImage . $file, $this->pathResult . $file);
            }
        }

        //$this->dbInsert($_POST['image']);

        unlink($this->pathTmpImage . $_POST['image']);
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
            $this->success = 1;
        }
        elseif(!empty($file) & $nameImage == 'interview.jpg') {
            $im = imagecreatefromjpeg($this->pathImage. "interview.jpg");
            imagealphablending($im, true);
            imagesavealpha($im, true);

            $is = $this->testTypeImage($file['file']['tmp_name']);      //return creating image
            imagealphablending($is, false);
            imagesavealpha($is, true);

            $black = imagecolorallocate($is, 0, 0, 0);
            imagecolortransparent($is, $black);

            imagecopymerge($im, $is, 43, 83, 0, 0, imagesx($im), imagesy($im), 100);        //return $im
            $this->success = 1;

        }
        elseif (!empty($file)) {
            $is = imagecreatefromjpeg($this->pathImage . "news.jpg");
            imagealphablending($is, true);
            imagesavealpha($is, true);

            $im = imagecreatefromjpeg($file['file']['tmp_name']);
            imagealphablending($im, true);

            $this->testUploadImage($is, $im);

            imagecopymerge($im, $is, 0, 0, 0, 0, imagesx($is), imagesy($is), 70);       //return $im
        }

        $font = $this->fontSize;

        $fontFamily = $this->fontFamily;

        $textColor = imagecolorallocate ($im, 0, 0,0);

        $titleRes = $this->widthTitle($title);

        $newImage = [
            'file' => time() . rand(555, 25478) . '.jpg',
            'success' => $this->success
        ];

        $xy = explode(',', $padding);

        imagettftext($im, $font, 0, $xy[0], $xy[1], $textColor, $fontFamily, $titleRes);

        imagejpeg($im, $this->pathTmpImage . $newImage['file']);

        echo json_encode($newImage);        
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


    /**
     * testing sizes uploaded image for snippet image
     * @param $is
     * @param $im
     * @return int
     */
    public function testUploadImage($is, $im)
    {
        if (imagesx($is) != imagesx($im)) {
            return $this->success;
        }
        elseif (imagesy($is) != imagesy($im)) {
            return $this->success;
        }
        return $this->success = 1;
    }


    /**
     * set meme-type image for interview
     * @param $file
     * @return int
     */
    public function testTypeImage($file)
    {
        $size = getimagesize($file);
        $is = '';

        switch ($size['mime']) {
            case "image/gif":
                $is = imagecreatefromgif($file);
                break;
            case "image/jpeg":
                $is = imagecreatefromjpeg($file);
                break;
            case "image/png":
                $is = imagecreatefrompng($file);
                break;
        }
        return $is;
    }


    /**
     * insert to db
     * @param $image
     */
    public function dbInsert($image)
    {
        $pdo = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname", $this->dbusername, $this->dbpassword);

        $statement = $pdo->prepare("INSERT INTO table(image) VALUES(:image)");

        $statement->execute([
            'image' => $image
        ]);
    }
}