<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>modify_password.php</title>
</head>
<body>
    <?php
        session_start();
        $password = "";
        // 取得當前的帳號和重設的密碼
        if (isset($_SESSION["name"]))
            $name = $_SESSION["name"];
        else 
            header("Lacation: index.php");
        if ( isset($_POST["password"]) )
            $password = $_POST["password"];
        else 
            header("Lacation: index.php");
        //檢查是否輸入密碼
        if ($password != "") {
            // 建立MySQL的資料庫連接
            $link = mysqli_connect("127.0.0.1","root", "")
                            or die("無法開啟MySQL資料庫連接!<br/>");
            $db = mysqli_select_db($link, "finalproject");
            // 送出UTF8編碼的MySQL指令
            mysqli_query($link, 'SET NAMES utf8');
            // 建立SQL指令字串
            $sql = "UPDATE Account SET Password='".$password."' WHERE Account='".$name."'";
            // 執行SQL查詢
            $result = mysqli_query($link, $sql);
            if ($result) {
                echo "<center><font color='red'>";
                echo "修改成功!<br/>";
                echo "</font>";
                $_SESSION["modified"] = true;
                unset($_SESSION["login_session"]);
                header("Location: index.php");
            } else {  // 修改失敗
                echo "<center><font color='red'>";
                echo "密碼修改失敗!<br/>";
                echo "</font>";
            }
           mysqli_close($link); 
        }
    ?>
    <form method="post">
        <div align="center" style="background-color:#82FF82;padding:10px;margin-bottom:5px;">
            <br> 
            <label for="password">密碼:</label>
            <input type="password" name="password" id="password" required/>
            <br>
            <br>
            <input type="submit" value="修改"/>
        </div>
    </form>
</body>
</html>