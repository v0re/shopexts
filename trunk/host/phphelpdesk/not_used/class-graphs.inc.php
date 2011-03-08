<? 
/* 
####################################################################### 
# 
#  About: 
# 
#  The following PHP3 code provides a nice class interface for 
#  html graphs.  It provides a single, reasonably consistent 
#  interface for creating HTML based graphs.  The idea behind 
#  this code is that the user of the class sets up four or five 
#  arrays and pass these to html_graph() which then takes 
#  care of all the messy HTML layout.  I am reasonably happy 
#  with the outcome of this interface.  The HTML that must be 
#  generated for HTML graphs *is* messy, and the interface is 
#  very clean and flexible.  I think that once you generate 
#  one graph with it, you'll never look at creating HTML graphs 
#  the same.  The arrays that must be set up consist of: 
# 
#       * A names array containing column/row identifiers ($names) 
#       * One or two values arrays containg corresponding  
#         values to the column/row names ($values & $dvalues) 
#       * One or two bars array which also corresponds to the names 
#         array.  The values in these arrays are URLS to graphics 
#         or color codes starting with a # which will be used to 
#         generate the graph bar.  Color codes and graphics may 
#         be mixed in the same chart, although color codes can't  
#         be used on Vertical charts. ($bars & $dbars) 
#       * The heart of customization... a vals array.  If this  
#         array isn't created then html_graphs will use all  
#         default values for the chart.  Items that are customizable 
#         include font styles & colors, backgrounds, graphics,  
#         labels, cellspacing, cellpadding, borders, anotations 
#         and scaling factor. ($vals) 
# 
####################################################################### 
# 
#  Examples: 
# 
#  See http://www.pobox.com/~pdavis/programs/ 
# 
####################################################################### 
*/ 

/* 
####################################################################### 
# 
#  Function:  html_graph($names, $values, $bars, $vals[, $dvalues, $dbars])  
# 
#   Purpose:  Calls routines to initialize defaults, set up table 
#             print data, and close table. 
# 
# Arguments:  
#                   $names - Array of element names. 
#                  $values - Array of corresponding values for elements. 
#                    $bars - Array of corresponding graphic image names  
#                            or color codes (begining with a #) for elements. 
#                            Color codes can't be used on vertical charts. 
#                 $dvalues - Array of corresponding values for elements. 
#                            This set is required only in the double graph. 
#                   $dbars - Array of corresponding graphic image names  
#                            or color codes (begining with a #) for elements. 
#                            This set is required only in the double graph. 
# 
#                    $vals -  array("vlabel"=>"", 
#                                   "hlabel"=>"", 
#                                   "type"=>"", 
#                                   "cellpadding"=>"", 
#                                   "cellspacing"=>"", 
#                                   "border"=>"", 
#                                   "width"=>"", 
#                                   "background"=>"", 
#                                   "vfcolor"=>"", 
#                                   "hfcolor"=>"", 
#                                   "vbgcolor"=>"", 
#                                   "hbgcolor"=>"", 
#                                   "vfstyle"=>"", 
#                                   "hfstyle"=>"", 
#                                   "noshowvals"=>"", 
#                                   "scale"=>"", 
#                                   "namebgcolor"=>"", 
#                                   "valuebgcolor"=>"", 
#                                   "namefcolor"=>"", 
#                                   "valuefcolor"=>"", 
#                                   "namefstyle"=>"", 
#                                   "valuefstyle"=>"", 
#                                   "doublefcolor"=>"") 
# 
#             Where: 
# 
#                   vlabel - Vertical Label to apply 
#                            default is NUL 
#                   hlabel - Horizontal Label to apply 
#                            default is NUL 
#                     type - Type of graph  
#                            0 = horizontal 
#                            1 = vertical 
#                            2 = double horizontal 
#                            3 = double vertical  
#                            default is 0 
#              cellpadding - Padding for the overall table 
#                            default is 0 
#              cellspacing - Space for the overall table 
#                            default is 0 
#                   border - Border size for the overall table 
#                            default is 0 
#                    width - Width of the overall table 
#                            default is NUL 
#               background - Background image for the overall table 
#                            If this value exists then no BGCOLOR 
#                            codes will be added to table elements. 
#                            default is NUL 
#                  vfcolor - Vertical label font color 
#                            default is #000000 
#                  hfcolor - Horizontal label font color 
#                            default is #000000 
#                 vbgcolor - Vertical label background color 
#                            Not used if background is set 
#                            default is #FFFFFF 
#                 hbgcolor - Horizontal label background color 
#                            Not used if background is set 
#                            default is #FFFFFF 
#                  vfstyle - Vertical label font style 
#                            default is NUL  
#                  hfstyle - Horizontal label font style 
#                            default is NUL  
#               noshowvals - Don't show numeric value at end of graphic 
#                            Boolean value, default is FALSE 
#                    scale - Scale values by some number. 
#                            default is 1. 
#              namebgcolor - Color code for element name cells 
#                            Not used if background is set 
#                            default is "#000000" 
#             valuebgcolor - Color code for value cells 
#                            Not used if background is set 
#                            default is "#000000" 
#               namefcolor - Color code for font of name element 
#                            default is "#FFFFFF" 
#              valuefcolor - Color code for font of value element 
#                            default is "#000000" 
#               namefstyle - Style code for font of name element 
#                            default is NUL  
#              valuefstyle - Style code for font of value element 
#                            default is NUL  
#             doublefcolor - Color code for font of second element value 
#                            default is "#886666" 
# 
####################################################################### 
*/ 
function html_graph($names, $values, $bars, $vals, $dvalues=0, $dbars=0)  
   { 
    // Set the error level on entry and exit so as not to interfear 
    // with anyone elses error checking. 
    $er = error_reporting(1); 

    // Set the values that the user didn't 
    $vals = hv_graph_defaults($vals); 
    start_graph($vals, $names); 

    if ($vals["type"] == 0) 
       { 
        horizontal_graph($names, $values, $bars, $vals); 
       } 
    elseif ($vals["type"] == 1) 
       { 
        vertical_graph($names, $values, $bars, $vals); 
       } 
    elseif ($vals["type"] == 2) 
       { 
        double_horizontal_graph($names, $values, $bars, $vals, $dvalues, $dbars); 
       } 
    elseif ($vals["type"] == 3) 
       { 
        double_vertical_graph($names, $values, $bars, $vals, $dvalues, $dbars); 
       } 

    end_graph(); 

    // Set the error level back to where it was. 
    error_reporting($er);   
   } 

/* 
#######################################################################  
# 
#  Function:  html_graph_init() 
# 
#   Purpose:  Sets up the $vals array by initializing all values to  
#             null.  Used to avoid warnings from error_reporting being 
#             set high.  This routine only needs to be called if you  
#             are woried about using uninitialized variables. 
#            
#   Returns:  The initialized $vals array 
#  
#######################################################################  
*/ 
function html_graph_init() 
   { 
    $vals = array("vlabel"=>"", 
                  "hlabel"=>"", 
                  "type"=>"", 
                  "cellpadding"=>"", 
                  "cellspacing"=>"", 
                  "border"=>"", 
                  "width"=>"", 
                  "background"=>"", 
                  "vfcolor"=>"", 
                  "hfcolor"=>"", 
                  "vbgcolor"=>"", 
                  "hbgcolor"=>"", 
                  "vfstyle"=>"", 
                  "hfstyle"=>"", 
                  "noshowvals"=>"", 
                  "scale"=>"", 
                  "namebgcolor"=>"", 
                  "valuebgcolor"=>"", 
                  "namefcolor"=>"", 
                  "valuefcolor"=>"", 
                  "namefstyle"=>"", 
                  "valuefstyle"=>"", 
                  "doublefcolor"=>""); 

    return($vals); 
   } 
/* 
#######################################################################  
# 
#  Function:  start_graph($vals, $names) 
# 
#   Purpose:  Prints out the table header and graph labels. 
# 
#######################################################################  
*/ 
function start_graph($vals, $names) 
   { 
    print '<TABLE '; 
    print ' CELLPADDING="' . $vals["cellpadding"] . '"'; 
    print ' CELLSPACING="' . $vals["cellspacing"] . '"'; 
    print ' BORDER="' . $vals["border"] . '"'; 

    if ($vals["width"] != 0) { print ' WIDTH="' . $vals["width"] . '"'; } 
    if ($vals["background"]) { print ' BACKGROUND="' . $vals["background"] . '"'; } 

    print '>'; 

    if (($vals["vlabel"]) || ($vals["hlabel"])) 
       { 
        if (($vals["type"] == 0) || ($vals["type"] == 2 ))// horizontal chart 
           {  
            $rowspan = SizeOf($names) + 1;  
            $colspan = 3;  
           } 
        elseif ($vals["type"] == 1 || ($vals["type"] == 3 )) // vertical chart 
           { 
            $rowspan = 3; 
            $colspan = SizeOf($names) + 1;  
           } 

        print '<TR><TD ALIGN=CENTER VALIGN="CENTER" '; 

        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print 'BGCOLOR="' . $vals["hbgcolor"] . '"'; } 

        print ' COLSPAN="' . $colspan . '">'; 
        print '<FONT COLOR="' . $vals["hfcolor"] . '" STYLE="' . $vals["hfstyle"] . '">'; 
        print "<B>" . $vals["hlabel"] . "</B>"; 
        print '</FONT></TD></TR>'; 

        print '<TR><TD ALIGN="CENTER" VALIGN="CENTER" '; 

        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print 'BGCOLOR="' . $vals["vbgcolor"] . '"'; } 

        print ' ROWSPAN="' . $rowspan . '">'; 
        print '<FONT COLOR="' . $vals["vfcolor"] . '" STYLE="' . $vals["vfstyle"] . '">'; 
        print "<B>" . $vals["vlabel"] . "</B>"; 
        print '</FONT></TD>'; 
       } 
   } 

/* 
#######################################################################  
# 
#  Function:  end_graph() 
# 
#   Purpose:  Prints out the table footer. 
# 
#######################################################################  
*/ 
function end_graph() 
   { 
    print "</TABLE>"; 
   } 

/* 
#######################################################################  
# 
#  Function:  hv_graph_defaults($vals) 
# 
#   Purpose:  Sets the default values for the $vals array  
# 
#######################################################################  
*/ 
function hv_graph_defaults($vals) 
   { 
    if (! $vals["vfcolor"]) { $vals["vfcolor"]="#000000"; } 
    if (! $vals["hfcolor"]) { $vals["hfcolor"]="#000000"; } 
    if (! $vals["vbgcolor"]) { $vals["vbgcolor"]="#FFFFFF"; } 
    if (! $vals["hbgcolor"]) { $vals["hbgcolor"]="#FFFFFF"; } 
    if (! $vals["cellpadding"]) { $vals["cellpadding"]=0; } 
    if (! $vals["cellspacing"]) { $vals["cellspacing"]=0; } 
    if (! $vals["border"]) { $vals["border"]=0; } 
    if (! $vals["scale"]) { $vals["scale"]=.1; } 
    if (! $vals["namebgcolor"]) { $vals["namebgcolor"]="#FFFFFF"; } 
    if (! $vals["valuebgcolor"]) { $vals["valuebgcolor"]="#FFFFFF"; } 
    if (! $vals["namefcolor"]) { $vals["namefcolor"]="#000000"; } 
    if (! $vals["valuefcolor"]) { $vals["valuefcolor"]="#000000"; } 
    if (! $vals["doublefcolor"]) { $vals["doublefcolor"]="#886666"; } 

    return ($vals); 
   } 

/* 
#######################################################################  
# 
#  Function:  horizontal_graph($names, $values, $bars, $vals)  
# 
#   Purpose:  Prints out the actual data for the horizontal chart.  
# 
#######################################################################  
*/ 
function horizontal_graph($names, $values, $bars, $vals)  
   { 
    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  
?> 
       <TR> 
        <TD ALIGN=RIGHT  
<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["namebgcolor"] . '"'; } 
?> 
         > 
         <FONT SIZE="-1" COLOR="<? echo $vals["namefcolor"] ?>" STYLE="<? echo $vals["namefstyle"] ?>"> 
         <? echo $names[$i] ?> 
         </FONT> 
        </TD> 
        <TD  ALIGN=LEFT  
<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
?> 
         > 
<? 
        // Decide if the value in bar is a color code or image. 
        if (ereg("^#", $bars[$i])) 
           {  
?> 
            <TABLE ALIGN="LEFT" CELLPADDING=0 CELLSPACING=0  
             BGCOLOR="<? echo $bars[$i] ?>"  
             WIDTH="<? echo $values[$i] * $vals["scale"] ?>"> 
             <TR><TD>&nbsp</TD></TR> 
            </TABLE> 
<? 
            } 
         else 
            { 
             print '<IMG SRC="' . $bars[$i] . '"'; 
             print ' HEIGHT=10 WIDTH="' . $values[$i] * $vals["scale"] . '">'; 
            } 
        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="3" COLOR="' . $vals["valuefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $values[$i] . ")</FONT></I>"; 
           } 
?> 
        </TD>  
       </TR> 
<? 
       } // endfor 

   } // end horizontal_graph 

/* 
#######################################################################  
# 
#  Function:  vertical_graph($names, $values, $bars, $vals)  
# 
#   Purpose:  Prints out the actual data for the vertical chart.  
# 
#######################################################################  
*/ 
function vertical_graph($names, $values, $bars, $vals)  
   { 
    print "<TR>"; 

    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  

        print '<TD  ALIGN="CENTER" VALIGN="BOTTOM" '; 

        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
        print ">"; 

        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="-2" COLOR="' . $vals["valuefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $values[$i] . ")</FONT></I><BR>"; 
           } 
?> 

         <IMG SRC="<? echo $bars[$i] ?>" WIDTH=5 HEIGHT="<?  

        // Values of zero are displayed wrong because a image height of zero  
        // gives a strange behavior in Netscape. For this reason the height  
        // is set at 1 pixel if the value is zero. - Jan Diepens 
        if ($values[$i] != 0) 
           { 
            echo $values[$i] * $vals["scale"]; 
           }  
        else  
           {  
            echo "1"; 
           }  
?>"> 

         </TD>  
<? 
       } // endfor 

    print "</TR><TR>"; 

    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  
?> 
        <TD ALIGN="CENTER" VALIGN="TOP"  

<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["namebgcolor"] . '"'; } 
?> 
         > 
         <FONT SIZE="-1" COLOR="<? echo $vals["namefcolor"] ?>" STYLE="<? echo $vals["namefstyle"] ?>"> 
         <? echo $names[$i] ?> 
         </FONT> 
        </TD> 
<? 
       } // endfor 

   } // end vertical_graph  

/* 
#######################################################################  
# 
#  Function:  double_horizontal_graph($names, $values, $bars,  
#                                     $vals, $dvalues, $dbars)  
# 
#   Purpose:  Prints out the actual data for the double horizontal chart.  
# 
#######################################################################  
*/ 
function double_horizontal_graph($names, $values, $bars, $vals, $dvalues, $dbars)  
   { 
    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  
?> 
       <TR> 
        <TD ALIGN=RIGHT  
<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["namebgcolor"] . '"'; } 
?> 
         > 
         <FONT SIZE="-1" COLOR="<? echo $vals["namefcolor"] ?>" STYLE="<? echo $vals["namefstyle"] ?>"> 
         <? echo $names[$i] ?> 
         </FONT> 
        </TD> 
        <TD  ALIGN=LEFT  
<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
?> 
         > 
         <TABLE ALIGN="LEFT" CELLPADDING=0 CELLSPACING=0 WIDTH="<? echo $dvalues[$i] * $vals["scale"] ?>"> 
          <TR><TD  
<? 
        // Set background to a color if it starts with # or 
        // an image otherwise. 
        if (ereg("^#", $dbars[$i])) { print 'BGCOLOR="' . $dbars[$i] . '">'; } 
        else { print 'BACKGROUND="' . $dbars[$i] . '">'; } 
?> 
           <NOWRAP> 
<? 
        // Decide if the value in bar is a color code or image. 
        if (ereg("^#", $bars[$i])) 
           {  
?> 
            <TABLE ALIGN="LEFT" CELLPADDING=0 CELLSPACING=0  
             BGCOLOR="<? echo $bars[$i] ?>"  
             WIDTH="<? echo $values[$i] * $vals["scale"] ?>"> 
             <TR><TD>&nbsp</TD></TR> 
            </TABLE> 
<? 
            } 
         else 
            { 
             print '<IMG SRC="' . $bars[$i] . '"'; 
             print ' HEIGHT=10 WIDTH="' . $values[$i] * $vals["scale"] . '">'; 
            }           

        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="-3" COLOR="' . $vals["valuefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $values[$i] . ")</FONT></I>"; 
           } 
?> 
           </NOWRAP> 
          </TD></TR> 
         </TABLE> 
<? 
        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="-3" COLOR="' . $vals["doublefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $dvalues[$i] . ")</FONT></I>"; 
           } 
?> 
        </TD>  
       </TR> 
<? 
       } // endfor 

   } // end double_horizontal_graph 

/* 
#######################################################################  
# 
#  Function:  double_vertical_graph($names, $values, $bars, $vals, $dvalues, $dbars)  
# 
#   Purpose:  Prints out the actual data for the double vertical chart.  
# 
#    Author: Jan Diepens 
# 
#######################################################################  
*/ 
function double_vertical_graph($names, $values, $bars, $vals, $dvalues, $dbars)  
   { 
   // print "<TR>"; 

    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  

        print '<TD  ALIGN="CENTER" VALIGN="BOTTOM" '; 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
        print ">"; 

        print '<TABLE><TR><TD ALIGN="CENTER" VALIGN="BOTTOM" '; 

        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
        print ">"; 

        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="-2" COLOR="' . $vals["valuefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $values[$i] . ")</FONT></I><BR>"; 
           } 
?> 

         <IMG SRC="<? echo $bars[$i] ?>" WIDTH=10 HEIGHT="<? if ($values[$i]!=0){ 
                echo $values[$i] * $vals["scale"]; 
                } else { echo "1";} ?>"> 
         </TD><TD ALIGN="CENTER" VALIGN="BOTTOM" 
<? 
         // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["valuebgcolor"] . '"'; } 
        print ">"; 

        if (! $vals["noshowvals"]) 
           { 
            print '<I><FONT SIZE="-2" COLOR="' . $vals["doublefcolor"] . '" '; 
            print ' STYLE="' . $vals["valuefstyle"] . '">(';  
            print $dvalues[$i] . ")</FONT></I><BR>"; 
           } 
?> 

         <IMG SRC="<? echo $dbars[$i] ?>" WIDTH=10 HEIGHT="<? if ($dvalues[$i]!=0){ 
                echo $dvalues[$i] * $vals["scale"]; 
                } else { echo "1";} ?>"> 
         </TD></TR></TABLE> 
         </TD> 
<? 
       } // endfor 

    print "</TR><TR>"; 

    for( $i=0;$i<SizeOf($values);$i++ ) 
       {  
?> 
        <TD ALIGN="CENTER" VALIGN="TOP"  

<? 
        // If a background was choosen don't print cell BGCOLOR 
        if (! $vals["background"]) { print ' BGCOLOR="' . $vals["namebgcolor"] . '"'; } 
?> 
         > 
         <FONT SIZE="-1" COLOR="<? echo $vals["namefcolor"] ?>" STYLE="<? echo $vals["namefstyle"] ?>"> 
         <? echo $names[$i] ?> 
         </FONT> 
        </TD> 
<? 
       } // endfor 

   } // end double_vertical_graph 
    //    print "</TR>"; 

?> 
