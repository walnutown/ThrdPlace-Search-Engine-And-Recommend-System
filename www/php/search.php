<?php
header('Content-Type: text/html; charset=utf-8');
$limit = 20;
$query = isset($_GET['q']) ? $_GET['q'] : false;
if($query === ""){
	$query = "*:*";
}
$sort = isset($_GET['s']) ? $_GET['s'] : false;
$order = isset($_GET['order']) ? $_GET['order'] : false;
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : false;
$filter = "New";
$stateFilter = isset($_COOKIE['state'])? ($_COOKIE['state']):"";
$cityFilter = isset($_COOKIE['city'])? ($_COOKIE['city']):"";
session_start();
// some code here
if(isset($_GET['name'])){
	if(isset($_SESSION['name'])){
		unset($_SESSION['name']);
	}
	if(isset($_SESSION['location'])){
		unset($_SESSION['location']);
	}
}
$location = "Louisville";
$name = "Ew2701";
$projectName = null;
if (!isset($_SESSION['location'])) {
    $location = isset($_GET['location']) && strcmp($_GET['location'], "")!=0 ? $_GET['location'] : "Louisville";
	$name = isset($_GET['name']) && strcmp($_GET['name'], "") !=0 ? $_GET['name'] : "Ew2701";
	$_SESSION['location'] = $location;
	$_SESSION['name'] = $name;
} else {
    $name = $_SESSION['name'];
	$location = $_SESSION['location'];
}

if(strcmp($location, "") != 0){
	$filter = $location;

}

$results = false;

require_once( '../include/SolrPhpClient/Apache/Solr/Service.php' );   
if ($query){
	try{
		if($searchType == "Contributor"){
			$solr = new Apache_Solr_Service( 'localhost', '8983', '/solr/contributor1' );
		}
		else{ 
			if($searchType == "Creator"){
				$solr = new Apache_Solr_Service( 'localhost', '8983', '/solr/ProjectCreator' );
			}
			else{
				$solr = new Apache_Solr_Service( 'localhost', '8983', '/solr/collection1' );	
			}
			
		}
		if ( !$solr->ping()) {
			echo 'Solr service not responding.';
		}
		$params = array(
					'sort' => $sort . " " . $order,
					'fq' =>array(
						$stateFilter,
						$cityFilter
					)
				);
		$results = $solr->search($query, 0, $limit, $params);
	}catch (Exception $e){
		die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
	}
}	

require_once( '../include/SolrPhpClient/Apache/Solr/Service.php' ); 
$solr = new Apache_Solr_Service( 'localhost', '8983', '/solr/collection1' ); 
if ( ! $solr->ping() ) {
    echo 'Solr service not responding.';
    exit;
}
if (true)
{
  try
  {
	$params = array(
		'fq' => $name
	);	
    $ProjectN = $solr->search("*:*", 0, $limit, $params);
	foreach($ProjectN->response->docs as $doc){
		$projectName = $doc->project_title;
	}
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
        // and then show a special message to the user but for this example
        // we're going to show the full exception
        die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
  
  }

 
 
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Search</title>
<link href="../css/search.css" rel="stylesheet" type="text/css" />
<link href="../css/list.css" rel="stylesheet" type="text/css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="../js/jquery.chained.min.js"></script>
<script>
  $( document ).ready(function() {
	// Handler for .ready() called.
	$("#series").chained("#mark");
	});
	
	function setCookie(c_name,value,exdays){
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}
	
	function setState(){
		var s = document.getElementById("mark").value;
		setCookie("state",s,365);
	}
	
	function setCity(){
		var c = document.getElementById("series").value;
		setCookie("city",c,365);
	}
  </script>
<!--[if lte IE 7]>

<style>
.content { margin-right: -1px; } /* this 1px negative margin can be placed on any of the columns in this layout with the same corrective effect. */
ul.nav a { zoom: 1; }  /* the zoom property gives IE the hasLayout trigger it needs to correct extra whiltespace between the links */
</style>
<![endif]-->
</head>

<body>
<div class="status-bar">
	<P>Name: <?php echo htmlspecialchars($name, ENT_QUOTES, 'utf-8') . "    ";?>Location: <?php echo htmlspecialchars($filter, ENT_QUOTES, 'utf-8') . "    ";?>Project: <?php echo htmlspecialchars($projectName, ENT_QUOTES, 'utf-8');?></p>
</div>
<div class="container">
  <div class="header">
	<div class="logo-header">
		<a href="../thrdplace.html">
			<img src="../img/beta-logo.jpg" alt="Insert Logo Here" width="100%" name="Insert_logo"  id="Insert_logo" style="background-color: #8090AB; display:block;" />
		</a>
	</div>
	<div class="search-box">
		<FORM  accept-charset="utf-8" method="get">
			<LABEL for = "q"></LABEL>
			<INPUT id = "search-text" id = "q" name = "q" type = "text" placeholder="Which project do you want to search for?" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8');?>"/>
			<INPUT id = "search-button" type = "submit" value="search"/>
			<select id="search-type" class="search-type" name = "searchType">
                    <option <?php if((isset($searchType) && $searchType === "Project")) echo "selected = \"selected\""?>>Project</option>
                    <option <?php if((isset($searchType) && $searchType === "Contributor")) echo "selected = \"selected\""?>>Contributor</option>
                    <option <?php if((isset($searchType) && $searchType === "Creator")) echo "selected = \"selected\""?>>Creator</option>
            </select>
			<INPUT type="hidden" name="s" value="id">
			<INPUT type="hidden" name="order" value="DESC">
		<FORM>
	</div>
    
	
	<!-- end .header --></div>
	<div class="sidebar1">
		<H4 class="section-title">SORT</H4>
		<div class="type-tag">
			<ul class="nav">
<?php
			if((isset($searchType) && $searchType === "Creator")){
?>				
				<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Creator&s=username&order=DESC&rows=20">Username</a></li>
				
<?php			
			}
			else{
				if((isset($searchType) && $searchType === "Contributor")){
?>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Contributor&s=promotion&order=DESC&rows=20">Influence</a></li>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Contributor&s=contribute_money&order=DESC&rows=20">Money</a></li>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Contributor&s=contribute_volunteer_hours&order=DESC&rows=20">Hours</a></li>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Contributor&s=contribute_supplies&order=DESC&rows=20">Supplies</a></li>
					
<?php
				}
				else{
?>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Project&s=influence&order=DESC&rows=20">Influence</a></li>
					<li><a href="/php/search.php?q=<?php echo $query?>&searchType=Project&s=success&order=DESC&rows=20">Success</a></li>
<?php
				}
			}
?>
			</ul>
		</div>
		<H4 class="section-title">LOCATION</H4>
		<div class="type-tag">
		
			
			<form>
			<p>State</p>
			<select id="mark" name="mark" style="width:200px;" class="search-type" onchange="setState()">		
<?php
			//pseudo
			$arr = array("", "Alabama", "California", "NewYork");
			foreach($arr as $value) {
				echo '<option value="' . $value . '" ';
				if($stateFilter === $value) {
					echo "selected = \"selected\"";
				}
				if($value===""){
					echo '>--</option>';
				}
				else{
					echo '>' . $value . "</option>";
				}
			}
?>				
			</select>
			<p>&nbsp;</p>
			
			<p>City</p>
			<select id="series" name="series" style="width:200px;" class="search-type" onchange="setCity()">
<?php
			//pseudo
			$arr = array("", "Adamsville", "Cottonwood", "Eclectic", "Gurley", "Hollins", "Mobile", "NewHope", "Sheffield", "SpanishFort");
			foreach($arr as $value) {
				echo '<option value="' . $value . '" ';
				if($cityFilter === $value) {
					echo "selected = \"selected\"";
				}
				if($value===""){
					echo '>--</option>';
				}
				else{
					echo ' class="Alabama">' . $value . "</option>";
				}
			}
			
			$arr = array("Austerlitz", "Brooklyn", "Cassadaga", "EastElmhurst", "FlyCreek", "Haverstraw", "Inwood", "LakeLuzerne", "Thomson");
			foreach($arr as $value) {
				echo '<option value="' . $value . '" ';
				if($cityFilter === $value) {
					echo "selected = \"selected\"";
				}
				if($value===""){
					echo '>--</option>';
				}
				else{
					echo ' class="NewYork">' . $value . "</option>";
				}
			}
			
			$arr = array("Aguanga", "Anaheim", "Arbuckle", "Arcadia", "Arleta", "Baker", "Ballico", "BirdsLanding", "BodegaBay", "LosMolinos","LosAngeles");
			foreach($arr as $value) {
				echo '<option value="' . $value . '" ';
				if($cityFilter === $value) {
					echo "selected = \"selected\"";
				}
				if($value===""){
					echo '>--</option>';
				}
				else{
					echo ' class="California">' . $value . "</option>";
				}
			}
?>	
			</select>
			<p>&nbsp;</p>
			</form>
		</div>
    <p>&nbsp;</p>
    <!-- end .sidebar1 --></div>
  <div class="content">
    <div class="results-div">
	
	<div class="results">

<?php

	if($results){
		$total = (int) $results->response->numFound;
		$start = min(1, $total);
		$end = min($limit, $total);
?>
<?php
		if($total < 1){
			echo "<H3>NO RESULTS<H3>";
		}
		else{
			$projcet_count = 0;
			if($searchType == "Contributor"){
?>
<?php
				foreach($results->response->docs as $doc){
					$projcet_count++;
?>	
					<div class="results-list">
						<H4 class="result-title"><?php echo "[" . $projcet_count . "]CONTRIBUTOR:   " . $doc->contributor_first_name . " " . $doc->contributor_last_name;?></H4>
						<div class="project-img">
							<img src="../img/Contributor.jpg" alt="No image" width="100%" height="100%">
						</div>
						<div class="project-profile">
							<div>
								<span class="item-owner" ><?php echo "Email:   " . $doc->contributor_email;?></span>
								<span class="item-owner" ><?php echo "Phone:   " . $doc->contributor_phone;?></span>
								<span class="item-location" ><?php echo "Address:   " . $doc->contributor_address . ", " . $doc->contributor_city . ", " . $doc->contributor_state;?></span>
							</div>
						<div>
							<span class="item11" ><?php echo "Contribution(Money):   $" . $doc->contribute_money;?></span>
							<span class="item11" ><?php echo "Contribution(Hours):   " . $doc->contribute_volunteer_hours. " hour(s)";?></span>
							<span class="item11" ><?php echo "Contribution(Supply):   " . $doc->contribute_supplies;?></span>
							<span class="item11" ><?php echo "Influence:   " . $doc->promotion;?></span>
						</div>
						<div class="detail">
							<?php echo "Project:   " . $doc->project_title;?>	
						</div>
						</div>
							<p>&nbsp;</p>
					</div>
<?php
				}
			}
			else{ 
				if($searchType == "Creator"){
					foreach($results->response->docs as $doc){
						$projcet_count++;
?>
						<div class="results-list">
						<H4 class="result-title"><?php echo "[" . $projcet_count . "]CREATOR:   " . $doc->creator_first_name . " " . $doc->creator_last_name;?></H4>
						<div class="project-img">
							<img src="../img/creator.jpg" alt="No image" width="100%" height="100%">
						</div>
						<div class="project-profile">
							<div>
								<span class="item-owner" ><?php echo "User Name:   " . $doc->username;?></span>
								<span class="item-owner" ><?php echo "Email:   " . $doc->creator_email;?></span>
								<span class="item-owner" ><?php echo "Phone:   " . $doc->creator_phone;?></span>
							</div>
						<div>
							<span class="item11" ><?php echo "Address   " . $doc->creator_address;?></span>
							<span class="item11" ><?php echo "City:   " . $doc->creator_city;?></span>
							<span class="item11" ><?php echo "State:   " . $doc->creator_state;?></span>
							<span class="item11" ><?php echo "Zip:   " . $doc->creator_zip;?></span>
						</div>
						<div class="detail">
							<?php echo "Project(Created):   " . $doc->project_title;?>	
						</div>
						</div>
							<p>&nbsp;</p>
						</div>


<?php
					}
				}
				else{
					foreach($results->response->docs as $doc){
						$projcet_count++;
?>	
						<div class="results-list">
						<H4 class="result-title"><?php echo "[" . $projcet_count . "]PROJECT:   " . $doc->project_title;?></H4>
						<div class="project-img">
							<img src="../img/school_3.jpg" alt="No image" width="100%" height="100%">
						</div>
						<div class="project-profile">
							<div>
								<span class="item-owner" ><?php echo "Creator:   " . $doc->username;?></span>
								<span class="item-location" ><?php echo "Location:   " . $doc->project_address . ", " . $doc->project_city . ", " . $doc->project_state;?></span>
							</div>
						<div>
							<span class="item11" ><?php echo "Funds(needed):   " . $doc->money_needed;?></span>
							<span class="item11" ><?php echo "Volunteers(needed):   " . $doc->volunteer_needed;?></span>
							<span class="item11" ><?php echo "Supply(needed):   " . $doc->supplies_needed;?></span>
							<span class="item11" ><?php echo "Success:   " . number_format($doc->success,1) . "%";?></span>
							<span class="item11" ><?php echo "Influence:   " . number_format($doc->influence,1);?></span>
						</div>
						<div class="detail">
							<?php echo "Detail:   " . $doc->project_description;?>	
						</div>
						</div>
							<p>&nbsp;</p>
						</div>
<?php		
					}
				}
			}
		}	
	}
?>
	
	</div>
	</div>
	<div class="recommendation fancyList">
	<H6 class="section-title">RECOMMADATION</H3>	
	<H4 align="center">Contributors interested in your project</H4>
<?php
	$solr = new Apache_Solr_Service( 'localhost', '8983', '/solr/contributor1' );
	$projectKeywords = explode(" ", $projectName);
	$PK = $projectKeywords[count($projectKeywords)-1];
	$params = array(
		'fq' => $PK,
		'sort' => 'promotion desc',
		'fl' => 'contributor_phone contributor_first_name contributor_last_name promotion contributor_email'	
	);
	$results = $solr->search("*:*", 0, 5, $params);	
?>
	<ol class="circle-list">
<?php
	foreach($results->response->docs as $doc){
?>
		<li>
		    <a href="http://trojansite.com/influence/<?php echo $doc->contributor_first_name . ".html";?>"><h3><?php echo $doc->contributor_first_name . " " . $doc->contributor_last_name?></h3></a>
		    <p>Email:<?php echo $doc->contributor_email;?></p>
			<p>Phone:<?php echo $doc->contributor_phone;?></p>
			<p>Influence:<?php echo $doc->promotion;?></p>

		</li>
<?php
	}
?>
	</ol>
	</div>
    <!-- end .content --></div>
	<div class="footer">
		<div id="colophon">
		<div class="wrap">
			<a href="/blog/?p=1013">Join the team</a> //
			<a href="/blog/?p=999">Contact us</a> //
			<a href="/blog/?p=123">The team</a> //
			<a href="/blog/">Blog</a> //
			<a href="/blog/?p=132 ">How to create a project</a>  //
			<a href="/blog/?cat=21">FAQs</a>// 
			<a href="/blog/?p=1018">Terms of use</a>
			
		</div>	
		</div>
    <!-- end .footer --></div>
  <!-- end .container --></div>
</body>
</html>


