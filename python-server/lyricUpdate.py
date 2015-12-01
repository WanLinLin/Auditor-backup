# -*- coding: utf-8 -*-

import sys
import jieba
import MySQLdb
import urllib,urllib2
from bs4 import BeautifulSoup

def judgeRhyme (text):

    htmlSource = urllib.urlopen("http://chinese.cdict.info/chwwwcdict.php?word="+text).read(200000)
    soup = BeautifulSoup(htmlSource, "html.parser")

    song2 = soup.select(".markb")
    ryh = []
    #text兩個字以上
    if len(text) > 3:
        #print "兩個字以上"
        if len(song2) == 0:
            #再來一次 用副程式!!!!!!!!
            return judgeRhyme(text[-3:])
        else:
            #取出
            songText = song2[0].get_text()
            temp = songText.split(u"　")
            ntfSongText = temp[1].encode( 'utf-8' )

            if len(ntfSongText)%3  == 2:
                final = temp[1].encode( 'utf-8' )[-8:-5]
                #判斷重複韻
                if len(ryh) == 0:
                    ryh.append(final)
                else:
                    for j in range(len(ryh)):
                        if final == ryh[j-1]:
                            tag = 1
                            break
                    if tag == 0:
                        ryh.append(final)

            if len(ntfSongText)%3  == 0:
                final = temp[1].encode( 'utf-8' )[-6:-3]
                #判斷重複韻
                if len(ryh) == 0:
                    ryh.append(final)
                else:
                    for j in range(len(ryh)):
                        if final == ryh[j-1]:
                            tag = 1
                            break
                    if tag == 0:
                        ryh.append(final)

    else:
        for i in range(len(song2)):
            
            tag = 0
            if i % 2 ==0:
                songText = song2[i].get_text()
                ntfSongText = songText.encode( 'utf-8' )
                if len(ntfSongText)%3  == 2:
                    final = songText.encode( 'utf-8' )[-8:-5]
                    #判斷重複韻
                    if len(ryh) == 0:
                        ryh.append(final)
                        continue
                    else:
                        for j in range(len(ryh)):
                            if final == ryh[j-1]:
                                tag = 1
                                break
                        if tag == 0:
                            ryh.append(final)
                            
                if len(ntfSongText)%3  == 0:
                    final = songText.encode( 'utf-8' )[-6:-3]
                    #判斷重複韻
                    if len(ryh) == 0:
                        ryh.append(final)
                        continue
                    else:
                        for j in range(len(ryh)):
                            if final == ryh[j-1]:
                                tag = 1
                                break
                        if tag == 0:
                            ryh.append(final)
    return ryh



lyric = sys.argv[1]
#lyric = lyric.decode("big5", "strict").encode("utf8", "strict")

# GJ part starts

db = MySQLdb.connect("localhost",
            "root",
            "mis105RAY",
            "auditor",
            charset='utf8')

cursor = db.cursor()

userContext = lyric

userEachLineList = userContext.split('~')
del userEachLineList[-1]

for a in userEachLineList:
    print (">>>>" + a).decode('utf-8').encode('big5')

# deal with one single line
for i in userEachLineList:
    userEachLine = i
    userEachLineCut =  jieba.cut(userEachLine, cut_all = False)
    userEachWordList = []

    for j in userEachLineCut:
        userEachWordList.append(j.encode("utf8", "strict"))
        print j

    # now here deal with each word in a single line
    # do calculation and store them into DB

    for k in userEachWordList:
        text = k

        # for fear network connecting problem
        attempTimes = 0
        rhymeListGotten = False
           
        while (attempTimes < 10) and (not rhymeListGotten):
            try:
                rhymeList = judgeRhyme(text)
                rhymeListGotten = True
            except:
                print "Network Exception when getting rhymeList"
                attempTimes = attempTimes + 1
        
        if len(rhymeList) > 0:
            # by setting text column as UNIQUE in word table, we can insert directly
            try:
                sql = "INSERT INTO word(wId, text) VALUE(NULL, '" + text + "')"
                cursor.execute(sql)
                db.commit()
            except:
                msg = "There's already a " + text + " in word table"
                print msg.decode('utf-8').encode('big5')
                db.rollback()
            try:
                wIdRhymeBasedOnSql = "SELECT wId FROM word WHERE text = '{0}'".format(text)
                print "---dealing with rhyme table---"
                print wIdRhymeBasedOnSql.decode('utf-8').encode('big5')
                cursor.execute(wIdRhymeBasedOnSql)
                wIdRhymeBasedOn = cursor.fetchone()[0]
                for j in rhymeList:
                    rhymeToInput = j
                    rhymeInsertSql = "INSERT INTO rhyme(wId, rhymes) VALUE({0}, '{1}')".format(wIdRhymeBasedOn, rhymeToInput)
                    cursor.execute(rhymeInsertSql)
                    db.commit()
            except:
                print "There is already a same data of rhyme in rhyme table"

    # reverse to loop easily through ray's algo
    userEachWordList.reverse()

    # stops when containing only one word
    while len(userEachWordList) > 1:
        wordBasedOn = userEachWordList[0]
        del userEachWordList[0]

        # make step initialize to zero here to calculate value of distance and link
        step = 0

        wIdSearchSql = "SELECT wId FROM word WHERE text = '" + wordBasedOn + "'"
        try:
            cursor.execute(wIdSearchSql)
            wIdWordBasedOn = cursor.fetchone()[0]
            db.commit()
        except:
            print "Something wrong when searching wId"
            db.rollback()
        for l in userEachWordList:
            tags = l

            # each time make step + 1 to count distance and link
            step = step + 1

            existOrNotSql = "SELECT * FROM tag WHERE wId = {0} AND tags = '{1}'".format(wIdWordBasedOn, tags)
            try:
                print existOrNotSql.decode('utf-8').encode('big5')
                cursor.execute(existOrNotSql)
                examineResult = cursor.fetchone()  # may be none or a list containing an existing row
                # if the tag hasn't been in tag table before
                if examineResult is None:
                    # examine if wIdWordBasedOn exists in tag table
                    wIdExamineSql = "SELECT * FROM tag WHERE wId = {0}".format(wIdWordBasedOn)
                    print wIdExamineSql.decode('utf-8').encode('big5')
                    cursor.execute(wIdExamineSql)
                    wIdExamineResult = cursor.fetchone()

                    # case 1: wId X, tags O or X
                    if wIdExamineResult is None:
                        tId = 1

                    # case 2: wId O, tags X
                    else:
                        max_tIdSql = "SELECT COUNT(tags) FROM tag WHERE wId = {0}".format(wIdWordBasedOn)
                        print max_tIdSql.decode('utf-8').encode('big5')
                        cursor.execute(max_tIdSql)
                        tIdResult = cursor.fetchone()[0]
                        tId = tIdResult + 1

                    if step == 1:
                        link = 1
                        distance = 0
                    else:
                        link = 0
                        distance = 1.0 / step

                    tagINSERTsql = "INSERT INTO tag(wId, tId, tags, link, distance) VALUE({0}, {1}, '{2}', {3}, {4})".format(wIdWordBasedOn, tId, tags, link, distance)
                    print tagINSERTsql.decode('utf-8').encode('big5')
                    cursor.execute(tagINSERTsql)
                    db.commit()

                    # how to INSERT new data:
                    # get max tId of the current based wId by SELECT MAX(tId)
                    # WHERE wId = wIdWordBasedOn sql
                    # let wId = wIdWordBasedOn, tId = max tId + 1, tags = tags
                    # note that the above max tId u get is a non-zero number
                    # however max tId could be not found because its wId hasn't been built before
                    # so let wId = wIdWordBasedOn, tId = 1, tags = tags
                    # if step == 1, let link = 1, distance = 0
                    # otherwise, let link = 0, distance = (1.0/step)

                # if the tag was already in tag table
                else:
                    if step == 1:
                        link = examineResult[3] + 1
                        updateTags = examineResult[2].encode('utf-8')
                        UPDATEsql = "UPDATE tag SET link = {0} WHERE wId = {1} AND tags = '{2}'".format(link, examineResult[0], updateTags)
                        print UPDATEsql.decode('utf-8').encode('big5')
                        cursor.execute(UPDATEsql)
                        db.commit()
                    else:
                        distance = examineResult[4] + (1.0 / step)
                        updateTags = examineResult[2].encode('utf-8')
                        UPDATEsql = "UPDATE tag SET distance = {0} WHERE wId = {1} AND tags = '{2}'".format(distance, examineResult[0], updateTags)
                        print UPDATEsql.decode('utf-8').encode('big5')
                        cursor.execute(UPDATEsql)
                        db.commit()

                    # how to UPDATE existing data:
                    # if step == 1:
                        # UPDATE tag SET link = examineResult[3]+1
                        # WHERE wId = examineResult[0]
                        # AND tags = examineResult[2]
                    # if step > 1:
                        # UPDATE tag SET distance = examineResult[4]+(1.0)/step
                        # WHERE wId = examineResult[0]
                        # AND tags = examineResult[2]

                db.commit()

            except Exception as e:
                print "DB ERROR!!!"
                print e.decode('utf-8').encode('big5')
                db.rollback()

db.close()