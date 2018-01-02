# -*- coding: utf-8 -*-
"""
author:AnE
"""
import time#导入包
import sys
import requests
import re
import MySQLdb


print ('------------------------------------------------------------------------------')
print (u'|                          欢迎使用淘宝商品爬虫脚本                          |')
print ('|                                                                            |')
print (u'|                          请按照脚本提示使用本脚本                          |')
print ('|                                                                            |')
print (u'                   切勿将此脚本所得数据用于商业用途 后果自负                 |')
print (u'                   切勿将此脚本所得数据用于商业用途 后果自负                 |')
print (u'                   切勿将此脚本所得数据用于商业用途 后果自负                 |')
print ('|                                                                            |')
print ('|                             __author__:AnE                                 |')
print ('|                                                                            |')
print ('|                             Qq:791677681                                 |')
print ('------------------------------------------------------------------------------')



class TaoBao:

    def __init__(self):


        self.header = {
            "authority":"s.taobao.com",
            "method":"GET",
            "path":"/search?q=%E6%89%8B%E6%9C%BA",
            "scheme":"https",
            "accept":"text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
            "accept-encoding":"gzip, deflate, br",
            "accept-language":"zh-CN,zh;q=0.8",
            "user-agent":"Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36"
        }

    def GetDate(self,url,TableName = None):
        res = requests.get(url,headers = self.header)
        res.encoding = "utf8"
        res = res.text

        GOODS_PIDS = re.findall('"nid":"(.*?)"',res)
        GOODS_TITLES = re.findall(',"raw_title":"(.*?)"',res)
        GOODS_PRICES = re.findall(',"view_price":"(.*?)"',res)
        GOODS_IMGS = re.findall(',"pic_url":"(.*?)",',res)
        GOODS_SALES = re.findall(',"view_sales":"(.*?)",',res)

        for GOODS_PID,GOODS_TITLE,GOODS_PRICE,GOODS_IMG,GOODS_SALE in zip(GOODS_PIDS,GOODS_TITLES,GOODS_PRICES,GOODS_IMGS,GOODS_SALES):
            GOODS_IMG = GOODS_IMG if "https:" in GOODS_IMG else "https:" + GOODS_IMG
            GOODS_URL = "https://detail.tmall.com/item.htm?id=" + GOODS_PID
            print (GOODS_TITLE + "   "  +  "价格:" + GOODS_PRICE +  "    " + GOODS_URL + "\n")

            if TableName == None:
                pass
            else:
                self.InserDate(GOODS_TITLE,GOODS_PID,GOODS_PRICE,GOODS_IMG,GOODS_SALE,GOODS_URL,TableName)


    def OpenSql(self):
        self.db = MySQLdb.connect("localhost", "root", "2945377348Qzb", "TaoBao", charset="utf8")

        if self.db:
            print (u"连接数据库成功...")
            self.cursor = self.db.cursor()
        else:
            print (u"连接数据库失败,未知原因")


    def CloseSql(self):
        self.db.close()


    def CreateTable(self,TableName):
            sql = """CREATE TABLE %s (
                            ID INT NOT NULL AUTO_INCREMENT,
                            GOODS_TITLE VARCHAR (512),
                            GOODS_PID VARCHAR (128),
                            GOODS_PRICE VARCHAR (512),
                            GOODS_IMG VARCHAR(512),
                            GOODS_SALE VARCHAR(512),
                            GOODS_URL VARCHAR(512),
                            PRIMARY KEY(ID))DEFAULT CHARSET=UTF8"""%TableName
            try:
                self.cursor.execute(sql)
                self.db.commit()
                print (u"创建表格成功...")
            except Exception as e:
                print (e)
                sys.exit()


    def InserDate(self,GOODS_TITLE,GOODS_PID,GOODS_PRICE,GOODS_IMG,GOODS_SALE,GOODS_URL,TableName):
        sql = """INSERT INTO %s (GOODS_TITLE, GOODS_PID, GOODS_PRICE, GOODS_IMG, GOODS_SALE,GOODS_URL) VALUES ('%s', '%s', '%s', '%s', '%s', '%s')""" % (TableName,GOODS_TITLE,GOODS_PID,GOODS_PRICE,GOODS_IMG,GOODS_SALE,GOODS_URL)

        try:
            self.cursor.execute(sql)
            self.db.commit()
        except:
            self.db.rollback()


    def GetAllDate(self):
        GoodsName = input("请输入需要爬取商品的名称:")
        Pages = input(("请输入需要爬取的页数:"))
        YesNo = input(("是否保存数据到数据库?(y/n):")).encode("gbk")

        if YesNo != "n":
            TableName = input("请输入数据库表的名称:")
            self.OpenSql()
            self.CreateTable(TableName)

            for i in range(0,int(Pages)):
                print (u"------------------------------正在爬取第%d页------------------------------"%(i + 1))
                url = "https://s.taobao.com/search?q=%s&imgfile=&ie=utf8&S={}" %GoodsName
                url = url.format(44 * i)
                print (url)
                self.GetDate(url,TableName)

            self.CloseSql()

            print ("--------------------------------------------------------------------")
            print (u"爬取完成...")
            input("请按下Enter退出:")

        else:
            for i in range(0,int(Pages)):
                print (u"------------------------------正在爬取第%d页------------------------------" % (i + 1))
                url = "https://s.taobao.com/search?q=%s&imgfile=&ie=utf8&S={}" %GoodsName
                url = url.format(44 * i)
                self.GetDate(url)

            print ("--------------------------------------------------------------------")
            print (u"爬取完成...")
            input("请按下Enter退出:")

def main():
    Taobao = TaoBao()
    Taobao.GetAllDate()


if __name__ == "__main__":
    main()
