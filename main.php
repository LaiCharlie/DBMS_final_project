<!DOCTYPE html>
<html>

<head>
    <title> DBMS Final project </title>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.1.2/dist/echarts.min.js"></script>
    <style>
    .separation-line {
        border-top: 1px solid #000;
        margin: 50px 0;
        padding-top: 50px;
    }
    
    </style>
    <style>
    .table-wrapper {
        display: flex;
        justify-content: center;
    }
    </style>
    <style>
    .dropdown {
        position: relative;
        display: inline-block;
        border: 1px solid black;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
        border: 1px solid #000; /* Add border style */
        border-radius: 4px; /* Optional: Add border radius */
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }
    #list-items a {  
     display: block;  
     font-size: 18px;  
     background-color: #ddd;  
     color: black;  
     text-decoration: none;  
     padding: 10px;  
    } 
    </style>
    </head>

<body>
    <div class="dropdown" style="position: absolute; top: 10px; right: 100px;">
        <label for="account-dropdown">Account option</label>
        <div class="dropdown-content" id="dropdown-content">
            <a href = "logout.php">Log out</a><br>
            <a href = "modify_password.php">modify password</a>
        </div>
    </div>
    <?php
        session_start();
        $link = mysqli_connect("127.0.0.1", "root", "fisher2004");
        if (!$link) {
            echo "<p>Failed to connect to the database</p>";
            return;
        }
        // else
        //     echo "<p>Connection successful</p>";
        $db = mysqli_select_db($link, "final_project");
    ?>
    <?php 
        if (!isset($_POST["table"]))
            $_POST["table"] = "close_quotation";
        if (!isset($_POST["selected_columns"]))
            switch ($_POST["table"]) {
                case 'close_quotation':
                    $_POST["selected_columns"] = array("stock_no", "date", "close_price", "max_price", "min_price", "open_price",
                                               "tot_money", "tot_num", "tot_volume", "up_diff", "up_down");
                    break;
                case 'T_price':
                    $_POST["selected_columns"] = array("date", "close_point", "up_down", "up_per", "up_point");
                    break;
                case 'T_remuneration':
                    $_POST["selected_columns"] = array("date", "close_point", "up_down", "up_per", "up_point");
                    break;
                case 'market_statistics':
                    $_POST["selected_columns"] = array("date", "tot_money", "tot_num", "tot_volume");
                    break;
                default:
                    $_POST["selected_columns"] = array();
                    break;
            }
        if (!isset($_POST["column_text"]))
            $_POST["column_text"] = array();
        if (!isset($_POST["stock_no"]))
            $_POST["stock_no"] = "";

        $table = $_POST["table"];
        $selected_columns = $_POST["selected_columns"];
    ?>
    <?php
        $table = "close_quotation";
        if (isset($_POST["table"]))
            $table = $_POST["table"];
        $query = "SELECT COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = 'final_project'
            AND TABLE_NAME = '".$table."' ORDER BY ordinal_position;";
        $result = mysqli_query($link, $query);
        $column_num = mysqli_num_rows($result);
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_row($result)) {
                $columns[] = $row[0];
            }
        }
    ?>
    <form method="post">
        <input type="hidden" name="stock_no" value="<?php echo htmlspecialchars($_POST["stock_no"]); ?>">
        <label for="table">Table</label>
        <select name="table" id = "table">
            <option <?php if ($_POST["table"] == "close_quotation") {?>selected="true"<?php };?> value="close_quotation">close quotation</option>
            <option <?php if ($_POST["table"] == "T_price") {?>selected="true"<?php };?> value="T_price">T price</option>
            <option <?php if ($_POST["table"] == "T_remuneration") {?>selected="true"<?php };?> value="T_remuneration">T remuneration</option>
            <option <?php if ($_POST["table"] == "market_statistics") {?>selected="true"<?php };?> value="market_statistics">market statistics</option>
        </select>
        <input type="submit" value="change">
    </form>
    <form method="post">
        <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
        <?php
            echo '<style>';
            echo '.form-grid {';
            echo '    display: grid;';
            echo '    grid-template-columns: auto auto auto;';
            echo '    grid-gap: 10px;';
            echo '}';
            echo '.form-grid label {';
            echo '    display: inline-block;';
            echo '    width: 90px;'; // Adjust the width as needed
            echo '    text-align: left;';
            echo '}';
            echo '.form-grid input[type="text"] {';
            echo '    margin-left: 5px;'; // Adjust the margin as needed
            echo '}';
            echo '</style>';
            
            echo '<div class="form-grid">';
            foreach ($columns as $column) {
                $formattedColumn = str_pad($column, 10, ' ', STR_PAD_LEFT);
                echo '<div>';
                echo '<input type="checkbox" name="selected_columns[]" value="'.$column.'">';
                echo '<label>'.$formattedColumn.'</label>';
                echo '<input type="text" name="column_text['.$column.']" placeholder="Find for '.$column.'"style="width= 30px;">';
                echo '<input type="text" name="column_min['.$column.']" placeholder="Min">';
                echo '<input type="text" name="column_max['.$column.']" placeholder="Max">';
                echo '</div>';
            }
            echo '</div>';
        ?>
        <input type="submit" value="select">
        <div>
            <label for="limit">rows per page:</label>
            <input name="limit" id ="limit">
        </div>
    </form>
    <div class="separation-line"></div>
    <div class="table-wrapper">
        <?php
            $query = "SELECT";
            if (isset($_POST["selected_columns"])) {
                $selected_columns = $_POST["selected_columns"];
                $first = true;
                foreach ($selected_columns as $column) {
                    if ($first) {
                        $query .= " ".$column;
                        $first = false;
                    } else {
                        $query .= ", ".$column;
                    }
                }
            } else {
                $selected_columns = $columns;
                $query .= " *";
            }
            $query .= " FROM ".$table;
            $whereClause = '';
            foreach ($selected_columns as $column) {
                $column_text = $_POST["column_text"][$column];
                $min_text = $_POST["min_text"][$column];
                $max_text = $_POST["max_text"][$column];
                
                if ($column_text != '') {
                    if (empty($whereClause)) {
                        $whereClause = " WHERE ";
                    } else {
                        $whereClause .= " AND ";
                    }
                    $whereClause .= $column . " = '" . mysqli_real_escape_string($link, $column_text) . "'";
                } elseif ($min_text != '' && $max_text != '') {
                    if (empty($whereClause)) {
                        $whereClause = " WHERE ";
                    } else {
                        $whereClause .= " AND ";
                    }
                    $whereClause .= $column . " BETWEEN '" . mysqli_real_escape_string($link, $min_text) . "' AND '" . mysqli_real_escape_string($link, $max_text) . "'";
                }
            }
            
            $query .= $whereClause;
            $table = $_POST["table"];
            if ($table == "close_quotation") {
                $query .= " ORDER BY stock_no";
            }
            if (empty($_POST["limit"])) {
                $_POST["limit"] = 100;
            }
            $query .= " LIMIT ".$_POST["limit"];
            $result = mysqli_query($link, $query);            
            if (mysqli_num_rows($result) > 0) {
                echo "<div style='height: 300px; overflow-y: scroll;'>";
                echo "<table style='border-collapse: collapse;'>";
                // add arrow buttons row
                echo "<tr>";
                echo "</tr>";
                // print column headers
                echo "<tr>";
                foreach ($selected_columns as $column) {
                    echo "<th style='border: 1px solid black; padding: 8px;'>".$column."</th>";
                }
            
            // add favorite column header
            if ($table == "close_quotation") {
            echo "<th style='border: 1px solid black; padding: 8px;'>Favorite</th>";
            }
                echo "</tr>";
                // print data rows
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    foreach ($selected_columns as $column) {
                        echo "<td style='border: 1px solid black; padding: 8px;'>".$row[$column]."</td>";
                    }
                    // add favorite button
                    if ($table == "close_quotation") {
                        echo "<td style='border: 1px solid black; padding: 8px;'>";
                        echo "<form method='post'>";
                        echo "<input type='hidden' name='stock_no' value='".$row['stock_no']."'>";
                        echo "<input type='hidden' name='table' value='".$table."'>";
                        echo "<button type='submit' name='add_to_favorite'>Add to Favorites</button>";
                        echo "</form>";
                        echo "</td>";
                    }
                    echo "</tr>";
                }   
                echo "</table>";
                echo "</div>";
            }
            else {
                echo "<p>No results found.</p>";
            }
            // Process adding to favorites
            if (isset($_POST['add_to_favorite'])) {
                $stock_no = mysqli_real_escape_string($link, $_POST['stock_no']);
                $account = $_SESSION["name"]; // Replace with the desired account number
                $query = "INSERT INTO favorite (Account, stock_no) VALUES ('$account','$stock_no')";
                if (mysqli_query($link, $query)) {
                    echo " ";
                } else {
                    echo "Error adding stock number to favorites: " . mysqli_error($link);
                }
            }
        ?>
    </div>
    <div class="separation-line"></div>
    <h2 style="text-align: center;"> Favorite Stock </h2>
    <div class="table-wrapper">
        <?php
            $Account = $_SESSION["name"]; // Replace with your account value
            // Process delete from favorites
            if (isset($_POST['remove_from_favorite'])) {
                $stockNo = mysqli_real_escape_string($link, $_POST['stock_no']);
                $deleteQuery = "DELETE FROM favorite WHERE Account = '$Account' AND stock_no = '$stockNo' ";
                $deleteResult = mysqli_query($link, $deleteQuery);
                if ($deleteResult) {
                    echo "";
                } else {
                    echo "<p>Error removing data from favorites.</p>";
                }
            }
            $query = "SELECT cq.*, f.Account FROM close_quotation cq
                      INNER JOIN favorite f ON cq.stock_no = f.stock_no
                      WHERE f.Account = '$Account' ORDER BY cq.stock_no";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result) > 0) {
                echo "<div style='height: 300px; overflow-y: scroll;'>";
                echo "<table style='border-collapse: collapse;'>";
                echo "<tr>";
                echo "<th style='border: 1px solid black; padding: 8px;'>date</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>stock_no</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>stock_name</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>tot_volume</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>tot_num</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>tot_money</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>open_price</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>max_price</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>min_price</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>close_price</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>up_down</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>up_diff</th>";
                echo "<th style='border: 1px solid black; padding: 8px;'>Actions</th>";
                echo "</tr>";
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['date']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['stock_no']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['stock_name']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['tot_volume']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['tot_num']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['tot_money']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['open_price']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['max_price']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['min_price']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['close_price']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['up_down']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>".$row['up_diff']."</td>";
                    echo "<td style='border: 1px solid black; padding: 8px;'>";
                    echo "<form method='post'>";
                    echo "<input type='hidden' name='stock_no' value='".$row['stock_no']."'>";
                    echo "<input type='hidden' name='table' value='".$table."'>";
                    echo "<button type='submit' name='remove_from_favorite'>Remove</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>No results found.</p>";
            }
            ?>
    </div>
    <div class="separation-line"></div>
    <?php
         if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["stock_no"])) {
             $stock_no = $_POST["stock_no"];
             $query = "SELECT date, open_price, close_price, min_price, max_price FROM close_quotation WHERE stock_no = '$stock_no' ORDER BY date";
             $result = mysqli_query($link, $query);
         
             $dbData = array();
             while ($row = mysqli_fetch_assoc($result)) {
                 $dbData[] = $row;
             }
             if (empty($dbData)) {
                 echo "<p>The stock_no '$stock_no' does not exist.</p>";
             }
         }
         ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="stock_no">Stock Number:</label>
        <input type="text" id="stock_no" name="stock_no">
        <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
        <input type="submit" value="Submit">
    </form>
    <div id="stockChartContainer" style="width: 100%; height: 400px;"></div>
    <script>
    var stockNo = "<?php echo isset($stock_no) ? htmlspecialchars($stock_no) : 'N/A'; ?>";
    var dbData = <?php echo isset($dbData) ? json_encode($dbData) : '[]'; ?>;
    </script>
    <script src="temp.js"></script>
</body>

</html>