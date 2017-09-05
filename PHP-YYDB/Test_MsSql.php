<?php
$incPath = dirname(__FILE__);
require_once "{$incPath}/system/db/SQLHelper.php";   ///////////数据库操作类

$SQLHelper = new SQLHelper();
 $sql ="select * from  Accounts_Users where userid=4";
 /*$result=$SQLHelper->GetFill($sql);  */

 $result=$SQLHelper->getRow($sql);

if ($result!=null) {
	echo $result['UserName'];
}else{
     echo '结果是null';
}


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

?>