<?php session_start();
   header("Cache-control: private");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html><!-- InstanceBegin template="/Templates/schedule.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
<link rel="SHORTCUT ICON" href="http://www.aysosection1.org/favicon.ico">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!-- InstanceBeginEditable name="doctitle" -->
<title>AYSO Section 1</title>
<meta name="description" content="Welcome to the Home Page of Section 1 of the American Youth Soccer Organizition ( AYSO ) serving Los Angeles, San Bernardino, and Riverside Counties in Southern California.">
<meta name="keywords" content="aysosection1, ayso section 1, ayso, section 1, section one, soccer, youth soccer, los angeles, riverside,san bernardino, american youth soccer organization">
<!-- InstanceEndEditable -->
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
body {
	background-color: #FFFFFF;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
a:link {
	color: #0066CC;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #0099FF;
}
a:hover {
	text-decoration: none;
	color: #0000FF;
	background-color: #FFFFCC;
}
a:active {
	text-decoration: none;
	color: #CC0000;
	background-color: #FFFFCC;
}
h1 {
	font-size: 16px;
}
h2 {
	font-size: 14px;
}
h3 {
	font-size: 12px;
}
.style1 {
	color: #FFFFFF;
	font-weight: bold;
}
.style4 {
	font-size: 9px;
	font-weight: bold;
	color: #FFFFFF;
}
table.banner {
	padding-top: 2px;
	padding-right: 4px;
	padding-bottom: 1px;
	padding-left: 1px;
	background-color: #0066FF;
}
.style4 a:link {
	color: #FFFFFF;
	text-decoration: underline;
}
.style4 a:visited {
	color: #FFFFFF;
	text-decoration: underline;
}
.style4 a:hover {
	color: #0000FF;
	text-decoration: none;
	background-color: #66CCFF;
}
.style4 a:active {
	color: #0000FF;
	text-decoration: none;
	background-color: #66CCFF;
}
.colhead {
	font-size: small;
	font-weight: bold;
	color: #000000;
	background-color: #66FFFF;
	text-align: center;
	border: thick solid #0066FF;
}
.colcontent {
}
.colhighlight {
	font-weight: bold;
}
.maincontent {
	padding: 10px;
	margin-top: 10px;
	border: 0px;
}
table {
	margin: 0px;
	padding: 0px;
	width: 100%;
	border: 0px;
}
.contentarea {
	padding-right: 15px;
	padding-left: 15px;
	line-height: 1.4;
	vertical-align: top;
	text-align: left;
}
.flinks {
	text-decoration: none;
}
.foottext {
	font-family: Arial, Helvetica, sans-serif;
	font-size: xx-small;
}
.bannertext {
	font-size: large;
	font-weight: bold;
	font-variant: small-caps;
	color: #CC0000;
	text-align: center;
	vertical-align: middle;
}
.topspace {
	padding-top: 5px;
}
-->
</style>
<!-- InstanceBeginEditable name="head" --><!-- InstanceEndEditable -->
</head>

<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <tr>
    <td width="140" height="72"><div align="center"><img src="graphics/logo2.jpg" width="69" height="72"></div></td>
	<td class="bannertext"><!-- InstanceBeginEditable name="TopHeader" -->Section One <br>
American Youth Soccer Organization<!-- InstanceEndEditable --></td>
	<td width="140" height="72" align="right"><div align="center"><img src="graphics/AYSOLOG2.gif" width="60" height="60"></div></td>
  </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="50%" height="20" nowrap bgcolor="#0066FF"><span class="style1">Welcome to AYSO Section One </span></td>
    <td bgcolor="#0066FF">&nbsp;</td>
  </tr>
</table>
<table class="banner">
  <tr>
    <td colspan="2" class="style1">Areas that make up Section 1 </td>
    <td class="style4"><a href="http://www.aysoarea1b.org" target="_blank">Area 1B - Diamond Bar and Pomona Valley</a> </td>
    <td class="style4"><a href="http://www.aysoarea1c.org" target="_blank">Area 1C - W San Gabriel Valley</a></td>
    <td><span class="style4"><a href="http://www.ayso1d.org" target="_blank">Area 1D - Beach Cities</a></span></td>
  </tr>
  <tr>
    <td width="10">&nbsp;</td>
    <td class="style4"><a href="http://www.aysoarea-1f.org" target="_blank">Area 1F - Southwest Los Angeles County</a> </td>
    <td class="style4">Area 1G - Inland Empire</td>
    <td class="style4">Area 1H - Desert Cities </td>
    <td class="style4"><a href="http://www.aysoarea1n.org" target="_blank">Area 1N - San Bernardino County</a> </td>
  </tr>
  <tr>
    <td width="10">&nbsp;</td>
    <td class="style4"><a href="http://www.ayso1p.org" target="_blank">Area 1P - Central and West Los Angeles</a> </td>
    <td class="style4"><a href="http://area1r.ayso47.org" target="_blank">Area 1R - W Riverside County</a> </td>
    <td class="style4">Area 1S - W Arizona, E California, S Nevada </td>
    <td><span class="style4"><a href="http://www.area1u.org" target="_blank">Area 1U - E San Gabriel and W Pomona Valley </a></span></td>
  </tr>
</table>
<table width="98%"  class="maincontent">
<!-- InstanceBeginEditable name="mainContent" -->
  <tr>
    <td align="center" class="contentarea">
     
<?php
      $authed = $_SESSION['authed'];
      $rep = $_SESSION['unit'];
      $locked = $_SESSION['locked'];
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
