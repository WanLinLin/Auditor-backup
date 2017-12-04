# The Files We Mainly Used

### `www/stest/client.php`
1. receives a sentence from android phone
2. send the sentence to python socket server, and receive the sentence's words
3. query database to recommend a word to user

### `python-server/socket_server.py`
1. opent a socket server to receive sentences from `www/stest/client.php`
2. cut a sentence into words and send them back to `www/stest/client.php`

### `www/stest/upload_lyric.php`
1. receive the word that user has choosen to be the rhyme word
2. ~~send the word to `python-server/lyric_server.py`~~ this part has been removed

### `python-server/lyric_server.py`
1. open a socket server to receive words sent by `www/stest/upload_lyric.php`
2. calculate the word's weighting to fit our recommend algo and update it into the database.

### `www/feedback_upload.php`
a web page to show the lyrics that upload by users

---

Other files mostly are testing files for development, we have merged some part of them into the files above to provide the main functions of the auditor server
