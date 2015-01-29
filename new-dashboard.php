<?php
//error_reporting(0);

/**
 * Our custom dashboard page
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );
?>


<!---------------PAGE CONTENT STARTS HERE------------------->

<?php
	/**************** Code to extracting information from wp_posts starts here ************************/
	
	$file_title = array(); //array used for storing file title
	$file_url = array(); //array used for storing file url
	$file_id = array(); //array used for storing file id

	$query = "SELECT * FROM wp_posts WHERE post_type='attachment' && post_mime_type='application/pdf'";
	$result = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$file_title[] = $row['post_title'];
		//echo $title;
		
		$file_url[] = $row['guid'];
		//echo $url;
		
		$file_id[] = $row['ID'];
	}
	
	//print_r ($file_title);
	//echo "<br/><br/>";
	//print_r ($file_url);
	echo "<br/>";
	//print_r ($file_id);
	
	/**************** Code to extracting information from wp_posts ends here ************************/
	
	/**************** Code to print information in table starts here ************************/
	$f_id = array();
	echo "<form method='post' action='../process.php'/>";
	echo "<table>";
		echo "<tr class='head'>";
			echo "<th colspan='4'>Included Files</th>";
		echo "</tr>";
		echo "<tr>";
			echo "<th>Title</th>";
			echo "<th class='center'>Type</th>";
			echo "<th class='center'>Include</th>";
			echo "<th class='center'>Exclude</th>";
		echo "</tr>";
		/******** forech loop starts here for included files ********/
	
		$c = 0;
		foreach($file_title as $title)
		{
		$query = "SELECT * FROM wp_upload_pdfs WHERE file_id='$file_id[$c]'";
		$result = mysql_query($query) or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
			$included = $row['included'];
		}
		if($included==yes)
		{
			$f_id[] = $file_id[$c];
			$checked = "checked";
			$type = "PDF";
			echo "<tr>";
				echo "<td><a href='".$file_url[$c]."' target='_blank'>".$title."</a></td>";
				echo "<td class='center'>".$type."</td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' ".$checked." value='yes'/></td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' value='no'/></td>";
			echo "</tr>";
		}
		$c++;
		}
		
		/******** forech loop ends here for included files ********/
		echo "<tr class='head'>";
			echo "<th colspan='4'>Excluded Files</th>";
		echo "</tr>";
		echo "<tr>";
			echo "<th>Title</th>";
			echo "<th class='center'>Type</th>";
			echo "<th class='center'>Include</th>";
			echo "<th class='center'>Exclude</th>";
		echo "</tr>";
		/******** forech loop starts here for excluded files ********/
		$c = 0;
		foreach($file_title as $title)
		{
		$query = "SELECT * FROM wp_upload_pdfs WHERE file_id='$file_id[$c]'";
		$result = mysql_query($query) or die(mysql_error());
		while($row = mysql_fetch_array($result))
		{
			$included = $row['included'];
		}
		if($included==no || $included==null)
		{
			$f_id[] = $file_id[$c];
			$checked = "checked";
			$type = "PDF";
			echo "<tr>";
				echo "<td><a href='".$file_url[$c]."' target='_blank'>".$title."</a></td>";
				echo "<td class='center'>".$type."</td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' value='yes'/></td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' ".$checked." value='no'/></td>";
			echo "</tr>";
		}
		$c++;
		}
		/******** forech loop ends here for excluded files ********/
	echo "</table>";
	echo "<br/>";
	echo "<input type='submit' class='button button-primary menu-save' name='submit' value='Save Changes'/>";
	echo "</form>";
	/***************** Code to print information in table ends here ************************/
	
	session_start();
	$_SESSION['file_title'] = $file_title;
	$_SESSION['file_url'] = $file_url;
	$_SESSION['file_id'] = $f_id;
	
?>

<!---------------PAGE CONTENT ENDS HERE------------------->

<!----------------STYLE FOR THE PAGE STARTS HERE--------------->

<style>
	table{ width:95%; text-align:left; border:solid 1px #000}
	th{background-color:#000; color:#fff}
	.head th{line-height:30px; background-color:#0074a2; color:#fff; font-size:25px}
	.center{text-align:center}
	tr:hover{background-color:#ffffb3}
</style>

<!---------------- STYLE FOR THE PAGE ENDS HERE--------------->