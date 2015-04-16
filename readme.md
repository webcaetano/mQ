# mQ.php

simple PHP SQL class

config.php - credencials file
mQ.php - main file

- [connect](#connect)
- [mQ](#mq)
- [mIns](#mins)

### connect

Connect to database.

```php
connect('players');
```


### mQ

Run SQL command.

```php
mQ('SELECT * FROM players WHERE 1');
``` 

### mIns

Insert mySql row and return row index.
mIns(table,cols)

```php
mIns('players','name="Polt"');

$id = mIns('players',['name="TheOddOne"','team="TSM"']);
echo $id;
# 2  // return row insertion index
```
