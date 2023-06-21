import pandas as pd
import numpy as np
import json
import requests
import pymysql as mysql


def Get_StockPrice(Symbol, Date):
    url = f'https://www.twse.com.tw/exchangeReport/STOCK_DAY?response=json&date={Date}&stockNo={Symbol}'
    data = requests.get(url).text
    json_data = json.loads(data)
    Stock_data = json_data['data']
    StockPrice = pd.DataFrame(Stock_data, columns=['date', 'Volume', 'Volume_Cash', 'open_price', 'max_price', 'min_price', 'close_price', 'Change', 'Order'])
    StockPrice['date'] = StockPrice['date'].str.replace('/', '').astype(int) + 19110000
    StockPrice['date'] = pd.to_datetime(StockPrice['date'].astype(str))
    StockPrice['Volume'] = StockPrice['Volume'].str.replace(',', '').astype(float) / 1000
    StockPrice['Volume_Cash'] = StockPrice['Volume_Cash'].str.replace(',', '').astype(float)
    StockPrice['Order'] = StockPrice['Order'].str.replace(',', '').astype(float)

    StockPrice['open_price'] = StockPrice['open_price'].str.replace(',', '').astype(float)
    StockPrice['max_price'] = StockPrice['max_price'].str.replace(',', '').astype(float)
    StockPrice['min_price'] = StockPrice['min_price'].str.replace(',', '').astype(float)
    StockPrice['close_price'] = StockPrice['close_price'].str.replace(',', '').astype(float)
    StockPrice = StockPrice[['date', 'open_price', 'close_price', 'min_price', 'max_price']]
    return StockPrice

stock_no = '0050'
data = Get_StockPrice(stock_no, '20220601')
db_settings = {
    "host": "127.0.0.1",
    "port": 3306,
    "user": "root",
    "password": "",
    "db": "final_project",
    "charset": "utf8"
}
conn = mysql.connect(**db_settings)
with conn.cursor() as cursor:
    for row in data.itertuples(index=False):
        sql = "INSERT INTO test (stock_no, date, open_price, close_price, min_price, max_price) VALUES (%s, %s, %s, %s, %s, %s);"
        cursor.execute(sql, (stock_no, row.date, row.open_price, row.close_price, row.min_price, row.max_price))
    conn.commit()

# Close the SQL connection
conn.close()
