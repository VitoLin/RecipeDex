<?php
    function connect(){
        $link = mysqli_connect('localhost', 'vlin2', '123') or die ('Could not connect to DB' . mysql_error());
        mysqli_select_db($link, 'vlin2');
        return $link;
    }

    function getTable($query, $total_records_per_page, $offset){
        return "$query limit $total_records_per_page offset $offset;";
    }
?>