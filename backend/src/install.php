<?php

include_once('./include/table_user.php') ;
include_once('./include/table_game.php') ;
include_once('./include/table_message.php') ;
include_once('./include/table_log.php') ;
include_once('./include/table_pending_game.php') ;
include_once('./include/table_session.php') ;
include_once('./include/table_move.php') ;



//create the tables:
$successful = True ;

$successful = user::create_table()         && $successful  ;
if(!$successful)
{
	echo "Failed to create tables" ;
}
$successful = game::create_table()         && $successful  ;
$successful = message::create_table()      && $successful  ;
$successful = log::create_table()          && $successful  ;
$successful = pending_game::create_table() && $successful  ;
$successful = session::create_table()      && $successful  ;
$successful = move::create_table()         && $successful  ;



if(!$successful)
{
	echo "Failed to create tables" ;
}
else
{
	echo "OK" ;
}

?>
