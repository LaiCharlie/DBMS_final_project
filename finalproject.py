# coding: utf-8
import requests
import pandas as pd
import numpy as np
import pymysql as mysql
import datetime
import pytz


# 取得今天日期
def date_get_today():
    central = pytz.timezone('Asia/Taipei')
    now = datetime.datetime.now(central).date().strftime("%Y-%m-%d 00:00:00")
    return now
# 刪掉數字中的逗號 ex: 12,345.67 => 12345.67
def delete_comma(num):
    num_str = num.split(',')
    num = ""
    for part in num_str:
        num = num + part
    return num
# 刪掉正負值的<style>
def choose_up_down(value):
    for char in value:
        if(char == '+'):
            return '+'
        elif(char == '-'):
            return '-'
    return '*'
# 取得data1並處理資料
def get_data1(data):
    data1 = pd.DataFrame(data['data4'])
    data1.columns = ['name', 'close_point', 'up_down', 'up_point', 'up_per', 'special']
    # 只要 '寶島股價報酬指數'
    data1 = data1.loc[0,:]
    # 刪掉數字中的逗號
    data1['close_point'] = delete_comma(data1['close_point'])
    # 把正負值的<style>刪掉
    data1['up_down'] = choose_up_down(data1['up_down'])
    return data1
# 取得data2並處理資料
def get_data2(data):
    data2 = pd.DataFrame(data['data1'])
    data2.columns = ['name', 'close_point', 'up_down', 'up_point', 'up_per', 'special']
    # 只要 '寶島股價指數'
    data2 = data2.loc[0,:]
    # 刪掉數字中的逗號
    data2['close_point'] = delete_comma(data2['close_point'])
    # 把正負值的<style>刪掉
    data2['up_down'] = choose_up_down(data2['up_down'])
    return data2
# 取得data3並處理資料
def get_data3(data):
    data3 = pd.DataFrame(data['data9'])
    data3.columns = ['stock_no', 'stock_name', 'tot_volume', 'tot_num', 'tot_money', 'open_price',
                     'max_price' ,'min_price', 'close_price', 'up_down', 'up_diff', 'final_buy_money',
                     'final_buy_volume', 'final_sell_money', 'final_sell_volime', 'P/E_ratio']
    # 刪掉正負值的<style>跟 數字中的逗號
    for key, value in data3.iterrows():
        value['tot_volume'] = delete_comma(value['tot_volume'])
        value['tot_num'] = delete_comma(value['tot_num'])
        value['tot_money'] = delete_comma(value['tot_money'])
        value['up_down'] = choose_up_down(value['up_down'])
        value['open_price'] = delete_comma(value['open_price'])
        value['max_price'] = delete_comma(value['max_price'])
        value['min_price'] = delete_comma(value['min_price'])
        value['close_price'] = delete_comma(value['close_price'])
        # 當天沒開盤
        if(value['open_price'] == '--'):
            value['open_price'] = value['max_price'] = value['min_price'] = value['close_price'] = '0'
    return data3
# 取得data4並處理資料
def get_data4(data):
    data4 = pd.DataFrame(data['data7'])
    data4.columns = ['name', 'tot_money', 'tot_volume', 'tot_num']
    # 只要 '一般股票'
    data4 = data4.loc[0,:]
    # 刪掉數字中的逗號
    data4['tot_money'] = delete_comma(data4['tot_money'])
    data4['tot_volume'] = delete_comma(data4['tot_volume'])
    data4['tot_num'] = delete_comma(data4['tot_num'])
    return data4

# api link
link = 'https://www.twse.com.tw/exchangeReport/MI_INDEX?response=json&type=ALLBUT0999'

# 取得data
data = requests.get(link).json()
data1 = get_data1(data)
data2 = get_data2(data)
data3 = get_data3(data)
data4 = get_data4(data)

# 資料庫設定
db_settings = {
    "host": "127.0.0.1",
    "port": 3306,
    "user": "root",
    "password": "googleking110652042",
    "db": "finalproject",
    "charset": "utf8"
}

def MySQL():
    today = date_get_today()
    try:
        # 建立Connection物件
        conn = mysql.connect(**db_settings)
        # 建立Cursor物件
        with conn.cursor() as cursor:
            # 設定指令
            init_command1 = 'DELETE FROM t_remuneration WHERE date=%s;'
            init_command2 = ' DELETE FROM t_price WHERE date=%s;'
            init_command3 = ' DELETE FROM close_quotation WHERE date=%s;'
            init_command4 = ' DELETE FROM market_statistics WHERE date=%s;'
            insert_command1 = 'INSERT INTO t_remuneration(date, close_point, up_down, up_point, up_per)VALUE(%s, %s, %s, %s, %s);'
            insert_command2 = 'INSERT INTO t_price(date, close_point, up_down, up_point, up_per)VALUE(%s, %s, %s, %s, %s);'
            insert_command3 = 'INSERT INTO close_quotation(date, stock_no, tot_volume, tot_num, tot_money, open_price, max_price, min_price, close_price, up_down, up_diff)VALUE(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s);'
            insert_command4 = 'INSERT INTO market_statistics(date, tot_money, tot_volume, tot_num)VALUE(%s, %s, %s, %s);'
            # 執行指令 -------------------------------------------------------------------------------------------------------------------------------
            # Initialize
            cursor.execute(init_command1, today)
            cursor.execute(init_command2, today)
            cursor.execute(init_command3, today)
            cursor.execute(init_command4, today)
            # Load data
            cursor.execute(insert_command1, (today, data1['close_point'], data1['up_down'], data1['up_point'], data1['up_per']))
            cursor.execute(insert_command2, (today, data2['close_point'], data2['up_down'], data2['up_point'], data2['up_per']))
            for key, row in data3.iterrows():
                cursor.execute(insert_command3, (today, row['stock_no'], row['tot_volume'], row['tot_num'], row['tot_money'], row['open_price'],
                                                        row['max_price'], row['min_price'], row['close_price'], row['up_down'], row['up_diff']))
            cursor.execute(insert_command4, (today, data4['tot_money'], data4['tot_volume'], data4['tot_num']))
            # 執行結束 -------------------------------------------------------------------------------------------------------------------------------
            # 儲存更改
            conn.commit()

    except Exception as ex:
        print(ex)
MySQL()