<?php
  $page_title = 'Monthly Sales';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(3);
?>
<?php
 $year = date('Y');
 
?>
<?php  
  
// 总记录数  
$sql  = "SELECT s.product_id,p.name,SUM(s.qty) as qty, ";
$sql .= "SUM(p.sale_price * qty) AS total_saleing_price,";
$sql .= " DATE_FORMAT(s.date,'%Y-%m') as date ";
$sql .= "FROM sales s LEFT JOIN products p ON s.product_id = p.id ";
$sql .= " WHERE DATE_FORMAT(s.date, '%Y' ) = '{$year}'";
$sql .= "GROUP BY DATE_FORMAT(s.date,'%Y-%m'), s.product_id ";
$sql .= "ORDER BY s.product_id, date ASC";
$totalnums = totalnums($sql);  
  
// 每页显示条数  
$fnum = 20;  
  
// 翻页数  
$pagenum = ceil($totalnums / $fnum);  
  
// 页数常量  
$page = $_GET['page'];  
if ( $page == '' ) {
$page = 1;
}  
//防止恶意翻页  
if ($page > $pagenum)  
    echo "<script>window.location.href='daily_sales.php'</script>";  
  
//计算分页起始值  
if ($page == "") {  
    $num = 0;  
} else {  
    $num = ($page-1) * $fnum;  
}
$stime = microtime(true);
//这里写执行代码
$sales = monthly_SalesPart($year,$num,$fnum);
$etime = microtime(true);
$total=$etime-$stime;
?> 
<?php include_once('layouts/header.php'); ?>
<div class="row">
  <div class="col-md-6">
    <?php echo display_msg($msg); ?>
  </div>
</div>
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <strong>
            <span class="glyphicon glyphicon-th"></span>
            <span>Monthly Sales</span>
          </strong>
        </div>
        <div class="panel-body">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Product name </th>
                <th class="text-center" style="width: 15%;"> Quantity sold</th>
                <th class="text-center" style="width: 15%;"> Total </th>
                <th class="text-center" style="width: 15%;"> Date </th>
             </tr>
            </thead>
           <tbody>
             <?php foreach ($sales as $sale):?>
             <tr>
               <td class="text-center"><?php echo count_id();?></td>
               <td><?php echo remove_junk($sale['name']); ?></td>
               <td class="text-center"><?php echo (int)$sale['qty']; ?></td>
               <td class="text-center"><?php echo remove_junk($sale['total_saleing_price']); ?></td>
               <td class="text-center"><?php echo $sale['date']; ?></td>
             </tr>
             <?php endforeach;?>
           </tbody>
         </table>
        </div>
      </div>
    </div>
  </div>

<?php
  //上一页
   $pagestr="";
   $bothNum=4;
   if($page==1){
       $pagestr.='<span>首页</span>';
   }else{
       $lastPage=$page-1;
       $pagestr.="<a href='monthly_sales.php?page=1'>首页</a>"."  ";
   }
 
   if($page-$bothNum>1){
       $pagestr.="<a href='monthly_sales.php?page=$lastPage'>上一页</a>";
       $pagestr.="<span>...</span>";
   }
   //当前页的左边
   for($i=$bothNum;$i>=1;$i--){
       if(($page - $i) < 1 ) { // 当前页左边花最多 bothnum 个数字
            continue;
        }
       $lastPage=$page-$i;
       $pagestr.="<a href='monthly_sales.php?page=$lastPage'>$lastPage</a>"."  ";
   }
   //当前页
   $pagestr.="<span>$page</span>"."  ";
   //当前页右边
   for($i=1;$i<=$bothNum;$i++){
       if(($page + $i) > $pagenum) { // 当前页右边最多 bothnum 个数字
           break;
       }
       $lastPage=$page+$i;
       $pagestr.="<a href='monthly_sales.php?page=$lastPage'>$lastPage</a>"."  ";
   
   }
   if(($page+$bothNum)<$pagenum){
       $pagestr.="<span>...</span>"."  ";
     }
  //下一页
    if($page == $pagenum) {
         $pagestr .= '<span>下一页  </span>';
      } else {
             $nextPage=$page+1;
          $pagestr .= "<a href='monthly_sales.php?page={$nextPage}'>下一页  </a>";
      }
   //尾页
   if(($page+$bothNum)<$pagenum){
       $pagestr .= '<a href="monthly_sales.php?page='.$pagenum.'">尾页</a>'."  ";
   }
   
   echo $pagestr;
   echo "<hr/>";
   echo '当前页数为：'.$page.'，总页数为：'.$pagenum;
   echo "查询执行了{$total}秒";    
   ?>
<?php include_once('layouts/footer.php'); ?>
