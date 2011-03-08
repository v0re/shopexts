<?php //ENGLISH language file
/*

   To whom it may concern:

   The only language that I really know good is English.  I have made this
   file available so that other language speaking people can modify these
   variables so that PHP Helpdesk will work for other languages.  If you can
   help translate the following variables into another language, then please
   email me at joe@networkpenguin.com.  I will then let you know if someone
   else has already done this translation.  If they have not, then I will ask
   for your help in the translation.  I will need you to then translate these
   variables into the other language and send me an email with the document
   attached.  I will then add the language to the project so that other can
   benefit from your translations.  It will then appear in the project as:

     languages/yourlanguage.lang.php

   Thank You in advance,

   Joe Hoot

*/


//GENERAL
$l_version = "Version";
#$l_status = "Status: ";
$l_departments = "Abteilungen: ";
$l_companies = "Firmen: ";
$l_laston = "last on: ";
$l_needhelp = "Zur Online-Hilfe?";
$l_clickhere = "Hier klicken";
$l_visitphphelpdesk = "PHP Helpdesk Hompage unter";
$l_ifyouneedhelp = "Wenn Sie noch Fragen haben, schreiben Sie uns doch eine Email an";
$l_addjob = "Neuer Auftrag";
$l_viewjobs = "Auftr�ge anzeigen";
$l_adduser = "Benutzer hinzuf�gen";
$l_modifyuser = "Benutzer �ndern";
$l_deleteuser = "Benutzer l�schen";
$l_addcategory = "Kategorie hinzuf�gen";
$l_deletecategory = "Kategorie l�schen";
$l_adddepartment = "Abteilung hinzuf�gen";
$l_addcompany = "Firma hinzuf�gen";
$l_deletedepartment = "Abteilung l�schen";
$l_deletecompany = "Firma l�schen";
$l_addparts = "Ersatzteil hinzuf�gen";
$l_reports = "Berichte";
$l_preferences = "Einstellungen";
$l_logout = "Abmelden";


// LOGIN
$l_whatdepartmentareyouin = "Zu welcher Abteilung geh�ren Sie?";
$l_whatcompanyareyouin = "Zu welcher Firma geh�ren Sie?";
$l_login = "Anmelden";
$l_username = "Benutzername";
$l_password = "Passwort";
$l_enter = "Betrete";
$l_welcometothe = "Willkommen zum ".$g_title;
$l_pleasechoose = "Bitte w�hlen Sie einen Eintrag aus der obigen Liste.";
$l_usernameorpasswordareincorrect = "Benutzername oder Passwort sind falsch!";
$l_cannotconnecttodatabase = "Keine Verbindung zur Datenbank m�glich!";

//ADD JOBS
$l_choosecompany = "Firma w�hlen";
$l_choosedepartment = "Abteilung w�hlen";
$l_continue = "weiter";
$l_addjobform = "Neues Auftragsformular";
$l_itisimportant = "Bitte geben Sie Ihre Daten ein, damit wir uns bei R�ckfragen mit Ihnen in Verbindung setzen k�nnen.";
$l_firstname = "Vorname:";
$l_lastname = "Nachname:";
$l_telephonenumberextension = "Telefonummer / Durchwahl:";
$l_emailaddress = "Emailadresse:";
$l_shortsummary = "Kurzbeschreibung:";
$l_detail = "Details:";
$l_location = "Standort:";
$l_priority = "Priorit�t:";
$l_low = "Niedrig";
$l_normal = "Normal";
$l_high = "Hoch";
$l_urgent = "Dringend!";
#$l_addjob = "Add Job";
$l_clearform = "Formular leeren";
$l_areceiptwassent = "Eine Best�tigung wurde verschickt an";
$l_yourservicerequest = "Ihre Anfrage wurde erfasst!";
$l_atechnician = "Wir werden Ihren Auftrag so schnell wie m�glich bearbeiten.";
$l_eventwasadded = "Bearbeitungsschritt wurde hinzugef�gt.";
$l_error = "FEHLER:";
$l_jobnotadded = "Ihr Auftrag wurde <B>nicht</B> in die Datenbank aufgenommen!";
$l_resolution = "<font color=red>L�SUNG:</font>";
$l_youmustenteratleast = "<font color=red>Sie m�ssen mindestens folgendes angeben:</font>";
// l_eventwayadded change to was
$l_wehavereceivedyourrequest = "Wir haben Ihren Auftrag erhalten !";
$l_belowisacopyoftheticket = "Es folgt eine Kopie des von Ihnen eingegebenen Auftrags.";
$l_ithasbeenadded = "Er wurde in die Warteschlange aufgenommen und wird in K�rze bearbeitet.";
$l_senton = "Verschickt am"; // As in Sent on May 5, 2001 usering username sam smith
$l_usingusername = "von Benutzername";
$l_from = "Von:"; //As in From Sam@smith.com
$l_mailwassent = "Mail wurde verschickt an";
$l_mailto = "Mail an:";
$l_pleasechoosecompanytoviewtickets = "Bitte w�hlen Sie alle Firmen, f�r die die Tickets angezeigt werden sollen.";




//VIEW JOBS
$l_viewalltickets = "Alle Tickets anzeigen";
$l_viewonlyopenedtickets = "Nur offene Tickets anzeigen";
$l_ticketid = "Ticket Nr";
$l_summary = "Zusammenfassung";
$l_category = "Kategorie";
$l_status = "Status";
$l_department = "Abteilung";
$l_company = "Firma";
$l_assignedto = "Zugeordnet an";
$l_priority = "Priorit�t";
$l_dateopened = "Er�ffnet am";
$l_viewjobdetailsform = "Zeige Auftragsformular";
$l_computerid = "Computer Nr:";
$l_openedby = "Er�ffnet von:";
$l_totaltime = "Gesamtzeit:";
$l_priceforparts = "Preise f�r Ersatzteile:";
$l_closeticket = "Ticket schliessen";
$l_showprintableversion = "Druckbare Fassung anzeigen";
$l_time = "Zeit";
$l_event = "Bearbeitungsschritt";
$l_duration = "Dauer";
$l_reassignedto = "Bearbeiter";
$l_addevent = "Bearbeitungsschritt hinzuf�gen";
$l_selectpartsfromthislist = "Ersatzteile aus dieser Liste ausw�hlen";
$l_notassigned = "nicht zugeordnet";
$l_ticketregisterd = "Ticket registriert";
$l_reassignedticket = "Ticket neu zugeordnet / Aufwand erh�ht";
$l_added = "hinzugef�gt";
$l_manager = "Manager:";
$l_technician = "Techniker";
$l_hours = "Stunden";
$l_parts = "Ersatzteile:";
$l_unitprice = "St�ck Preis";
$l_extendedprice = "Gesamt Preis";
$l_phonenumber = "Telefonnummer:";
$l_networkpropertiescorrect = "Netzwerk Einstellungen i.O.:";
$l_antivirus = "Antivirus - Virenliste aktualisiert:";
$l_runantivirus = "Antivirus Programm ausgef�hrt";
$l_removenonworkrelatedprograms = "Nicht-T�tigskeitsrelevante Programme entfernt:";
$l_runscandisk = "Scan Disk ausgef�hrt:";
$l_authorizingsignature = "Genehmigt (Unterschrift):";
$l_quantity = "Menge:";
$l_partid = "Ersatzteil ID:";
$l_partdescription = "Ersatzteil Beschreibung";



//ADDUSER
$l_userinformation = "Benutzer Informationen - 1 von 3 ";
$l_verifypassword = "Passwort (Wiederholung):";
$l_next = "weiter";
$l_selectpermissions = "Berechtigungen bearbeiten - 2 von 3";
$l_privileges = "Privilegien";
$l_grant = "erlaubte";
$l_dontgrant = "nicht erlaubte";
$l_registernewtickets = "Neue Tickets er�ffnen:";
$l_authorizetickets = "Tickets authorisieren:";
$l_assigntickets = "Tickets zuweisen:";
$l_updatetickets = "Tickets aktualisieren:";
$l_deletetickets = "Tickets l�schen:";
$l_openclosedtickets = "Geschlossene Tickets wieder �ffnen:";
$l_viewunauthorizedtickets = "Nicht-Authorisierte Tickets anzeigen:";
$l_viewdepartmenttickets = "Abteilungs-Tickets anzeigen:";
$l_addcategories = "Kategorien hinzuf�gen:";
$l_deletecategories = "Kategorien l�schen:";
$l_adddepartments = "Abteilungen hinzuf�gen:";
$l_deletedepartments = "Abteilungen l�schen:";
$l_manageusers = "Benutzer verwalten:";
$l_manageparts = "Ersatzteile verwalten:";
$l_runreports = "Berichte abrufen:";
$l_isamanager = "Ist ein Manager:";
$l_userinfoadded = "Benutzer Information wurde hinzugef�gt.";
$l_wassuccessfullyadded = "Wurde erfolgreich in der Datenbank erstellt !";
$l_pleasechoosethedepts = "W�hlen Sie die Abteilungen oder Firmen aus, f�r die dieser Benutzer berechtigt ist:";
$l_selectdepartments = "Auswahl Abteilungen - 3 von 3";
$l_alldepartments = "Alle <BR> Abteilungen";
$l_useravailabledepartments = "F�r den Benutzer <BR> verf�gbare<BR> Abteilungen";
$l_userthisbuttontoadd = "Diesen Button zum <B><I>hinzuf�gen</I></B> der ausgew�hlten Abteilungen zu den verf�gbaren Abteilungen des Benutzers auf der rechten Seite benutzen.";
$l_userthisbuttontodelete = "Diesen Button zum <B><I>l�schen</I></B> der ausgew�hlten Abteilungen aus den verf�gbaren Abteilungen des Benutzers benutzen.";
$l_add = "hinzuf�gen";
$l_delete = "l�schen";
$l_clickfinishmessage = "Wenn die Aufstellung der <I>f�r den Benutzer verf�gbaren Abteilungen</I> vollst�ndig ist, klicken Sie auf <B><I>Fertigstellen</B></I>.";
$l_finish = "Fertigstellen";
$l_usersuccessfullycreated = "wurde erfolgreich erstellt !";
$l_isalreadyauser = "ist bereits in der Datenbank vorhanden !<BR>\nBitte w�hlen Sie einen anderen Benutzernamen und versuchen Sie es noch einmal.\n";
$l_atleastusername = "Mindestens Benutzername, Passwort und die Wiederholung des Passworts m�ssen angegeben werden.";
$l_Username = "Benutzername:";
$l_Password = "Passwort:";

//MODIFY USER
$l_selectusertomodify = "Benutzername zum �ndern ausw�hlen";
$l_selectuser = "Benutzername:";
$l_selectthisuser = "Diesen Benutzer �ndern";
$l_modifyuserinfo = "�ndern der Benutzer-Informationen - 1 von 3";
$l_userhasbeenupdated = "wurde erfolgreich aktualisiert !";
$l_userhasnotbeenupdated = "wurde <b>nicht</b> aktualisiert!";

//DELETE USER
$l_selectusertodelete = "Benutzername zum L�schen ausw�hlen";
#$l_deleteuser = "Delete User";
$l_areyousureyouwanttodelete = "Sind Sie sicher, dass Sie diesen Benutzer l�schen wollen?";
$l_yes = "JA";
$l_no = "NEIN";
$l_userhasbeendeleted = "wurde gel�scht";

//ADD CATEGORY
$l_addcategoryform = "Kategorie hinzuf�gen";
$l_choosethisdepartment = "Verwende diese Abteilung";
$l_choosethiscompany = "Verwende diese Firma";
$l_categoryname = "Name der Kategorie:";
$l_currentcategories = "vorhandene Kategorien:";
$l_addthiscategory = "Diese Kategorie zuordnen zu";
$l_wasadded = "wurde erfolgreich hinzugef�gt !";
$l_wasnotadded = "wurde <b>nicht</b> hinzugef�gt !";

//DELETE CATEGORY
$l_deletecategoryform = "Kategorie l�schen";
$l_deletethiscategory = "l�sche diese Kategorie aus";
$l_wasnotdeleted = "wurde <b>nicht</b> gel�scht !";

//ADD DEPARTMENT/COMPANY
$l_adddepartmentform = "Abteilung hinzuf�gen";
$l_addcompanyform = "Firma hinzuf�gen";
$l_newdepartmentname = "Name der Abteilung:";
$l_newcompanyname = "Name der Firma:";
$l_currentdepartments = "vorhandene Abteilungen:";
$l_currentcompanies = "vorhandene Firmen:";
#$l_add = "Add";

//DELETE DEPARTMENT/COMPANY
$l_deletedepartmentform = "L�sche Abteilung";
$l_deletecompanyform = "L�sche Firma";
$l_selectdepartment = "Abteilung ausw�hlen:";
$l_selectcompany = "Firma ausw�hlen:";
#$l_delete = "L�schen";

//ADD PARTS
$l_partnumber = "Ersatzteilnummer";
$l_description = "Beschreibung";
$l_price = "Preis";
$l_stockquantity = "Lagerbestand";
$l_mustbeunique = "muss einmalig sein";
$l_imsure = "ich bin sicher";
$l_currentquantity = "derzeitiger Bestand:";
$l_addthisquantity = "dies zum Bestand hinzuf�gen:";
$l_cantfindpartid = "kann Ersatzteil Nr nicht finden";
$l_save = "speichern";
$l_parthasalreadybeenused = "Ersatzteilnummer $partfound wird bereits verwendet."; //$partfound is the part number
$l_pleasechooseadifferentpart = "Bitte w�hlen Sie eine andere Ersatzteilnummer.";
$l_partwasnotdeleted = "Ersatzteil $p_id wurde aus der Datenbank aus folgenden Gr�nden nicht entfernt:"; //p_id is a part number
$l_pleasecheckimsure = "Bitte machen Sie den Haken in die  \"ich bin sicher\" Checkbox unter dem L�schen-Button um das Ersatzteil zu l�schen.";

//REPORTS
$l_selectthedepartment = "W�hlen Sie die Abteilung, f�r die Sie einen Bericht w�nschen.";
$l_selectthecompany = "W�hlen Sie die Firma, f�r die Sie einen Bericht w�nschen.";
$l_inputthebeginningdate = "Geben Sie das Anfangs- und Enddatum in folgendem Format ein: Jahr-Monat-Tag (z.B. 2001-02-23).";
$l_runthereport = "Bericht ausgeben.";
$l_startdate = "Anfangsdatum:";
$l_enddate = "Enddatum:";
$l_generatethisreport = "Erstelle diesen Bericht";
$l_reportgenerated = "Bericht erstellt";
$l_totalservicecalls = "Gesamt Service Anfragen:";
$l_minresponsetime = "Min Reaktionszeit:";
$l_maxresponsetime = "Max Reaktionszeit:";
$l_avgresponsetime = "Durchschn. Reaktionszeit:";
$l_minresolutiontime = "Min Bearbeitungszeit:";
$l_maxresolutiontime = "Max Bearbeitungszeit:";
$l_avgresolutiontime = "Durchschn. Bearbeitungszeit:";
$l_categories = "Kategorien";
$l_valuesareintickets = "(Werte sind in den Tickets)";
$l_computeridsandassociated = "Computer Nr und zugeh�rige Tickets";

//PREFERENCES
$l_userpreferences = "Benutzereinstellungen";
$l_showalltickets = "Alle Tickets anzeigen:";
$l_viewjobsfirst = "Auftr�ge auf der Startseite anzeigen:";
$l_savechanges = "�nderungen speichern";

?>
