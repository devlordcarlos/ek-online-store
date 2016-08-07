<?php

session_start();
require_once('../mysql_connect.php');

$user=$_SESSION['type'];
if($user!='aac'){
  header("Location: http://".$_SERVER['HTTP_HOST'].  dirname($_SERVER['PHP_SELF'])."/index.php");
}

if(isset($_POST['specific_comment'])){
$id=$_POST['specific_comment'];
$_SESSION['specific_comment']=$id;
}
else{
	$id=$_SESSION['specific_comment'];
}

$datetime= date("Y-m-d H:i:s");

$query="select username from accounts where account_id=$id";
		$result=mysqli_query($dbc, $query);
		$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
$user=$row['username'];


$query="select count(c.comment_id) as comment_count from comments c join products p on c.prod_id=p.prod_code where c.username='$user'";
$result=mysqli_query($dbc,$query);
$row=mysqli_fetch_array($result, MYSQLI_ASSOC);
$pages=$row['comment_count'] / 10;
$page_no=ceil($pages);

$start=0;
if(isset($_GET['go'])){
	$pn=$_GET['dropdown'];

$start=($_GET['dropdown']-1)*10;
}
else{
$pn=1;
}


echo"
<p align='center'><b>COMMENTS OF USER: '{$user}'</p></b>
<p align='left'>$datetime</p>
<table width='75%' border='1' align='center' cellpadding='0' cellspacing='0' bordercolor='#000000'>
<tr>
<td>
DATE
</td>
<td>
PRODUCT
</td>
<td>
COMMENT
</td>
<td>
STATUS
</td>
</tr>
";

$query="select c.date, p.prod_name, c.comment, c.status from comments c join products p on c.prod_id=p.prod_code where c.username='$user'";
$result=mysqli_query($dbc,$query);
while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)){
echo"
<tr>
<td>
{$row['date']}
</td>
<td>
{$row['prod_name']}
</td>
<td>
{$row['comment']}
</td>
<td>
";
if($row['status']==0){
	echo"Unapproved";
}

else if($row['status']==1){
	echo"Pending";
}

else if($row['status']==2){
	echo"Approved";
}
echo"
</td>
</tr>
";

}

echo"</table>";
if(isset($_GET['dropdown'])){
if($_GET['dropdown']==$page_no){
echo "<p align='center'><b>----END OF REPORT----</p></b>";
}
}
else{
	if($page_no==1){
		echo "<p align='center'><b>----END OF REPORT----</p></b>";
	}
}


echo "<p align='right'>Page: {$pn}	</p>";
echo"
<form action='{$_SERVER['PHP_SELF']}' method='GET'>
<p align='right'>Page: <select name='dropdown'></p>
";
for($i=$page_no;$i>0;$i--){
	echo"
		<option value='{$i}' selected>{$i} </option>
	";
}
echo"
</select>
<input type='submit' name='go' value='Go' />
</form>
";

?>