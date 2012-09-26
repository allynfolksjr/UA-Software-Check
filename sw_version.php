<?php


$allSystems = array();
$table = '<table>';

// I started making these, but then realized the script was pretty great already.


function softwareWhichChecker ($software, $system)
{
  $location = exec ("ssh webstats@$system which $software", $whichOutput, $whichReturn );
  if ( $whichReturn = 127 )
  {
    return "false";
  }
  else
  {
    return $location;
  }
} 

function softwareVersionChecker ($software, $system)
{
  $version = exec ("ssh webstats@$system $software --version", $versionOutput, $versionReturn );
  return $version;
}

// Okay, starting for reals this time.

// Configuration Variables 

// This array defines the clusters themselves.
$clusters = array("dante.u.washington.edu", 
  "vergil.u.washington.edu", 
  "homer.u.washington.edu", 
  "ovid.u.washington.edu");

// If a cluster has more than one member, define them.
// You *must* name this as the first part of the FQDN
// of the cluster appended with _members.

$dante_members = array("dante01.u.washington.edu",
  "dante02.u.washington.edu",
  "dante03.u.washington.edu");

$homer_members = array("homer01.u.washington.edu",
  "homer02.u.washington.edu",
  "homer03.u.washington.edu");

// Software to Check Versions of.

$allSoftware = array(
  "Perl" => "perl",
  "Python" => "python",
  "Ruby" => "ruby",
  "Ruby Gem" => "gem",
);


$dante_software = array(
  "Perl" => "perl",
  "Python" => "python",
);


$vergil_software = array("PHP" => "php",
  "Perl" => "perl",
  "Python" => "python",
  "Ruby" => "ruby",
  "Ruby Gem" => "gem",
);

$homer_software = array(
  "Perl" => "perl",
  "Python" => "python",
);

$ovid_software = array("PHP" => "php",
  "Perl" => "perl",
  "Python" => "python",
  "Ruby" => "ruby",
  "Ruby Gem" => "gem",
);
?>


<html> 
<head> 
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<style> 
p.code,pre  {
  background: rgb(234, 234, 234);
  border: thin solid rgb(70, 122, 167);
  padding: 2px;
 }

 a:link {
  color: #39275B;
 } 
 a:visited {
  color: #39275B;
 }
 p {
  font-family: sans-serif;
 }
ul p {
  color: #000000;
}
h2 {
  font: Georgia;
  color: #39275B
}
div.border
{
border-width: 1px;
border-style: double;
padding: 5px;
background-color: #C79900;
}
table
{
border-collapse: collapse;
}
table, th, td
{
border: 1px solid black;
}
</style>
<title>UA-installed Software Versions</title> 
</head>
<body>
<h2>Installed Software and Versions on Uniform Access Systems</h2>
<p>
The following is meant to be a reference for advanced users. Data refreshed nightly.
</p>
<div class="border">
<?php
foreach($clusters as $k => $longname)
{
  $components = explode(".", $longname);
  $shortname = $components["0"];
?>
  <? print $longname; ?> [<a href="#<? print $shortname; ?>">jump</a>]<br/>
<?
}
?>
</div>

<?php
foreach($clusters as $k => $longname)
{
  $components = explode(".", $longname);
  $shortname = $components["0"];
?>
  <h3 id="<? print $shortname; ?>" name="<? print $shortname; ?>"><strong><? print $longname; ?></strong></h3>
<h4>Cluster Members</h4>
<? if (${$shortname.'_members'})
{
  foreach(${$shortname.'_members'} as $k => $longname)
  {
    print "<h4>" . $longname . "</h4>";
    print "<pre>";
    print `ssh webstats@$longname "hostname;uname -r;uptime"`;
    print "</pre>";
    array_push($allSystems, $longname);
  }
}
else
{
  print "<pre>";
  print `ssh webstats@$longname "hostname;uname -r;uptime"`;
  print "</pre>";
  array_push($allSystems, $longname);
}


?>

<h4>Cluster Software</h4>
<p>
<?
foreach(${$shortname.'_software'} as $friendlyName => $sw)
{
  print "<em>" . $friendlyName . "</em><br/>";
  print "Location: " . `ssh webstats@$longname "which $sw"` . "<br/>";
  print "Location: " . $location . "<br/>";
  if ($sw == "python")
  {
    print "Version: <p class='code'>";
    $version = exec("ssh webstats@$longname '$sw -V 2>&1'");
    print $version;
    print "</p><br/><br/>";
  }
  elseif ($sw == "perl" or $sw == "php")
  {
    print "Version: <p class='code'>";
    $version = exec("ssh webstats@$longname '$sw --version | grep built'");
    print $version;
    print "</p><br/><br/>";
  }
  else
  {
    print "Version: <p class='code'>";
    $version = exec("ssh webstats@$longname '$sw --version'");
    print $version;
    print "</p><br/><br/>"; 
  }
  ${$shortname.$sw} = $version;
}
print "<hr/>";

}
print "<hr/>";
print "<table>";
print "<tr>";
print "<td></td>";
foreach($allSoftware as $friendlyName => $sw)
  {
    print "<td>" . $friendlyName . "</td>";
  }
print "</tr>";
foreach($clusters as $k => $longname)
{
    $components = explode(".", $longname);
    $shortname = $components["0"];
    print "<tr><td>" . $shortname . "</td>";
    foreach($allSoftware as $friendlyName => $sw)
    {
      if(${$shortname.$sw})
      {
              print "<td>" . ${$shortname.$sw} . "</td>";
      }
      else
      {
        print "<td>" . "Not Present" . "</td>";
      }

    }
    print "</tr>";
}
print "</table>";


?>




