<?php
  define( 'DB_HOST', 'localhost' );          // Set database host
  define( 'DB_USER', 'root' );             // Set database user
  define( 'DB_PASS', '2945377348Qzb' );             // Set database password
  define( 'DB_NAME', 'hw1' );        // Set database name
 
function conn()  
{  
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);  
    mysqli_query($conn, "set names utf8");  
    return $conn;  
}  
  
//获得结果集  
function doresult($sql){  
   $result=mysqli_query(conn(), $sql);  
   return  $result;  
}  
  
//结果集转为对象集合  
function dolists($result){  
    return mysqli_fetch_array($result, MYSQL_ASSOC);  
}  
  
function totalnums($sql) {  
    $result=mysqli_query(conn(), $sql);  
    return $result->num_rows;  
}  
  
  
  
  
// 关闭数据库  
function closedb()  
{  
    if (! mysqli_close()) {  
        exit('关闭异常');  
    }  
}  
?>
