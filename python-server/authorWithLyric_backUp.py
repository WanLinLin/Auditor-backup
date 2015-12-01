# -*- coding: utf-8 -*-

import MySQLdb

db = MySQLdb.connect("localhost",
                    "root",
                    "mis105RAY",
                    "auditor",
                    charset='utf8')

cursor = db.cursor()

# make it deal with all the files at one time
f = open(r'C:\workspace\tempjie3.txt', 'r')

eachLineList = f.readlines()

singleSongLyric = []

for i in eachLineList:	

	lyric = i

	print lyric
	print repr(lyric)
	

	if lyric == '/\n':
		author = singleSongLyric[-1]
		pureAuthorName = ''
		for letter in author:
			if letter != '/' and letter != '\n' and letter != '\r':
				pureAuthorName = pureAuthorName + letter

		for j in range(0, len(singleSongLyric) - 1):
			singleLine = singleSongLyric[j]
			wordsInSingleLineList = singleLine.split('/')
			del wordsInSingleLineList[0]
			del wordsInSingleLineList[-1]
			pureSingleLine = ''
			wId = ''
			for eachword in wordsInSingleLineList:
				if eachword != '\n':
					pureSingleLine = pureSingleLine + eachword

			for k in wordsInSingleLineList:
				singleWord = k
				wIdSearchingSql = "SELECT wId FROM word WHERE text = '{0}'".format(singleWord)
				print wIdSearchingSql
				try:
					cursor.execute(wIdSearchingSql)
					wId = cursor.fetchone()[0]
					db.commit()
				except:
					print "something wrong when searching {0}".format(singleWord)
					db.rollback()

				insertDataSql = "INSERT INTO lyric(wId, author, lyrics) VALUE({0}, '{1}', '{2}')".format(wId, pureAuthorName, pureSingleLine)
				print insertDataSql
				try:
					cursor.execute(insertDataSql)
					db.commit()
				except:
					print "ERROR when inserting {0}, {1}, {2}".format(wId, pureAuthorName, pureSingleLine)
					db.rollback()
		singleSongLyric = []
	else:
		singleSongLyric.append(lyric)



