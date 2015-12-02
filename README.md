#What we actually use?
1.Receive a sentence from android phone, and send the sentence to **socket_server.py**. **socket_server.py** will cut the sentence into words and sent them back. Then, **client.php** will query the database to recommend a word to user.
```
www/stest/client.php
```
2.Receive a sentence from **client.php**, and cuts it into words. Then, the words will be sent back to **client.php** to query the database and recommend a word to user.
```
python-server/socket_server.py
```
3.Browse the lyrics that upload by users
```
www/feedback_unupload.php
```
