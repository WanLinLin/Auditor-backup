import socket
import sys
import thread
import jieba

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind(('127.0.0.1', 1242))
server_socket.listen(5)

def CutThread(client_socket, string):
    try:
        data = client_socket.recv(1024)
        print >>sys.stderr, 'received "%s"' % data
        if data:
            segments = jieba.cut(data, cut_all=False)
            s = ""
            for i in segments:
                cutWord = i.encode("utf-8")
                s += cutWord
                s += '/'

            client_socket.sendall(s)

    finally:
        client_socket.close()

while (i < 5):
    (client_socket, address) = server_socket.accept()
    thread.start_new_thread(CutThread, (client_socket, None))

server_socket.close()