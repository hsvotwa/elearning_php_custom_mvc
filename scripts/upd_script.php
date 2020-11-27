<?php
set_time_limit(0);
$mysql = new MySql();
$query = "";
echo $mysql->getQueryResult( $qry ) ? "Done." : "Error.";
return;