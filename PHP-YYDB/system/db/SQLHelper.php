<?php
class SQLHelper{

 function togbk($str){
    $encode =mb_detect_encoding($str, array("ASCII","GB2312","UTF-8","GBK","BIG5"));
        $str = iconv($encode,"GBK//IGNORE",$str);
    return ($str);
}

   function GetFill($strSQL){
       
  /*     $serverName = "USER-20160930SK\SQLEXPRESS2008";  
$database = "GCShop";  
$uid = "sa";  
$pwd = "zxh"; */  

  if(!defined('APP_ROOT_PATH')) 
      define('APP_ROOT_PATH', str_replace('system/db/SQLHelper.php', '', str_replace('\\', '/', __FILE__)));

//echo APP_ROOT_PATH;
   // 加载数据库中的配置与数据库配置
       if(file_exists(APP_ROOT_PATH.'public/db_config_mssql.php'))
       {
           $db_config  = require APP_ROOT_PATH.'public/db_config_mssql.php';
        }   

   /* try {  
      $conn = new PDO("sqlsrv:server=$serverName;Database = $database", $uid, $pwd);   
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );   
   }  
   catch( PDOException $e ) {  
      echo ($e->message);  
      die( "Error connecting to SQL Server" );   
   }  */

 $serverName = $db_config['DB_HOST'];  
          $database =$db_config['DB_NAME'];  
          $uid = $db_config['DB_USER'];  
          $pwd =$db_config['DB_PWD'];   

    /*$conn = odbc_connect("Driver={SQL Server};Server=$serverName;Database=$database;", $uid,$pwd ) or die("连接失败");

 if (($res = odbc_exec($conn, $strSQL)))
      {
      $results=Array();
      while(( $rows=odbc_fetch_array($res))!=FALSE)
      {
         $results[]=$rows;
      }
      }
    odbc_close($conn);
  
    return $results;*/


    try {  
      $conn = new PDO("sqlsrv:server=$serverName;Database = $database", $uid, $pwd);   
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );   
   }  
   catch( PDOException $e ) {  
      echo ($e->message);  
      $conn = null;   
      die( "Error connecting to SQL Server" );   
   }  

    //使用query方式执行SELECT语句，建议使用prepare()和execute()形式执行语句
    $stmt = $conn->prepare($strSQL);
    $stmt->execute();
//或者使用query方式执行SELECT语句
  /*$stmt = $conn->query( $strSQL );   */


  /* while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ){   
      var_dump( $row );   
   }  */
  
  //以关联下标从结果集中获取所有数据
  //以PDO::FETCH_NUM形式获取索引并遍历
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

   // 使用方法:以PDO::FETCH_NUM形式获取索引并遍历
 /*  foreach($result as $row){
        echo '<tr>';
        echo '<td>'.$row['uid'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['$address'].'</td>';
        echo '<td>'.$row['phone'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '</tr>';
    }*/

  //以下是在fetchAll()方法中使用两个特别参数的演示示例
   /* $stmt->execute();
    $row = $stmt->fetchAll(PDO::FETCH_COLUMN,1);   //从结果集中获取第二列的所有值
    echo '所有联系人的姓名：';
    print_r($row);*/

   // Free statement and connection resources.   
   $stmt = null;   
   $conn = null;   

return $result;
   }

   function getRow($strSQL){
    if(!defined('APP_ROOT_PATH')) 
      define('APP_ROOT_PATH', str_replace('system/db/SQLHelper.php', '', str_replace('\\', '/', __FILE__)));

//echo APP_ROOT_PATH;
   // 加载数据库中的配置与数据库配置
       if(file_exists(APP_ROOT_PATH.'public/db_config_mssql.php'))
       {
           $db_config  = require APP_ROOT_PATH.'public/db_config_mssql.php';
        }   

 $serverName = $db_config['DB_HOST'];  
          $database =$db_config['DB_NAME'];  
          $uid = $db_config['DB_USER'];  
          $pwd =$db_config['DB_PWD'];   

    try {  
      $conn = new PDO("sqlsrv:server=$serverName;Database = $database", $uid, $pwd);   
      $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );   
   }  
   catch( PDOException $e ) {  
      echo ($e->message);  
      $conn = null;   
      die( "Error connecting to SQL Server" );   
   }  

//或者使用query方式执行SELECT语句
  $stmt = $conn->query( $strSQL );   
  
  //以关联下标从结果集中获取所有数据
  //以PDO::FETCH_NUM形式获取索引并遍历
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
   // Free statement and connection resources.   
   $stmt = null;   
   $conn = null;   
   
if(count($result)<=0){
  return null;
}
return $result[0];
  }
}
?>