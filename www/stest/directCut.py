#!/usr/bin/env python

# -*- coding: utf-8 -*-

import jieba
import sys

lyric = sys.argv[1]
lyric = lyric.decode("big5", "strict").encode("utf8", "strict")

segments = jieba.cut(lyric, cut_all=False)

for i in segments:
    cutWord = i.encode("utf-8")
    sys.stdout.write(cutWord)
    sys.stdout.write("/")