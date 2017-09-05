<?php

/*define('BASE_PATH',str_replace('system/model/', '', str_replace('\\','/',realpath(dirname(__FILE__).'/'))."/"));

echo BASE_PATH;

require_once BASE_PATH."./system/db/SQLHelper.php";   ///////////数据库操作类

$SQLHelper = new SQLHelper();
 $sql ="select * from  Accounts_Users where userid=4";

 $result=$SQLHelper->getRow($sql);

if ($result!=null) {
	echo $result['UserName'];
}else{
     echo '结果是null';
}*/
/*echo function_exists('iconv') ? 'yes' : 'no'; //看看是否存在这个函数,如果 no , 那麼 打开PHP配置文件(php.ini) 找到extension=php_mbstring.dll , 把前面的,去掉*/

/*var_dump( $result );   */

  // 使用方法:以PDO::FETCH_NUM形式获取索引并遍历
/*echo '<table border="1" align="center" width=90%>';
    echo '<caption><h1>联系人信息表</h1></caption>';
    echo '<tr bgcolor="#cccccc">';
    echo '<th>UID</th><th>姓名</th><th>联系地址</th><th>联系电话</th><th>电子邮件</th></tr>';
   foreach($result as $row){
        echo '<tr>';
        echo '<td>'.$row['UserName'].'</td>';
        echo '<td>'.$row['NickName'].'</td>';
        echo '</tr>';
    }
 echo '</table>';*/
/*echo iconv('utf-8','gb2312//ignore',$result[4]['NickName']);

echo count($result);*/
/*var_dump($result);*/

 /*while ( $row = $result->fetch( PDO::FETCH_ASSOC ) ){   
      var_dump( $row );   
   }  */

$url='http://localhost:8059/ida/account/dologin?u=18068671606&p=1'; 
$html=file_get_contents($url); 
//print_r($http_response_header); 
/*echo($html); 
*/
 
/*php里面有2个函数：json_encode 和 json_decode
 
查一下手册就可以解决了。
 
json_decode($str, true) 可以得到数组，第二参数不加默认为false，得到对象。*/
$objInfo=json_decode($html, true) ;

/*echo($objInfo["STATUS"]);
echo "<br />";
echo($objInfo["DATA"]);*/

if($objInfo["STATUS"]=='OK'){
  /*echo( json_decode($objInfo["DATA"],true));*/
 /* echo( json_decode($objInfo["DATA"],false));*/
  echo $objInfo["DATA"]["userName"];
}else{
  echo "失败了";
}
?>