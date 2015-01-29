<?php
error_reporting(0);
session_start();

$server = "index.php?page=vinod-dashboard";
/**
 * Our custom dashboard page
 */

/** WordPress Administration Bootstrap */
require_once( ABSPATH . 'wp-load.php' );
require_once( ABSPATH . 'wp-admin/admin.php' );
require_once( ABSPATH . 'wp-admin/admin-header.php' );

function merge_queries(array $original, array $updates) 
		{
			$params = array_merge($original, $updates);
			return '?'.http_build_query($params);
			
		}
		
	
?>
<?php
if(isset($_POST['submit']))
	{
		$file_title = $_SESSION['file_title'];
		//print_r ($file_title);
		//echo "<br/><br/>";
		
		$file_url = $_SESSION['file_url'];
		//print_r ($file_url);
		//echo "<br/><br/>";
		
		$file_id = $_SESSION['file_id'];
		//print_r ($file_id);
		//echo "<br/><br/>";
		
                $truncate = mysql_query("TRUNCATE TABLE wp_upload_pdf_sitemap") or die(mysql_error());

		$c=0;
		foreach($file_id as $id)
		{
			//echo $id."<br/>";
			$included = $_POST[$id];
			//echo "is included : ".$included."<br/>";
			$f_url = $file_url[$c];
			//echo $f_url."<br/>";
			$f_title = $file_title[$c];
			//echo $f_title."<br/><br/>";
			
			$select = mysql_query("SELECT * FROM wp_upload_pdfs WHERE file_id='$id'");
			$num = mysql_num_rows($select);
			if($num==1)
			{
				$query = "UPDATE wp_upload_pdfs SET included='$included' WHERE file_id='$id'";
			}
			else
			{
				$query = "INSERT INTO wp_upload_pdfs(file_url,file,included,file_id) VALUES('$f_url','$f_title','$included','$id')";
			}
                        
                        $query1 = "INSERT INTO wp_upload_pdf_sitemap(file_url,file,included,file_id) VALUES('$f_url','$f_title','$included','$id')";
			$result = mysql_query($query);
                        $result = mysql_query($query1);
			
			$c++;
		}
		/************* Unset Used session variables ***************/
		unset($_SESSION['file_title']);
		unset($_SESSION['file_url']);
		unset($_SESSION['file_id']);
                 //ob_flush();
                 //ob_start();   
               // header('location:http://hostzack.com/adapplied/wp-admin/index.php?page=vinod-dashboard');
	}
	
	if(isset($_POST['file_type_submit']))
	{
		$file_type = $_POST['file_type'];
		unset($_SESSION['file_type']);
		$_SESSION['file_type'] = $file_type;
		//print_r ($file_type);
		//echo "<br/>";
		
	}
?>
<!---------------PAGE CONTENT STARTS HERE------------------->
<div>
	<div class="clear"></div>
	<form action="<?php echo $server; ?>" method="post">
	<select multiple name="file_type[]">
		<option disabled selected>Select File Type(s)</option>
		<option value="application/pdf">PDF</option>
		<option value="audio/mpeg">MP3</option>
		<option value="video/mp4">MP4</option>
		<option value="application/msword">DOCX</option>
		<option value="image/png">PNG</option>
		<option value="image/jpeg">JPG</option>
	</select>
	<input type="submit" name="file_type_submit" class='button button-primary menu-save' value="Run The Script"/>
	</form>
</div>
<?php
	/**************** Code to extracting information from wp_posts starts here ************************/
	
	$file_title = array(); //array used for storing file title
	$file_url = array(); //array used for storing file url
	$file_id = array(); //array used for storing file id

	
	if(isset($_SESSION['file_type']))
	{
		$query = "SELECT * FROM wp_posts WHERE post_type='attachment'";
		$query .=" AND ";
		$combine = '';
		$file_type = $_SESSION['file_type'];
		foreach($file_type as $f_type)
		{
			//ADD THE QUERY INFORMATION TO THE WHERE CLAUSE
			$query.="{$combine}post_mime_type='$f_type' "; 
			$combine='OR ';
		}
		if(isset($_GET['sort']))
		{
			$sort = $_GET['sort'];
			if($sort==de_post_title)
			{
				$query .="ORDER BY post_title DESC";
			}
			elseif($sort==post_title)
			{
				$query .="ORDER BY post_title ASC";
			}
		}
	}
	else
	{
		$query = "SELECT * FROM wp_posts WHERE post_type='abc'";
	}
	//echo $query;
	$result = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$file_title[] = $row['post_title'];
		//echo $title;
		
		$file_url[] = $row['guid'];
		//echo $url;
		
		$file_id[] = $row['ID'];

		$file_type[] = $row['mime_type'];
	}
	
	//print_r ($file_title);
	//echo "<br/><br/>";
	//print_r ($file_url);
	echo "<br/>";
	//print_r ($file_id);
	
	/**************** Code to extracting information from wp_posts ends here ************************/
	
	/**************** Code to print information in table starts here ************************/
	$f_id = array();
	echo "<form method='post' action='$server'/>";
	echo "<table>";
	echo "<tr class='head'>";
	echo "<th colspan='4'>Included Files</th>";
	echo "</tr>";
	echo "</table>";
	echo "<table>";
		echo "<thead>";
		echo "<tr>";
			if(isset($_GET['sort']))
			{
				if($_GET['sort']==post_title)
				{
					$sort = "de_post_title";
				}
				elseif($_GET['sort']==de_post_title)
				{
					$sort = "post_title";
				}
				else
				{
					$sort = "post_title";
				}
				
			}
                        else
			{
				$sort = "post_title";
			}
			echo "<th width=\"825px\"><a href=\"".merge_queries($_GET, array('sort' =>$sort))."\">Title</a></th>";
			echo "<th class='center'>Type</th>";
			echo "<th class='center'>Include</th>";
			echo "<th class='center'>Exclude</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
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
		if($included==yes || $included==null)
		{
			$f_id[] = $file_id[$c];
			$checked = "checked";
			$type = pathinfo($file_url[$c], PATHINFO_EXTENSION);
			echo "<tr>";
				echo "<td><a href='".$file_url[$c]."' target='_blank'>".$title."</a></td>";
				echo "<td class='center capital'>".$type."</td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' ".$checked." value='yes'/></td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' value='no'/></td>";
			echo "</tr>";
		}
		$c++;
		}
		echo "</tbody>";
		echo "</table>";
		/******** forech loop ends here for included files ********/
		echo "<table>";
		echo "<tr class='head'>";
		echo "<th colspan='4'>Excluded Files</th>";
		echo "</tr>";
		echo "</table>";
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
			echo "<th width=\"825px\"><a href=\"".merge_queries($_GET, array('sort' =>$sort))."\">Title</a></th>";
			echo "<th class='center'>Type</th>";
			echo "<th class='center'>Include</th>";
			echo "<th class='center'>Exclude</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tbody>";
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
		if($included==no)
		{
			$f_id[] = $file_id[$c];
			$checked = "checked";
			$type = pathinfo($file_url[$c], PATHINFO_EXTENSION);
			echo "<tr>";
				echo "<td><a href='".$file_url[$c]."' target='_blank'>".$title."</a></td>";
				echo "<td class='center capital'>".$type."</td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' value='yes'/></td>";
				echo "<td class='center'><input type='radio' name='".$file_id[$c]."' ".$checked." value='no'/></td>";
			echo "</tr>";
		}
		$c++;
		}
		echo "</tbody>";
		/******** forech loop ends here for excluded files ********/
	echo "</table>";
	echo "<br/>";
	echo "<input type='submit' class='button button-primary menu-save' name='submit' value='Save Changes'/>$nbsp ";
	echo " <a href='http://www.adapplied.com/wp-admin/sitemap.php' target='_blank'><input type='button' class='button button-primary menu-save' value='View Sitemap'/></a>";
	echo "</form>";
	/***************** Code to print information in table ends here ************************/
	
	//session_start();
	$_SESSION['file_title'] = $file_title;
	$_SESSION['file_url'] = $file_url;
	$_SESSION['file_id'] = $f_id;
	
?>

<?php
	/*$query = "SELECT * FROM wp_upload_pdfs";
	$result = mysql_query($query) or die(mysql_error());
	while($row = mysql_fetch_array($result))
	{
		$post_id = $row['file_id'];
		
		$select1 = "SELECT * FROM wp_postmeta WHERE post_id='$post_id'";
		$result1 = mysql_query($select1) or die(mysql_error());
		while($row1 = mysql_fetch_array($result1))
		{
			echo $row1['meta_key']." : ".$row1['meta_value'];
			echo "<br/>";
		}
	}*/
?>


<!---------------PAGE CONTENT ENDS HERE------------------->

<!----------------STYLE FOR THE PAGE STARTS HERE--------------->

<style>
	table{ width:95%; text-align:left; border:solid 1px #000}
	th{background-color:#000; color:#fff}
	th a{color:#fff; text-decoration:none; display:block; width:100%}
	.head th{line-height:30px; background-color:#0074a2; color:#fff; font-size:25px}
	.center{text-align:center}
	tr:hover{background-color:#ffffb3}
	tr:nth-child(even) {background-color: #c0c0c0;}
	.capital{text-transform:uppercase;}
	.clear{clear:both; width:100%; height:20px}
</style>
<!---------------- STYLE FOR THE PAGE ENDS HERE--------------->