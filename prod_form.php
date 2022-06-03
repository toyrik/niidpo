<?php
require 'db.php';

//Получение цены через ajax jQuery
if (!empty($_GET['action']) && $_GET['action'] == 'get_prod_price') {
	$price_with_discount = "";
	$result = mysqli_query($conn, "SELECT * FROM test_product WHERE prod_id=".$_GET['prod_id']);
    if (mysqli_num_rows($result)) {
        $row = mysqli_fetch_assoc($result);
	    //Отправить через CURL строку товара $row в json
        //В $price_with_discount занести возвращенную цену товара со скидкой
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $url = $url[0];
        $data = [
            'action' => 'curl',
            'row' => json_encode($row)
        ];	

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);

        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }

        curl_close($ch);
        echo $res;
       
    }
    print $price_with_discount;
    exit;
}

//Получить строку товара в json, к цене товара применить скидку 1,5% и вернуть строку товара обратно в json
if (!empty($_POST['action']) && $_POST['action'] == 'curl') {
    header('Content-Type: application/json; charset=utf-8');
    print '{}';
	exit;
}
//Карточка товара
if (isset($_GET['prod_id'])) {
	$prod = array('prod_id'=>0, 'name'=>'', 'description'=>'');
	if (!empty($_GET['prod_id'])) {
		$result = mysql2_query($conn, "SELECT * FROM test_product WHERE prod_id=" . $_GET['prod_id']);
		if (mysql2_num_rows($result)) {
			$prod = mysql2_fetch_assoc($result);
            $prod['description'] = preg_replace("/{([^}]+)}/m", "", $prod['description']);
		}
	}
	
    //Сохранение
	if (isset($_POST['prod_id'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $description = $conn->real_escape_string($_POST['description']);
		if (!empty($_POST['prod_id'])) {
            $prodId = $conn->real_escape_string($_POST['prod_id']);

			mysql2_query($conn, "UPDATE test_product SET name='{$name}', description='{$description}' WHERE prod_id=" . $prodId);
		} else {
			mysql2_query($conn, "INSERT INTO test_product SET name='{$name}', description='{$description}'");
		}
		$prod=$_POST;
	}
?>
    <form method="post">
        <div><b>Название</b><br><input type="text" name="name" value="<?=$prod['name']?>"/></div>
        <div><b>Описание</b><br><textarea name="description"><?=$prod['description']?></textarea></div>
        <div id="prod_price_div"></div>
        <div>
            <input type="hidden" name="prod_id" value="<?=$prod['prod_id']?>"/>
            <input type="submit"/>
            <input type="button" id="get_prod_price_btn" value="Уточнить цену"/>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script>
        $('#get_prod_price_btn').click(function() {            
            $.get('/prod_form.php', {action: 'get_prod_price', prod_id: '<?=$prod['prod_id']?>'}, function(data){
                $('#prod_price_div').prepend(data);
            });
        });
    </script>
<?php
}
?>
