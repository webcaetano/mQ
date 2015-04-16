# mQ.php

simple PHP SQL class


example 1:
```
mQ('SELECT * FROM players WHERE 1');
```


example 2:
```
$id = mIns('players',['name="TheOddOne"']);
echo $id;
# 1  // return row insertion index
```