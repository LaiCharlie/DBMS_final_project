<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>login.php</title>
    </head>
    <body>
        <?php
            session_start();  // 啟用交談期
            $name = "";  $password = "";
            // 取得表單欄位值
            if ($_SESSION["modified"] == true) {
                echo "<center><font color='red'>";
                echo "password reset successfully, please login again<br>";
                echo "</font>";
                unset($_SESSION["modified"]);
            } 
            if ($_SESSION["registered"] == true) {
                echo "<center><font color='red'>";
                echo "registered successfully, please login again<br>";
                echo "</font>";
                unset($_SESSION["registered"]);
            }
            if ( isset($_POST["name"]) )
                $name = $_POST["name"];
            if ( isset($_POST["password"]) )
                $password = $_POST["password"];
            // 檢查是否輸入使用者名稱和密碼
            if ($name != "" && $password != "") {
                // 建立MySQL的資料庫連接 
                $link = mysqli_connect("127.0.0.1", "root", "")
                    or die("無法開啟MySQL資料庫連接!<br/>");
                $db = mysqli_select_db($link, "finalproject");
                // 送出UTF8編碼的MySQL指令
                mysqli_query($link, 'SET NAMES utf8');
                // 建立SQL指令字串
                $sql = "SELECT * FROM Account WHERE Password='";
                $sql.= $password."' AND Account='".$name."'";
                // 執行SQL查詢
                $result = mysqli_query($link, $sql);
                $total_records = mysqli_num_rows($result);
                // 是否有查詢到使用者記錄
                if ( $total_records > 0 ) {
                    // 成功登入, 指定Session變數
                    $_SESSION["login_session"] = true;
                    $_SESSION["name"] = $_POST["name"];
                    $_SESSION["password"] = $_POST["password"];
                    header("Location: index.php");
                } else {  // 登入失敗
                    echo "<center><font color='red'>";
                    echo "Account或password錯誤!<br/>";
                    echo "</font>";
                    $_SESSION["login_session"] = false;
                }
                mysqli_close($link);  // 關閉資料庫連接  
            }
        ?>
        <form action="login.php" method="post" >
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
                <input type="submit" value="登入"/>
                <a href="register.php">註冊</a>
            </div>
        </form>
    </body>
</html>