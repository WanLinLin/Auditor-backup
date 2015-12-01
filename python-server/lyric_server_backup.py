import socket
import sys
import thread
import jieba
import time
import struct

def sendResult(client_socket, string):
    try:
        start_time = time.time()
        recv_msg = client_socket.recv(1024) # recv msg from android
        lyric = recv_msg[2:].decode('utf-8') # skip 2 bytes
        print "received: %s" % lyric


        # Guan J


        end_time = time.time()

        print "process time: %6f seconds." % ((end_time-start_time))
    finally:
        client_socket.close()

server_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
server_socket.bind(('0.0.0.0', 1222)) # 0.0.0.0 for any client address
server_socket.listen(5)

while True:
    (client_socket, address) = server_socket.accept()
    thread.start_new_thread(sendResult, (client_socket, None))

server_socket.close()