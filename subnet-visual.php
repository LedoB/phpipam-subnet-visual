<?php

# show squares to display free/used subnet

print "<br><h4>"._('Visual subnet display')." <i class='icon-gray icon-info-sign' rel='tooltip' data-html='true' title='"._('Click on IP address box<br>to manage IP address')."!'></i></h4><hr>";
print "<div class='ip_vis'>";

# set limits - general
if (PHP_INT_SIZE === 8) {
    $start_visual = gmp_strval(gmp_and("0xffffffff", (int) $Subnets->transform_to_decimal($subnet_detailed['network'])));
    $stop_visual  = gmp_strval(gmp_and("0xffffffff", (int) $Subnets->transform_to_decimal($subnet_detailed['broadcast'])));
}
else {
    $start_visual = gmp_strval(gmp_and("0xffffffff", $Subnets->transform_to_decimal($subnet_detailed['network'])));
    $stop_visual  = gmp_strval(gmp_and("0xffffffff", $Subnets->transform_to_decimal($subnet_detailed['broadcast'])));

}

# remove subnet and bcast if mask < 31
if($subnet['mask'] > 30) {}
elseif ($section['strictMode']==1) {
$start_visual = gmp_strval(gmp_add($start_visual, 1));
$stop_visual = gmp_strval(gmp_sub($stop_visual, 1));
}

# we need to reindex addresses to have ip address in decimal as key!
$visual_addresses = array();
if($addresses_visual) {
foreach($addresses_visual as $a) {
	$visual_addresses[$a->ip_addr] = (array) $a;
}
}

# bools for handling pre-range and post-range blocking
# should only ever be run once for a subnet but this is just to ensure that
$not_done = true;
$not_end = true;

# the reference legend elements
$ip_reserved = "<span style='border-color:#58e8ed;background:#58e8ed;color:#ffffff;pointer-events:hoverOnly' rel='tooltip' title='Reserved' data-position='top' data-html='true'>IP</span>"; 
$ip_used = "<span style='border-color:#a9c9a4;background:#a9c9a4;color:#ffffff;pointer-events:hoverOnly' rel='tooltip' title='Used' data-position='top' data-html='true'>IP</span>"; 
$ip_unused = "<span style='border-color:#c4c0c0;background:#ffffff;color:#ffffff;pointer-events:hoverOnly' rel='tooltip' title='Unused' data-position='top' data-html='true'>IP</span>"; 
$ip_disabled = "<span style='border-color:#4f4a4a;background:#4f4a4a;color:#000000;pointer-events:hoverOnly' rel='tooltip' title='Disabled<br>(Out-of-Range)' data-position='top' data-html='true'>IP</span>"; 

# print
for($m=$start_visual; $m<=$stop_visual; $m=gmp_strval(gmp_add($m,1))) {
    
  # creates the headers for the subnet range blocks
	if(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1) == 0 or $m == $start_visual){
    $the_ip = $Subnets->transform_to_dotted($m);
    # just the last xxx.xxx.xxx.value
    $the_dot = substr(strrchr($Subnets->transform_to_dotted($m), "."), 1);
    # the xxx.xxx.xxx. prior
    $the_prefix = substr($Subnets->transform_to_dotted($m), 0, strlen($Subnets->transform_to_dotted($m))-(strlen($Subnets->transform_to_dotted($m))-(strpos($Subnets->transform_to_dotted($m), '.', -4))));
    
    # first header 
    if($m == $start_visual){
      print "<h4><p>" . $ip_reserved . $ip_used . $ip_unused . $ip_disabled . "</p></h4>";
      $the_start_label = ((intval($the_dot)));
      $the_label = $the_prefix . "." . $the_start_label;
    }else{
      print "<br><br><hr><h4><p>" . $ip_reserved . $ip_used . $ip_unused . $ip_disabled . "</p></h4>";
      $the_start_label = 0;
      $the_label = $the_prefix . ".0";
    }
    # get the stop value
    if(($m + 255) < $stop_visual){ $the_end_range = $the_prefix . ".255"; }else if(($m + 255) >= $stop_visual){ $the_end_range = $the_prefix . "." . substr(strrchr($Subnets->transform_to_dotted($stop_visual), "."), 1); }    
    
    # center the '-' and left & right align start and stop ranges (probably a better way to do this to center it)
    # like line_length/2 - len(' - ') - len_start_label = spaces left, but I started writing PHP like 2 days ago
    $range_string_length = (strlen($the_label) + strlen($the_end_range));
    if($range_string_length <= 23){ $the_spaces_a = str_repeat("&emsp;", 27); $the_spaces_b = str_repeat("&emsp;", 26) . "&nbsp;"; }
    else if($range_string_length <= 26){ $the_spaces_a = str_repeat("&emsp;", 26) . "&ensp;"; $the_spaces_b = str_repeat("&emsp;", 25) . "&ensp;"; }
    else if($range_string_length <= 28){ $the_spaces_a = str_repeat("&emsp;", 25) . "&ensp;"; $the_spaces_b = $the_spaces_a; }
    else if($range_string_length < 29){ $the_spaces_a = str_repeat("&emsp;", 26); $the_spaces_b = str_repeat("&emsp;", 25); }
    
    # the actual header for the host range in the subnet (add spacing between previous host range blocks)
    print "<br><br><h4>"._($the_label . $the_spaces_a . " - " . $the_spaces_b . $the_end_range)."<i class='icon-gray icon-info-sign' rel='tooltip' data-html='true' title='"._('Click on IP address box<br>to manage IP address')."!'></i></h4><hr>";    
	}  
		
  # the fill in for the blocks prior to the actual starting range
  if(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1) != 0 and $not_done){
          
    for($x = 0, $stop = intval(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1)); $x < $stop; $x++) {

      # print add new
      $class = "disabled";
      $id = $m;
      $action = 'disabled';
      $title = "Disabled<br>(Out-of-Range)";

      # set colors
      $background = "#4f4a4a";
      $foreground = "#000000";    
    
      # print box
      if($subnet_permission > 1) { 
        print "<span class='ip-$class modIPaddr' 	style='border-color:$background;background:$background;color:$foreground;pointer-events:hoverOnly' rel='tooltip' title='$title' data-position='top' data-html='true'>".$x."</span>"; 
      }
      else {
        print "<span class='ip-$class ' style='border-color:$background;background:$background;color:$foreground;pointer-events:hoverOnly' rel='tooltip' title='$title' data-position='top' data-html='true'>".$x."</span>"; 
      }	
      
      # need a 2-line break for the boxes to align correctly
      if($x != 0 and ($x + 1) % 32 == 0){ print "<br><br>"; }
    }
    $not_done = false;
  }

  
  # already exists
  if (array_key_exists((string)$m, $visual_addresses)) {

    # fix for empty states - if state is disabled, set to active
    if(strlen($visual_addresses[$m]['state'])==0) { $visual_addresses[$m]['state'] = 1; }

    # to edit
    $class = $visual_addresses[$m]['state'];
    $action = 'all-edit';
    $id = (int) $visual_addresses[$m]['id'];

    # tooltip
    $title = $Subnets->transform_to_dotted($m);
    if(strlen($visual_addresses[$m]['hostname'])>0)		{ $title .= "<br>".$visual_addresses[$m]['hostname']; }
    if(strlen($visual_addresses[$m]['description'])>0)	{ $title .= "<br>".$visual_addresses[$m]['description']; }

    # set colors
    $background = $Subnets->address_types[$visual_addresses[$m]['state']]['bgcolor'];
    $foreground = $Subnets->address_types[$visual_addresses[$m]['state']]['fgcolor'];
    # added this guy to make the other spans look cleaner (used=2, Reserved=3)
    $border_color = $Subnets->address_types[$visual_addresses[$m]['state']]['bgcolor'];
  }
  else {
    # print add new
    $class = "unused";
    $id = $m;
    $action = 'all-add';
    $title = $Subnets->transform_to_dotted($m);

    # set colors
    $background = "#ffffff";
    $foreground = "#333333";
    # added this guy to make the unused spans look cleaner
    $border_color = "#c4c0c0";
  }
  
  if(intval(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1)) % 32 == 0 and substr(strrchr($Subnets->transform_to_dotted($m), "."), 1) != 0){ print "<br><br>"; }  
  
  # print box
  if($subnet_permission > 1) 	{ print "<span class='ip-$class modIPaddr' 	style='border-color:$border_color;background:$background;color:$foreground' data-action='$action' rel='tooltip' title='$title' data-position='top' data-html='true' data-subnetId='".$subnet['id']."' data-id='$id'>".substr(strrchr($Subnets->transform_to_dotted($m), "."), 1)."</span>"; }
  else 						{ print "<span class='ip-$class '  			style='border-color:$border_color;background:$background;color:$foreground' data-action='$action' data-subnetId='".$subnet['id']."' data-id='$id'>".substr(strrchr($Subnets->transform_to_dotted($m), "."), 1)."</span>"; }
    
  # fill the rest of the boxes to 255
  if($m == $stop_visual){
    if(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1) != 255 and $not_end){
      for($x = intval(substr(strrchr($Subnets->transform_to_dotted($m), "."), 1))+1, $stop = 255; $x <= $stop; $x++) {
        if($x % 32 == 0){ print "<br><br>"; }
        
        # print add new
        $class = "disabled";
        $id = $m;
        $action = 'disabled';
        $title = "Disabled<br>(Out-of-Range)";

        # set colors
        $background = "#4f4a4a";
        $foreground = "#000000";    
      
        # print box
        if($subnet_permission > 1) { 
          print "<span class='ip-$class modIPaddr' 	style='border-color:$background;background:$background;color:$foreground;pointer-events:hoverOnly' rel='tooltip' title='$title' data-position='top' data-html='true'>".$x."</span>"; 
        }
        else {
          print "<span class='ip-$class ' style='border-color:$background;background:$background;color:$foreground;pointer-events:hoverOnly' rel='tooltip' title='$title' data-position='top' data-html='true'>".$x."</span>"; 
        }
      }
      $not_end = false;  
    }
  }
    
}
print "</div>";
print "<div class='clearfix' style='padding-bottom:20px;'></div>";	# clear float
