<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>register.php</title>
</head>
<body>
    <?php
        session_start();
        $name = "";
        $password = "";
        // 取得註冊的帳號和密碼
        if ( isset($_POST["name"]) )
            $name = $_POST["name"];
        if ( isset($_POST["password"]) )
            $password = $_POST["password"];
        // 檢查是否輸入帳號和密碼
        if ($name != "" && $password != "") {
            // 建立MySQL的資料庫連接
            $link = mysqli_connect("127.0.0.1","root", "")
                            or die("無法開啟MySQL資料庫連接!<br/>");
            $db = mysqli_select_db($link, "finalproject");
            // 送出UTF8編碼的MySQL指令
            mysqli_query($link, 'SET NAMES utf8');
            // 建立SQL指令字串
            $sql = "SELECT Account FROM Account WHERE Account='".$name."'";
            // 執行SQL查詢
            $result = mysqli_query($link, $sql);
            $total_records = mysqli_num_rows($result);
            // 是否有查詢到使用者記錄
            if ( $total_records == 0 ) {
                // 帳號未註冊過,註冊帳號
                $sql = "INSERT INTO Account VALUES(".$name.", ".$password.")";
                mysqli_query($link, $sql);
                echo "<center><font color='red'>";
                echo "註冊成功!<br/>";
                echo "</font>";
                $_SESSION["registered"] = true;
                unset($_SESSION["login_session"]);
                header("Location: index.php");
            } else {  // 註冊失敗
                echo "<center><font color='red'>";
                echo "使用者名稱已註冊過!<br/>";
                echo "</font>";
            }
            mysqli_close($link);
        }
    ?>
    <form action = 'register.php' method="post">
        <div align="center" style="background-color:#82FF82;padding:10px;margin-bottom:5px;">
        <br>
        <label for="name">帳號:</label>
        <input type="text" name="name" id="name" required autofocus/>
        <br>  
        <br> 
        <label for="password">密碼:</label>
        <input type="password" name="password" id="password" required/>
        <br>
        <br>
        <input type="submit" value="註冊"/>
        </div>
    </form>
</body>
</html>