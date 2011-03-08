<?php   // VIEW JOBS SCRIPT
// This is a very robust script, so I have divided it into seperate
// files.  It also makes if much easier to debug :^)
?>
<?PHP

include("includes/connect.inc.php");   //connect to the database
if ($s_view_department_tickets==1) {   //Start if s_view_departmts is true
  if ($s_pref_viewall == "1" || sizeof($departments) == 1) {
    for ($i=0; $i < sizeof($departments); $i++) {
      $lstChooseCompany[$i] = $departments[$i];
    }
  }
  if (!isset($lstChooseCompany) && !isset($t_id)) {  
    include("scripts/vj_choosecompany.scp.php");
  }
  elseif (isset($lstChooseCompany)
    && isset($t_id)
    && !isset($cmdAddEvent)
    && !isset($cmdCloseTicket)
    && !isset($cmdAddPart)
    && !isset($cmdPrintTicket)
    && !isset($cmdSaveChanges)
    && !isset($cmdDeleteTicket)) {
    include("scripts/vj_showticket.scp.php");
  }
  elseif (isset($cmdAddEvent)) {
    include("scripts/vj_addevent.scp.php");
    include("scripts/vj_showticket.scp.php");
  }
  elseif (isset($cmdSaveChanges)) {
    include("scripts/vj_savechanges.scp.php");
    include("scripts/vj_showticket.scp.php");
  }
  elseif (isset($cmdCloseTicket)) {
    include("scripts/vj_closeticket.scp.php");
    include("scripts/vj_printticket.scp.php");
  }
  elseif (isset($cmdAddPart)) {
    include("scripts/vj_addpart.scp.php");
    include("scripts/vj_printticket.scp.php");
  }
  elseif (isset($cmdPrintTicket)) {
    include("scripts/vj_printticket.scp.php");
  }
  elseif (isset($lstChooseCompany) && !isset($t_id)) {
    include("scripts/vj_viewtickets.scp.php");
  }
  elseif (isset($cmdDeleteTicket) && isset($s_delete_tickets)) {
    include("scripts/deleteticket.scp.php");
  }
}
?>
