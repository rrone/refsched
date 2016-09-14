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

     
      <div>
        <div align="center">
          <h1>Section 1 Referee Scheduling</h1>
        </div>
      </div>

<?php
   $authed = $_SESSION['authed'];
   $rep = $_SESSION['unit'];
   $schedule_file = $_SESSION['eventfile'];
   if ( count( $_GET ) && array_key_exists( 'group', $_GET ) ) {
      $showgroup = $_GET[ 'group' ];
   }
   $locked = 0;
   $thiscolor = '#00FFFF';
   $othercolor = '#FFFacd';
   $allatlimit = 1;
   $oneatlimit = 0;
   $showavailable = 1;
   $a_init = substr( $rep, -1 );
   if ( $authed && $rep != 'Section 1') {
      if ( file_exists( "refdata/limit" ) ) {
         $fp = fopen( "refdata/limit", "r" );
         while ( $line = fgets( $fp, 1024 ) ) {
            $record = explode( ',', $line );
            $limit_list[ $record[0] ] = $record[1];
            $used_list[ $record[0] ] = 0;
            $assigned_list[ $record[0] ] = 0;
         }
         fclose( $fp );
         if ( !count( $limit_list ) ) { $limit_list[ 'none' ] = 1; }
      }
      else { $limit_list[ 'none' ] = 1; }
      $no_assigned = 0;
      $scheddata = fopen( $schedule_file, "r");
      $sched_no = fgets( $scheddata, 1024 );
      $sched_title = fgets( $scheddata, 1024 );
      $page_title = substr( $sched_title, 1);
      $kount = 0;
      $kant = 0;
      while ( $line = fgetcsv( $scheddata, 1024 ) ) {
         if ( strtoupper( trim( $line[ 0 ] ) ) == '#LOCKED' ) { 
            $locked = 1;
            $_SESSION['locked'] = 1;
         }
         elseif (substr( $line, 0, 1 ) != '#' ) {
            $game_no[ $kount ] = $line[ 0 ];
            $date[ $kount ] = $line[ 1 ];
            $day[ $kount ] = $line[ 2 ];
            $field[ $kount ] = $line[ 3 ];
            $time[ $kount ] = $line[ 4 ];
            $div[ $kount ] = $line[ 5 ];
            $home[ $kount ] = $line[ 6 ];
            $visitor[ $kount ] = $line[ 7 ];
            $ref_team[ $kount ] = $line[ 8 ];
            if ( $ref_team[ $kount ] == $rep ) { 
               $no_assigned++;
               $assigned_list[ substr( $line[5], 0, 3 ) ]++;
            }
            $used_list[ substr( $line[5], 0, 3 ) ] = 1;
            $cr[ $kount ] = $line[ 9 ];
            $ar1[ $kount ] = $line[ 10 ];
            $ar2[ $kount++ ] = $line[ 11 ];
         }
      }
      fclose ( $scheddata );
      $free_board = $limit - $no_assigned;
      echo "<h2 align=\"center\">$page_title</h2>\n";
      if ( $locked && array_key_exists( 'none', $limit_list ) ) { 
         echo "<center><h3><font color=\"#CC0000\">The schedule has been locked.<br>You may sign up for games but not unassign yourself.</font></h3></center>\n"; 
         $allatlimit = 0;
      }
      elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned < $limit_list[ 'all' ] ) { 
         echo "<center><h3><font color=\"#CC0000\">The schedule has been locked.<br>You may sign up for games but not unassign yourself.</font></h3></center>\n"; 
         $allatlimit = 0;
      }
      elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned == $limit_list[ 'all' ] ) { 
         echo "<center><h3><font color=\"#CC0000\">The schedule has been locked and you are at your game limit.<br>\nYou will not be able to unassign yourself from games to sign up for others.<br>\nThe submit button on this page has been disabled and available games are not shown.<br>\nYou probably want to <a href=\"sched_greet.php\">Return to the Main Page</a> or <a href=\"sched_end.php\">Log Off</a></font></h3></center>\n";
         $showavailable = 0;
      }
      elseif ( $locked && array_key_exists( 'all', $limit_list ) && $no_assigned > $limit_list[ 'all' ] ) { 
         echo "<center><h3><font color=\"#CC0000\">The schedule has been locked and you are above your game limit.<br>\nThe extra games were probably assigned by the Section staff.<br>\nYou will not be able to unassign yourself from games to sign up for others.<br>\nThe Submit button has been disabled and available games are not shown.<br>\nYou probably want to <a href=\"sched_greet.php\">Return to the Main Page</a> or <a href=\"sched_end.php\">Log Off</a></font></h3></center>\n";
         $showavailable = 0; 
      }
      elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned < $limit_list['all'] ) { 
         $tmplimit = $limit_list['all'];
         echo "<center><h3>You are currently assigned to <font color=\"#CC00CC\">$no_assigned</font> of your <font color=\"#CC00CC\">$tmplimit</font> games.</h3></center>\n"; 
      }
      elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned == $limit_list['all'] ) { echo "<center><h3><font color=\"#CC0000\">You are at your game limit.<br>You will have to unassign yourself from games to sign up for others.</font></h3></center>\n"; }
      elseif ( !$locked && array_key_exists( 'all', $limit_list ) && $no_assigned > $limit_list['all'] ) { echo "<center><h3><font color=\"#CC0000\">You are above your game limit.<br>\nThe extra games were probably assigned by the Section staff.<br>\nIf you continue from here you will not be able to keep all the games you are signed up for and may lose some of the games you already have.<br>\nIf you want to keep these games and remain over the game limit it is recommended that you do not hit submit but do something else instead.<br>\n<a href=\"sched_greet.php\">Return to the Main Page</a></font></h3></center>\n"; }
      elseif ( $locked && count( $limit_list ) ) {
         echo "<center><h3><font color=\"#CC0000\">The system is locked.<br>You can add games to divisions that are below the limit but not unassign your Area from games.</font><br><br>\n";
            foreach ( $limit_list as $k => $v ) {
               $tempassign = $assigned_list[$k];
               if ( $used_list[ $k ] ) { 
                  echo "For $k you are assigned to <font color=\"#CC00CC\">$tempassign</font> with a limit of <font color=\"#CC00CC\">$v</font> games.<br>\n"; 
                  if ( $assigned_list[$k] < $limit_list[$k] ) { $allatlimit = 0;}
               }
            }
         if ( $allatlimit ) { 
            echo "<br><font color=\"#CC0000\">All of your divisions are at or above their limits.<br>Because the system is locked, games can not be unassigned to select new ones.<br>No changes are possible: Available games are not shown and the Submit button has been disabled.</font>\n";
            $showavailable = 0;
         } 
         echo "</h3></center>\n";
      }
      elseif ( !$locked && count( $limit_list ) ) {
         echo "<center><h3>\n";
         foreach ( $limit_list as $k => $v ) {
            $tempassign = $assigned_list[$k];
            if ( $used_list[ $k ] ) { 
               echo "For $k you are assigned to <font color=\"#CC00CC\">$tempassign</font> with a limit of <font color=\"#CC00CC\">$v</font> games.<br>\n"; 
               if ( $assigned_list[$k] >= $limit_list[$k] ) { $oneatlimit = 1;}
            }
         }
         if ( $oneatlimit ) { 
            echo "<br><font color=\"#CC0000\">One or more of your divisions are at or above their limits.<br>You will need to unassign games in that division before you can select additional games.</font>\n";
         } 
         echo "</h3></center>\n";
      }

      echo "<form name=\"form1\" method=\"post\" action=\"sched_assign.php\">\n";
      echo "  <div align=\"left\">";
      echo "    <h3>Available games - Color change indicates different start times.</h3>\n";
      echo "      <table>\n";
      echo "        <tr align=\"center\">";
      echo "            <td>Game No.</td>";
      echo "            <td>Assigned</td>";
      echo "		<td>Day</td>";
      echo "		<td>Time</td>";
      echo "		<td>Location</td>";
      echo "    	<td>Div</td>";
      echo "		<td>Home</td>";
      echo "	        <td>Away</td>";
      echo "		<td>Referee<br>Team</td>";
      echo "		</tr>";

      for ( $kant=0; $kant < $kount; $kant++ ) {
      if ( ( $showgroup && $showgroup == substr( $div[$kant], 0, 3 ) ) || !$showgroup ) {
         if ( substr( $game_no[$kant], 0, 1 ) != "#" && $a_init != substr( $home[$kant], 0, 1) && $a_init != substr( $visitor[$kant], 0, 1) && !$ref_team[$kant] && $showavailable ) {

            if ( !$testtime ) { $testtime = $time[$kant]; }
            elseif ( $testtime != $time[$kant] ) {
               $testtime = $time[$kant];
               $tempcolor = $thiscolor;
               $thiscolor = $othercolor;
               $othercolor = $tempcolor;
            }
      echo "		<tr align=\"center\" bgcolor=\"$thiscolor\">";
      echo "		<td>$game_no[$kant]</td>";
      echo "		<td><input type=\"checkbox\" name=\"game$game_no[$kant]\" value=\"assign$game_no[$kant]\"></td>";
      echo "		<td>$day[$kant]<br>$date[$kant]</td>";
      echo "		<td>$time[$kant]</td>";
      echo "		<td>$field[$kant]</td>";
      echo "		<td>$div[$kant]</td>";
      echo "		<td>$home[$kant]</td>";
      echo "		<td>$visitor[$kant]</td>";
      echo "		<td>&nbsp;</td>";
      echo "		</tr>\n";
         }
      }
      }
      echo "            </table>";


      echo "	  <h3>Assigned games</h3>\n";
      echo "	  <table>\n";
      echo "	    <tr align=\"center\">\n";
      echo "		<td>Game No.</td>\n";
      echo "		<td>Assigned</td>\n";
      echo "		<td>Day</td>\n";
      echo "		<td>Time</td>\n";
      echo "		<td>Location</td>\n";
      echo "		<td>Div</td>\n";
      echo "		<td>Home</td>\n";
      echo "		<td>Away</td>\n";
      echo "		<td>Referee<br>Team</td>\n";
      echo "          </tr>\n";

      for ( $kant=0; $kant < $kount; $kant++ ) {
         if ( $rep == $ref_team[$kant]) {
      echo "		<tr align=\"center\" bgcolor=\"#00FF88\">";
      echo "		<td>$game_no[$kant]</td>";
      if ( $locked ) {
         echo "		<td>Locked</td>";
      }
      else {
         echo "		<td><input name=\"game$game_no[$kant]\" type=\"checkbox\" value=\"assign$game_no[$kant]\" checked></td>";
      }
      echo "		<td>$day[$kant]<br>$date[$kant]</td>";
      echo "		<td>$time[$kant]</td>";
      echo "		<td>$field[$kant]</td>";
      echo "		<td>$div[$kant]</td>";
      echo "		<td>$home[$kant]</td>";
      echo "		<td>$visitor[$kant]</td>";
      echo "		<td>$ref_team[$kant]</td>";
      echo "		</tr>\n";
         }
      }
      echo "            </table>";
      if ( $locked && $allatlimit ) {
         echo "<h3>Submit Disabled</h3>\n";
      }
      else {
         echo "            <input type=\"submit\" name=\"Submit\" value=\"Submit\">\n";
      }
      echo "            </form>\n";      
      $_SESSION['locked'] = $locked;
   }
   elseif ( !$authed ) {
      print "<center><h1>You are not Logged On</h1></center>";
      echo "<p align=\"center\"><a href=\"index.htm\">Logon Page</a></p>";
      session_destroy();
   }
   elseif ( $authed && $rep == 'Section 1' ) {
      print "<center><h1>You should be on this<br>";
      echo "<a href=\"sched_master.php\">Schedule Page</a></h1>";
   }
   else {
      print "<center><h1>Something went wrong here!</h1></center>";
   }
?>

        </div>
    </td></tr>
   <tr><td>
   <h3 align="center">
      <a href="sched_greet.php">Return to main screen</a>&nbsp;-&nbsp;
      <a href="sched_sched.php">Return to schedule</a>&nbsp;-&nbsp;
      <a href="sched_end.php">Logoff</a>
   </h3>
</td>
</tr>
  <!-- InstanceEndEditable -->
</table>
<p align="center"><span class="foottext"><a href="index.htm">Home</a> - <a href="section_staff.htm">Contact&nbsp;Us</a>  - <a href="siteindex.htm" class="flinks">Site&nbsp;Index<br>
</a>Corrections or additions to this web page can be sent to: <a href="mailto:webmaster@aysosection1.org">webmaster@aysosection1.org</a><br>
&copy;2005  Section one, American Youth Soccer Organization <br />
AYSO name and AYSO initials, Logos and graphics copyright by the <a href="http://www.soccer.org" class="flinks">American Youth Soccer Organization</a> and used with permission.</span></p>
</body>
<!-- InstanceEnd --></html>
