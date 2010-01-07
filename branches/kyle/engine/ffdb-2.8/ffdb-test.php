<html>
<body>
<?php
//------------------------------------------------------------------------
// File:    ffdb-test.php
// Author:  John Papandriopoulos <jpap@users.sourceforge.net>
//
// Copyright (C) 2002 John Papandriopoulos.  All rights reserved.
//
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the Free Software
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//
//------------------------------------------------------------------------

// Include the FFDB library
include("ffdb.inc.php");

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

/** Delete/drop the database */
if (isset($drop))
{
   if ($db->drop())
   {
      echo("Database dropped.<br><br>\n");
      echo("[ <a href=\"$PHP_SELF\">Create new database</a>. ]\n");
      return;
   }  
   else
      echo("Error dropping database.\n");
}
/** Clean up the database */
else if (isset($cleanup))
{
   if ($db->cleanup())
      echo("Database cleaned up.\n");
   else
      echo("Error cleaning database.\n");
}
/** Delete an item */
else if (isset($delete))
{
   // See if it exists first
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
}
/** Look up an individual entry */
else if (isset($lookat))
{
   $record = $db->getbykey($lookat);
   if (!$record)
   {
      echo("Item not found!\n");
      echo("[ <a href=\"$PHP_SELF\">Return to the database</a> ]\n");
      return;
   }

   // Show the entry details
   echo("Looking up individual: $lookat<br><br>\n");

   echo("<table border=1 cellpadding=2>\n");
   echo("<tr>\n");
   echo("   <th>Last Name</th>\n");
   echo("   <th>First Name</th>\n");
   echo("   <th>Age</th>\n");
   echo("   <th>Phone</th>\n");
   echo("</tr>\n");

   // Show the record details in the table
   echo("<tr>\n");
   echo("<td>".$record["last_name"]."</td>\n");
   echo("<td>".$record["first_name"]."</td>\n");
   echo("<td>".$record["age"]."</td>\n");
   echo("<td>".$record["phone"]."</td>\n");
   echo("</tr>\n");
   echo("</table><br><br>\n");

   echo("[ <a href=\"$PHP_SELF\">Return to the database</a> ]\n");
   return;
}
/** Looks like they are adding a new entry... */
else if (isset($last_name))
{
   // Convert the form into a database record
   $record["last_name"] = $last_name;
   $record["first_name"] = $first_name;
   list($record["age"]) = sscanf($age, "%d"); // string -> int
   $record["phone"] = $phone;

   if (!isset($edit))
   {
      // Add a _new_ entry
      echo("Adding record... ");
      if (!$db->add($record))
         echo("failed!\n");
      else
         echo("success!\n");
   }
   else
   {
      // Edit an _existing_ entry
      echo("Editing record...");
      if (!$db->edit($record))
         echo("failed!\n");
      else
         echo("success!\n");
   }
}
echo("<br><br>\n");

// Show a count of how many records are in the table
echo("Database records: ".$db->size()."<br>\n");
?>

<table border=1 cellpadding=2>

<tr>
   <th><a href="<?php echo "$PHP_SELF?sortby=last_name"; ?>">Last Name</a></th>
   <th><a href="<?php echo "$PHP_SELF?sortby=first_name"; ?>">First Name</a></th>
   <th><a href="<?php echo "$PHP_SELF?sortby=age"; ?>">Age</a></th>
   <th><a href="<?php echo "$PHP_SELF?sortby=phone"; ?>">Phone</a></th>
   <th></th>
</tr>

<?php
/** List all records in the database */

/*
// Method #1: using an iterator
$db->reset();
do
{
   if ($item = $db->current())
      show_record($item);
} 
while ($db->next());
*/

// Method #2: using getXXX(...)

// Get all records, and order by age
if (!isset($sortby)) $sortby = NULL;
$result = $db->getall($sortby);

// Get all people ages 20 and up, ordered by age
//$result = $db->getbyfunction("ages20andup", "age");

foreach($result as $item)
   show_record($item);

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

// Returns true when the record has age greater and equal to 20.
function ages20andup($record)
{
   return ($record["age"] >= 20);
}
?>
</table>

<?php
echo("<form method=\"post\" action=\"$PHP_SELF\">");
?>
Add new entry:

<blockquote>
   <table border = 0>
   <tr>
      <td>Last name:</td>
      <td><input type="text" name="last_name"></td>
   </tr>
   <tr>
      <td>First name:</td>
      <td><input type="text" name="first_name"></td>
   </tr>
   <tr>
      <td>Age:</td>
      <td><input type="text" name="age"></td>
   </tr>
   <tr>
      <td>Phone:</td>
      <td><input type="text" name="phone"></td>
   </tr>
   <tr>
      <td colspan=2>
         <input type="checkbox" value="yes" name="edit"> Edit this entry
      </td>
   </tr>
   </table>
</blockquote>

<input type="submit" value="Add/Edit Item">
</form>

<p>
[ <a href="<?php echo "$PHP_SELF"; ?>">View All Database Records</a> ]
[ <a href="<?php echo "$PHP_SELF?cleanup=1"; ?>">Cleanup Database</a> ]
[ <a href="<?php echo "$PHP_SELF?drop=1"; ?>">Delete Database</a> ]
</p>

<hr>
<small>Copyright &copy 2002 John Papandriopoulos.  All rights reserved.</small>
</body>
</html>
