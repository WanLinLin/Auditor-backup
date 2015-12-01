#!/usr/bin/env python
# -*- coding: utf-8 -*-

import jieba
import sys

lyric = sys.argv[1]
# lyric.decode("utf8");
# lyric = unicode(sys.argv[1], 'utf-8')

#utf8Lyric = lyric.encode('utf-8')
#print repr(utf8Lyric)
#print utf8Lyric

# ulyric = unicode(lyric, 'utf-8')
# print repr(lyric)
print lyric

# segments = jieba.cut(lyric, cut_all=False)

# for i in segments:
# 	print i.encode("utf8") + ","

print "done"