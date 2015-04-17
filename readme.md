# mQ.php

simple PHP SQL functions
and JSON results convert 

## Documentation

config.php - credencials file
mQ.php - main file

- [connect](#connect)
- [mIns](#mins)
- [mQ](#mq)
- [mRow](#mrow)
- [mRows](#mrows)
- [mDel](#mdel)
- [mSet](#mset)
- [mAr](#mar)
- [mNum](#mnum)
- [mQN](#mqn)
- [oV](#ov)

### connect

Connect to database.

```php
connect('players');
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

### mQ

Run SQL command. 

mQ(sql)

```php
mQ('SELECT * FROM players WHERE 1');
``` 

### mRow

Return a single row SQL result 

mRow(query)


```php
$row1 = mRow('SELECT * FROM players WHERE 1');
``` 

### mRows

Return an array of rows

mRows(query)


```php
$allRows = mRows('SELECT * FROM players WHERE 1');
```

### mDel

Runs a DELETE comand in a table


mDel(table,where)


```php
mDel('players',['name="TheOddOne"','team="TSM"']); // runs DELETE FROM players WHERE name="TheOddOne" and team="TSM"

or 

mDel('players','1'); // runs DELETE FROM players WHERE 1
```

### mSet

Runs a UPDATE comand in a table

mSet(table,set,where)


```php
// @table could be string or array
// @set could be string or array
// @where could be string or array

mSet('players',['name="TheOddOne"','team="TSM"'],'team=""'); // runs UPDATE players set name="TheOddOne", team="TSM" WHERE team=""
```

### mAr

Turns SQL query and return the next row. 

mAr(query)


```php
$row1 = mAr(mQ('SELECT * FROM players WHERE 1'));
``` 

### mNum

Return num of rows in a query. 

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

Run command and return num of rows in this query.

mQN(sql)

```php
echo mQN('SELECT * FROM players ');
//2
//same as mNum(mQ('SELECT * FROM players '));
```


### oV
(One Value)
Return a single value of A single Row.

oV(sql)

```php
echo oV('SELECT name FROM players Limit 0,1');
//Polt
```