rem dir /s /b /a:d > allFiles.txt
rem type allFiles.txt | php -f findNadlib.php > nadlibFiles.txt
rem php nadlibPushAll.php < nadlibFiles.txt

dir /s /b /a:d | php -f findNadlib.php | php nadlibPushAll.php
