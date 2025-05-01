<?php
$connection = pg_connect("host=172.20.7.53 port=5432 dbname=db2991_17 user=st2991 password=pwd2991 connect_timeout=20");
$schema = "sneakerstore";
pg_query($connection,"set search_path to $schema");

if (!$connection) {
    echo "ошибка подключения";
}
?>