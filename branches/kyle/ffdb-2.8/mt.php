<?php

require 'ffdb.inc.php';

// Open the database called 'phonebook'
$db = new FFDB();
if (!$db->open("phonebook"))
{
   // Define the database shema.  
   // Note that the "last_name" field is our key.
   $schema = array( 
      array("first_name", FFDB_STRING),
      array("last_name", FFDB_STRING, "key"), 
      array("age", FFDB_INT),
      array("phone", FFDB_STRING)
   );
   
   // Try and create it... 
   if (!$db->create("phonebook", $schema))
   {
      echo "Error creating database\n";
      return;
   }
}

$record["last_name"] = 'kyle';
$record["first_name"] = 'xu';
$record["age"]= 24; 
$record["phone"] = '2048';
// Add a _new_ entry
echo("Adding record... ");
if (!$db->add($record))
	echo("failed!\n");
else
	echo("success!\n");

echo("Database records: ".$db->size()."\n");

if (!isset($sortby)) $sortby = NULL;
$result = $db->getall($sortby);

// Get all people ages 20 and up, ordered by age
//$result = $db->getbyfunction("ages20andup", "age");

foreach($result as $item)  show_record($item);

// Shows a record in table format
function show_record($record)
{
   global $PHP_SELF;

   echo("<tr>\n");
   echo("  <td>\n");
   echo("     <a href=\"$PHP_SELF?lookat=".$record["last_name"]."\">".$record["last_name"]."</a>\n");
   echo("  </td>\n");
   echo("  <td>".$record["first_name"]."</td>\n");
   echo("  <td>".$record["age"]."</td>\n");
   echo("  <td>".$record["phone"]."</td>\n");
   echo("  <td>\n");
   echo("     [ <a href=\"$PHP_SELF?delete=".$record["last_name"]."\">delete</a> ]\n");
   echo("  </td>\n");
}


$delete = $record['last_name'];

if (!$db->exists($delete))
	echo("Item does not exist!\n");
else
{
	// Delete the item
	if (!$db->removebykey($delete))
		echo("Unable to delete item '".$delete."'.\n");
	else
		echo("Item deleted.\n");
}

echo "done";