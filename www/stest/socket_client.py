import socket
import sys

s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
s.connect(('127.0.0.1', 80))

try:
    message = 'client!'
    s.sendall(message)

    data = s.recv(16)
    print >>sys.stderr, 'received "%s"' % data

finally:
    s.close()