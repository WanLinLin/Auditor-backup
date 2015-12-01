# -*- coding: utf-8 -*-

# this program deals with tempjie2

import MySQLdb

db = MySQLdb.connect("localhost",
                    "root",
                    "mis105RAY",
                    "auditor",
                    charset='utf8')

cursor = db.cursor()

f = open(r'C:\workspace\tempjie2.txt', 'r')

eachLineList = f.readlines()

singleSongLyric = []

author = "SHE"

for i in eachLineList:

	lyric = i

	print lyric
	print repr(lyric)
	
	singleSongLyric.append(lyric)

	if lyric == '/\n':
		
		del singleSongLyric[-1]

		for j in range(0, len(singleSongLyric)):
			singleLine = singleSongLyric[j]
			wordsInSingleLineList = singleLine.split('/')
			del wordsInSingleLineList[0]
			del wordsInSingleLineList[-1]
			pureSingleLine = ''
			for eachword in wordsInSingleLineList:
				if eachword != '\n':
					pureSingleLine = pureSingleLine + eachword

			for k in wordsInSingleLineList:
				singleWord = k
				wIdSearchingSql = "SELECT wId FROM word WHERE text = '{0}'".format(singleWord)
				print wIdSearchingSql
				wId = ''
				try:
					cursor.execute(wIdSearchingSql)
					wId = cursor.fetchone()[0]
					db.commit()
				except:
					print "something wrong when searching {0}".format(singleWord)
					db.rollback()

				insertDataSql = "INSERT INTO lyric(wId, author, lyrics) VALUE({0}, '{1}', '{2}')".format(wId, author, pureSingleLine)
				print insertDataSql
				try:
					cursor.execute(insertDataSql)
					db.commit()
				except:
					print "ERROR when inserting {0}, {1}, {2}".format(wId, author, pureSingleLine)
					db.rollback()
		singleSongLyric = []
		



