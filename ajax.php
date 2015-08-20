﻿<?php
	session_start();
	header("Content-type: text/html; charset=utf-8");
	require_once('config.php');
	@date_default_timezone_set(PRC);
	set_time_limit(0); 
	@ob_end_clean();
	ob_implicit_flush(true);
	switch($_GET['act']){
		case "database":
			if(empty($_SESSION['member'])){
				exit('var database=new Array("login");');
			}
			$conn=mysqli_connect($dbnhost, $dbnuser, $dbnpass) or die("Error " . mysqli_error($conn)); //连接mysql              
			mysqli_select_db($conn,$dbname); //选择mysql数据库
			$conn->query("SET NAMES 'UTF8'");
			$conn->query("SET CHARACTER SET UTF8");
	        $conn->query("SET CHARACTER_SET_RESULTS=UTF8");
			$rs = $conn->query("SHOW TABLES FROM $dbname");
			$tables = array();
			while ($row = mysqli_fetch_array($rs)) {
				$tables[] = $row[0];
			}
			mysqli_free_result($rs);
			$array_tj=count($tables);
			$count=1;
			$text="";
			foreach($tables as  $key=>$tableName){
				if($key==count($tables)-1){
					$dian="";
				}else{
					$dian=",";
				}
				$text=$text.'"'.$tableName.'"'.$dian;
				$count++;
			}
		echo "var database = new Array($text);";	
		break;
		case "select":
			if(empty($_SESSION['member'])){
					echo "cnrv_msg(\"请登录\");addRow(\"登录后查询\",\"登录后查询\",\"登录后查询\",\"登录后查询\");";
					exit;
					
			}
			$select_act=(int)addslashes(trim($_POST['select_act']));
			$match_act=(int)addslashes(trim($_POST['match_act']));
			$key=addslashes(trim($_POST['key']));
			$table=addslashes(trim($_POST['table']));
				if(empty($key) || $key==''){exit("请输入查询内容");}
				if(strlen($key)<4){exit("key length!!!");}
				
					$key = str_replace("_","\_",$key);
					$key = str_replace("%","\%",$key);
						switch($match_act){
							case 2:$key = '=\''.$key.'\'';break;
							case 1:$key = ' like \''.$key.'%\'';break;
							default:exit("SB");
						}
						switch($select_act){//查询方式
							case 1:$limits="username".$key;break;
							case 2:$limits="email".$key;break;
							case 3:$limits="username".$key."or email".$key;break;
							default:exit("SB");
						}
						$conn=mysqli_connect($dbnhost, $dbnuser, $dbnpass) or die("Error " . mysqli_error($conn)); //连接mysql              
						mysqli_select_db($conn,$dbname); //选择mysql数据库
						$conn->query("SET NAMES 'UTF8'");
						$conn->query("SET CHARACTER SET UTF8");
						$conn->query("SET CHARACTER_SET_RESULTS=UTF8");
						$rs = $conn->query("SHOW TABLES FROM $dbname");
						$sql="select $Field  from `$table` where $limits LIMIT 30";
						$result = $conn->query($sql);
							if($result){
								while($rows=mysqli_fetch_assoc($result)){
										$username= mysqli_real_escape_string($conn,$rows['username']);
										$email= mysqli_real_escape_string($conn,$rows['email']);
										$password= mysqli_real_escape_string($conn,$rows['password']);
										echo "addRow(\"$username\",\"$email\",\"$password\",\"$table\");";
								}
							}
					
		
		
		
		break;
		default:print_r("SB");
	}
	