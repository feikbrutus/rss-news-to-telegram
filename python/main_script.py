import sqlite3
from time import sleep
import feedparser
from pip._vendor.requests.packages.urllib3.connectionpool import xrange
import telebot
import constants

bot = telebot.TeleBot(constants.token)
con = sqlite3.connect(constants.bd_name)
cur = con.cursor()

def refresh_news():
    mas = []
    d = feedparser.parse(constants.rsslink)
    news_count = len(d['entries'])
    for numb1 in xrange(news_count):
        title1 = d['entries'][numb1]['title']
        mas.append(title1)
    ##cur.execute('CREATE TABLE rss (id INTEGER PRIMARY KEY, title VARCHAR(300), link VARCHAR(300), date VARCHAR(35) ) ')
    ##con.commit()
    for numb in xrange(news_count):
        title_local = mas[numb]
        cur.execute('SELECT * FROM rss WHERE title LIKE ?', [title_local])
        if cur.fetchone() == None :
            print("новости нету. добавляем в базу:")
            bot.send_message(constants.ch_name, title_local+"\n"+ (d['entries'][numb]['link']) )
            cur.execute('INSERT INTO rss (id, title, link, date) VALUES(NULL, ?, ?, ?)', (title_local, d['entries'][numb]['link'], d['entries'][numb].published))
            con.commit()
            sleep(2)
            print("В базу добавлено:"+title_local)
        else:
            print("такая новость уже есть:" + "  " + title_local)

while True:
    refresh_news()
    sleep(60*15)   #15 min sleep

print("*" * 50)
con.close()