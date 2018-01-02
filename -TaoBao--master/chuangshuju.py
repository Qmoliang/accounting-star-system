import pymysql
import datetime
import random
import string
import time
import pandas as pd
import csv
#批量插的次数
loop_count = 10
#每次批量查的数据量
batch_size = 100
success_count = 0
fails_count = 0
#数据库的连接
conn = pymysql.connect(host="127.0.0.1", user="root", passwd="2945377348Qzb", db="hw1", port=3306, cursorclass = pymysql.cursors.SSCursor)
chars = 'AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz'
digits = '0123456789'
month = ['01','02','03','04','05','06','07','08','09','10','11','12']
day = ['01','02','03','04','05','06','07','08','09','10','11','12'\
       ,'13','14','15','16','17','18','19','20','21','22','23','24',\
       '25','26','27','28']
IDcard=[]
#kouhong3
with open('shoushi2.csv','r') as csvfile:
    reader = csv.DictReader(csvfile)
    price_kouhong = [row['GOODS_PRICE'] for row in reader]

def dateRange(bgn, end):
    fmt = '%Y-%m-%d'
    bgn = int(time.mktime(time.strptime(bgn,fmt)))
    end = int(time.mktime(time.strptime(end,fmt)))
    timelist = [time.strftime(fmt,time.localtime(i)) for i in range(bgn,end+1,3600)]
    randomti = random.sample(timelist,1)
    return randomti
def random_generate_string(length):
    return "".join(random.sample(chars, length))
def random_generate_number(length):
    if length > len(digits):
        digit_list = random.sample(digits, len(digits))
        digit_list.append(random.choice(digits))
        return string.join(digit_list, '')
    return string.join(random.sample(digits, length), '')
def random_generate_IDcard():
    flag = True
    while(flag):
        digit_list = random.sample(str(digits), 6)
        digit_list.append(str(random.randint(1910, 2000)))
        digit_list.append(str(random.sample(month, 1))[2:4])
        digit_list.append(str(random.sample(day, 1))[2:4])
        digit_list.append(str(random.randint(0000,9999)))
        if ("".join(digit_list) not in IDcard):
            IDcard.append("".join(digit_list))
            flag = False
    return "".join(digit_list)
    
#s生成不均匀的购买
def ratioTable(ra):
   c=0
   ratio=[]
   for r in ra:
      c+=r
      ratio.append(c)
   return ratio
def Buy_qty(ratio,rnd):
   i=0
   for r in ratio:
      if rnd<r:
         return i
      i+=1
   return i
d= [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
ratio=ratioTable([10000,500,0,0,0,0,0,0,0])    
def random_generate_data(num):
    c = [num]
    phone_num_seed = 13100000000
    def _random_generate_data():
        c[0] += 1
        product_id=random.randrange(82,215)
        qty=d[Buy_qty(ratio,random.randrange(ratio[-1]))]
        return (
            c[0],
            product_id,#"product_id"
            qty,#"qty"
            float(price_kouhong[int(product_id-82)-1])*int(qty),#price
            dateRange('2018-01-01', '2018-12-31')[0]
        )
    return _random_generate_data
def datelist(beginDate, endDate):
    # beginDate, endDate是形如‘20160601’的字符串或datetime格式
    date_l=[datetime.strftime(x,'%Y-%m-%d') for x in list(pd.date_range(start=beginDate, end=endDate))]
    return date_l
def execute_many(insert_sql, batch_data):
    global success_count, fails_count
    cursor = conn.cursor()
    try:
        cursor.executemany(insert_sql, batch_data)
    except Exception as e:
        conn.rollback()
        fails_count = fails_count + len(batch_data)
        print (e)
        raise
    else:
        conn.commit()
        success_count = success_count + len(batch_data)
        print (str(success_count) + " commit")
    finally:
        cursor.close()
try:
    #user表列的数量
    column_count = 5

    #插入的SQL
    insert_sql = "insert into sales(id, product_id, qty, price, date) values (" + ",".join([ "%s" for x in range(column_count)]) + ")"
    batch_count = 0
    begin_time = time.time()
    for x in range(loop_count):
        batch_count =  x * batch_size + 1000
        gen_fun = random_generate_data(batch_count)
        batch_data = [gen_fun() for x in range(batch_size)]
        execute_many(insert_sql, batch_data)
    end_time = time.time()
    total_sec = end_time - begin_time
    qps = success_count / total_sec
    print ("总共生成数据： " + str(success_count))
    print ("总共耗时(s): " + str(total_sec))
    print ("QPS: " + str(qps))
except Exception as e:
    print (e)
    raise
else:
    pass
finally:
    pass
#CREATE TABLE `caifutong`.`user` (
#  `id` int(11) NOT NULL,
#  `ACCOUNT` varchar(45) DEFAULT NULL,
#  `name` varchar(45) DEFAULT NULL,
#  `IDcard` varchar(45) DEFAULT NULL,
#  `sex` set('M','F')  DEFAULT NULL,
#  `age` tinyint(1) DEFAULT NULL,
#  `phone` varchar(11) DEFAULT NULL,
#  `address` varchar(45) DEFAULT NULL,
#  `password` varchar(45) DEFAULT NULL,
#  `create_time` datetime DEFAULT NULL,
#  `RegisterDT` datetime DEFAULT NULL,
#  PRIMARY KEY (`id`),
#  KEY `idx_phone` (`phone`) USING BTREE,
#  KEY `idx_create_time` (`create_time`) USING BTREE
#) ENGINE=InnoDB DEFAULT CHARSET=utf8;