# mQ.php

simple PHP SQL functions

## Documentation

config.php - credencials file
mQ.php - main file

- [connect](#connect)
- [mQ](#mq)
- [mIns](#mins)
- [mNum](#mnum)
- [mQN](#mqn)
- [oV](#ov)

### connect

Connect to database.

```php
connect('players');
```


### mQ

Run SQL command.
mQ(sql)

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


### mNum

Return num of rows in a query
mNum(query)

```php
$sql = mQ('SELECT * FROM players ');
echo mNum($sql);
// 2

or 

echo mNum(mQ('SELECT * FROM players '));
//2
```

### mQN

Run command and return num of rows in this query
mQN(sql)

```php
echo mQN('SELECT * FROM players ');
//2
//same as mNum(mQ('SELECT * FROM players '));
```


### oV
(One Value)
Return a single value of A single Row 
oV(sql)

```php
echo oV('SELECT name FROM players Limit 0,1');
//Polt
```