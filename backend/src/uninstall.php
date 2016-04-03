<?php

include_once('./include/table_user.php') ;
include_once('./include/table_game.php') ;
include_once('./include/table_message.php') ;
include_once('./include/table_log.php') ;
include_once('./include/table_pending_game.php') ;
include_once('./include/table_session.php') ;
include_once('./include/table_move.php') ;



//drop the tables:
$successful = True ;

$successful = move::drop_table()         && $successful  ;
$successful = session::drop_table()      && $successful  ;
$successful = pending_game::drop_table() && $successful  ;
$successful = log::drop_table()          && $successful  ;
$successful = message::drop_table()      && $successful  ;
$successful = game::drop_table()         && $successful  ;
$successful = user::drop_table()         && $successful  ;



if(!$successful)
{
	echo "Failed to delete tables" ;
}
else
{
	echo "OK" ;
}


?>
