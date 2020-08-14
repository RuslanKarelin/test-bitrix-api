# test-bitrix-api
Тестовое задание

Пример запросов

create item

curl -H "Token:yYb8XU3rPR2" -d '{"method":"post","params":{"name":"name","phone":"phone","address":"address","time":"time","type":"круглосуточный"}}' http://sitename/api/v1/


update item

curl -H "Token:yYb8XU3rPR2" -d '{"method":"patch","id":"1","params":{"name":"name2","phone":"phone2","address":"address2","time":"time2","type":"VIP"}}' http://sitename/api/v1/


get item

curl -H "Token:yYb8XU3rPR2" -d '{"method":"get","id":"1"}' http://sitename/api/v1/


get a list of items

curl -H "Token:yYb8XU3rPR2" -d '{"method":"get"}' http://sitename/api/v1/


get a list of items with filter

curl -H "Token:yYb8XU3rPR2" -d '{"method":"get","filter":{"type":"круглосуточный"}}' http://sitename/api/v1/


delete item

curl -H "Token:yYb8XU3rPR2" -d '{"method":"delete","id":"1"}' http://sitename/api/v1/
