<?php session_start();
   header("Cache-control: private");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/schedule.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<link rel="SHORTCUT ICON" href="http://www.aysosection1.org/favicon.ico">
<link rel="stylesheet" type="text/css" href="/css/refsched.css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>AYSO Section 1</title>
<meta name="description" content="Welcome to the Home Page of Section 1 of the American Youth Soccer Organizition ( AYSO ) serving Los Angeles, San Bernardino, and Riverside Counties in Southern California.">
<meta name="keywords" content="aysosection1, ayso section 1, ayso, section 1, section one, soccer, youth soccer, los angeles, riverside,san bernardino, american youth soccer organization">

</head>

<body>

<table width="98%"  class="maincontent">
<!-- InstanceBeginEditable name="mainContent" -->
  <tr>
    <td align="center" class="contentarea">
     
<?php
      $authed = $_SESSION['authed'];
      $rep = $_SESSION['unit'];
      $locked = isset($_SESSION['locked']) ? $_SESSION['locked'] : null;
      $schedule_file = $_SESSION['eventfile'];

      if ( $authed ) {
           $any_games = 0;
           $fp = fopen( $schedule_file, "r" );
           while ( $line = fgets( $fp, 1024 ) ) {
              if ( substr( $line, 0, 1 ) != '#' ) {
                 $record = explode( ',', trim($line) );
                 if ( $record[8] == $rep || $rep == 'Section 1') {
                    if ( !$any_games ) {
                       if ( $rep != 'Section 1') { echo "<center><h2>You are currently scheduled for the following games</h2></center>\n"; }
                       echo "      <form name=\"addref\" method=\"post\" action=\"sched_editref.php\">\n";
                       echo "      <table width=\"100%\">\n";
                       echo "        <tr align=\"center\">";
                       echo "            <td>Game<br>No.</td>";
                       echo "            <td>Day</td>";
                       echo "            <td>Time</td>";
                       echo "            <td>Location</td>";
                       echo "            <td>Div</td>";
                       echo "            <td>Area</td>";
                       echo "            <td>CR</td>";
                       echo "            <td>AR1</td>";
                       echo "            <td>AR2</td>";
                       echo "            <td>4thO</td>";
                       echo "            <td>&nbsp;</td>";
                       echo "            </tr>\n";
                       $any_games = 1;
                    }
                    if ( !$record[8] && $rep == 'Section 1' ) {
                       echo "            <tr align=\"center\" bgcolor=\"#00FFFF\">";
                    }
                    else {
                       echo "            <tr align=\"center\" bgcolor=\"#00FF88\">";
                    }
                    echo "            <td>$record[0]</td>";
                    echo "            <td>$record[2]<br>$record[1]</td>";
                    echo "            <td>$record[4]</td>";
                    echo "            <td>$record[3]</td>";
                    echo "            <td>$record[5]</td>";
                    echo "            <td>$record[8]</td>";
                    echo "            <td>$record[9]</td>";
                    echo "            <td>$record[10]</td>";
                    echo "            <td>$record[11]</td>";
                    echo "            <td>$record[12]</td>";
                    if ( $record[8] ) {
                       echo "            <td><input type=\"submit\" name=\"game$record[0]\" value=\"Edit Refs\"></td>";
                    }
                    else {
                       echo "            <td>&nbsp;</td>\n";
                    }
                    echo "            </tr>\n";
                 }
              }
           }
           if ( $any_games ) {
             echo "      </table>\n";
             echo "      </form>\n";
           }
           fclose( $fp );
           if (!$any_games ) {
              echo "<center><h2>You do not currently have any games scheduled.</h2>\n";
              echo "  You should go to the <a href=\"sched_sched.php\">Schedule Page</a></h2></center>";
           }
      }
      elseif ( !$authed ) {
         echo "<center><h2>You need to <a href=\"index.htm\">logon</a> first.</h2></center>";
      }
      else {
         echo "<center><h1>Something is not right</h1></center>";
      }

      echo "<h3 align=\"center\"><a href=\"sched_greet.php\">Return to main screen</a>&nbsp;-&nbsp;\n";
      if ( $rep == 'Section 1' ) {
         echo "<a href=\"sched_master.php\">Return to schedule</a>&nbsp;-&nbsp;\n";
      }
      else {
         echo "<a href=\"sched_sched.php\">Return to schedule</a>&nbsp;-&nbsp;\n";
      }
      echo "<a href=\"sched_end.php\">Logoff</a></h3>\n";
?>
</tr>
  <!-- InstanceEndEditable -->
</table>
<p align="center"><span class="foottext"><a href="index.htm">Home</a> - <a href="section_staff.htm">Contact&nbsp;Us</a>  - <a href="siteindex.htm" class="flinks">Site&nbsp;Index<br>
</a>Corrections or additions to this web page can be sent to: <a href="mailto:webmaster@aysosection1.org">webmaster@aysosection1.org</a><br>
&copy;2005  Section one, American Youth Soccer Organization <br />
AYSO name and AYSO initials, Logos and graphics copyright by the <a href="http://www.soccer.org" class="flinks">American Youth Soccer Organization</a> and used with permission.</span></p>
</body>
<!-- InstanceEnd --></html>
