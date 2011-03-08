<?  // Maintenance

heading("Running Maintenance...", 0);

echo "Delete orphaned events (events with no valid ticket)<p>";

$r1 = query( "SELECT e_id, t_id FROM events ORDER BY t_id;" );
$r2 = query( "SELECT t_id FROM ticket ORDER BY t_id;" );

$tickets = array();

while( $row = mysql_fetch_row( $r2 ) )
    array_push( $tickets, $row[0] );

$prev = -1;
while( $row = mysql_fetch_row( $r1 ) ) {
    $e_id = $row[0];
    $t_id = $row[1];
    if( !in_array( $t_id, $tickets ) && $prev != $t_id ) {
        query( "DELETE FROM events WHERE t_id='$t_id';" );
        print "<br>DELETE FROM events WHERE t_id='$t_id';";
	$prev = $t_id;
    }
}


print "<p><h4>Done!</h2>";

?>

