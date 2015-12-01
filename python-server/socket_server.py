import socket
import sys
import thread
import jieba
import time

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind(('127.0.0.1', 1242))
server_socket.listen(5)

def CutThread(client_socket, string):
    try:
        start_time = time.time()
        sentence = client_socket.recv(1024)
        stringdata = sentence.decode('utf-8')
        print >>sys.stderr, 'received "%s"' % stringdata

        if sentence:
            segments = jieba.cut(sentence, cut_all=False)
            s = ""
            for i in segments:
                cutWord = i.encode("utf-8")
                s += cutWord
                s += '/'
            client_socket.sendall(s)
        end_time = time.time()

        print "cut time: %3f seconds." % ((end_time-start_time))
    finally:
        client_socket.close()

while True:
    (client_socket, address) = server_socket.accept()
    thread.start_new_thread(CutThread, (client_socket, None))

server_socket.close()