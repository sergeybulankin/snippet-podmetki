<?php
require_once('classes/CreateImageClass.php');

$snippet = new CreateImageClass();
$categoryNews = $snippet->categoryNews(); ?>
<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <title>TEST</title>
</head>
<body>
<div class="col-lg-12">
    <h1>Форма добавления сниппета</h1>

    <div class="col-lg-6">
        <form action="" method="post" enctype="multipart/form-data">
            <label for="category">Материал</label>
            <select name="category" id="category" class="form-control">
                <?php foreach ($categoryNews as $key => $value): ?>
                    <option value="<?=$key?>" id="<?=$key?>"><?=$value['category']?></option>
                <?php endforeach;?>
            </select>

            <br>

            <label for="file">Файл</label>
            <input type="file" name="file" id="file" class="form-control">

            <br>

            <label for="title">Заголовок <span class="alert-info" style="padding: 3px; font-size: 12px">максимум - 140 символов</span></label>
            <textarea class="form-control" name="title" id="title" rows="5" maxlength="124"></textarea> <!-- 124 !-->

            <br>
            
            <label for="preview">Превью</label>
            <div id="preview">
                
            </div>

            <br>

            <button type="button" name="preview" class="btn btn-info">Preview</button>
            <button type="button" name="create"  class="btn btn-success">Create</button>
        </form>
    </div>
    <script>
        $('button').click(function () {
            var name = $(this).attr('name');
            var title = $('textarea#title').val();
            var category = $(':selected').attr('id');
            var file = $('input#file').val();

            $.ajax({
                type: "POST",
                url: 'example.php',
                data: {name : name, title : title, category : category, file : file},
                success: function (data) {
                    $('div#preview').html('<img src="images/'+ data +'">');
                }
            });
        });
    </script>
</div>
</body>
</html>