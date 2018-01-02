<?php
  $page_title = 'All Product';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
   page_require_level(2);
   $all_categories = find_all('categories');
?>

<?php  
$sql = "SELECT id FROM products p";
  if(isset($_POST['show_part_product'])){
    $req_fields = array('product-categorie');
    validate_fields($req_fields);
    if(empty($errors)){
       $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
       $sql = "SELECT id FROM products p WHERE p.categorie_id=";
       $sql .= $p_cat;
    }
    else{
      $sql = "SELECT id FROM products p";
    }
  }
// 总记录数  

$totalnums = totalnums($sql);  
  
// 每页显示条数  
$fnum = 20;  
  
// 翻页数  
$pagenum = ceil($totalnums / $fnum);  
  
// 页数常量  
try {
  if(isset($_GET['page'])){$page = $_GET['page'];}
  else{$page = '';}
} catch (Exception $e){
  redirect('add_product.php',false);
}

  
if ( $page == '' ) {
$page = 1;
}  
//防止恶意翻页  
if ($page > $pagenum)  
    echo "<script>window.location.href='add_product.php'</script>";  
  
//计算分页起始值  
if ($page == "") {  
    $num = 0;  
} else {  
    $num = ($page-1) * $fnum;  
}   
$stime = microtime(true);
$products = join_product_table_part($num,$fnum);
$etime = microtime(true);
$total=$etime-$stime;
if(isset($_POST['show_part_product'])){
  $req_fields = array('product-categorie');
  validate_fields($req_fields);
  if(empty($errors)){
     $p_cat   = remove_junk($db->escape($_POST['product-categorie']));
     $stime = microtime(true);
     $products = join_product_table_part_categorie($p_cat,$num,$fnum);
     $etime = microtime(true);
     $total=$etime-$stime;
  }
else{
  $stime = microtime(true);
  $products = join_product_table_part($num,$fnum);
  $etime = microtime(true);
  $total=$etime-$stime;}}


?> 

<?php include_once('layouts/header.php'); ?>
  <div class="row">
     <div class="col-md-12">
       <?php echo display_msg($msg); ?>
     </div>
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <form method="post" action="product.php" class="clearfix">
          <div class="pull-left">
          <div class="col-md-12">
            <select class="form-control" name="product-categorie">
              <option value="">Select Product Category</option>
            <?php  foreach ($all_categories as $cat): ?>
              <option value="<?php echo (int)$cat['id'] ?>">
                <?php echo $cat['name'] ?></option>
            <?php endforeach; ?>
            </select>
          </div>
          </div>
        <button type="submit" name="show_part_product" class="btn btn-danger">Submit</button>
        </form>
         <div class="pull-right">
           <a href="add_product.php" class="btn btn-primary">Add New</a>
         </div>
        </div>
        <div class="panel-body">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th> Photo</th>
                <th> Product Title </th>
                <th class="text-center" style="width: 10%;"> Categorie </th>
                <th class="text-center" style="width: 10%;"> Instock </th>
                <th class="text-center" style="width: 10%;"> Buying Price </th>
                <th class="text-center" style="width: 10%;"> Saleing Price </th>
                <th class="text-center" style="width: 10%;"> Product Added </th>
                <th class="text-center" style="width: 100px;"> Actions </th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product):?>
              <tr>
                <td class="text-center"><?php echo count_id();?></td>
                <td>
                  <?php if($product['media_id'] === '0'): ?>
                    <img class="img-avatar img-circle" src="uploads/products/no_image.jpg" alt="">
                  <?php else: ?>
                  <img class="img-avatar img-circle" src="uploads/products/<?php echo $product['image']; ?>" alt="">
                <?php endif; ?>
                </td>
                <td> <?php echo remove_junk($product['name']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['categorie']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['quantity']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['buy_price']); ?></td>
                <td class="text-center"> <?php echo remove_junk($product['sale_price']); ?></td>
                <td class="text-center"> <?php echo read_date($product['date']); ?></td>
                <td class="text-center">
                  <div class="btn-group">
                    <a href="edit_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-info btn-xs"  title="Edit" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-edit"></span>
                    </a>
                    <a href="delete_product.php?id=<?php echo (int)$product['id'];?>" class="btn btn-danger btn-xs"  title="Delete" data-toggle="tooltip">
                      <span class="glyphicon glyphicon-trash"></span>
                    </a>
                  </div>
                </td>
              </tr>
             <?php endforeach; ?>
            </tbody>
          </tabel>
        </div>
      </div>
    </div>
  </div>
  <?php
  //上一页
   $pagestr="";
   $bothNum=4;
   if($page==1){
       $pagestr.='<span>首页  </span>';
   }else{
       $lastPage=$page-1;
       $pagestr.="<a href='product.php?page=1'>首页</a>"."  ";
   }
 
   if($page-$bothNum>1){
       $pagestr.="<a href='product.php?page=$lastPage'>上一页</a>";
       $pagestr.="<span>...</span>";
   }
   //当前页的左边
   for($i=$bothNum;$i>=1;$i--){
       if(($page - $i) < 1 ) { // 当前页左边花最多 bothnum 个数字
            continue;
        }
       $lastPage=$page-$i;
       $pagestr.="<a href='product.php?page=$lastPage'>$lastPage</a>"."  ";
   }
   //当前页
   $pagestr.="<span>$page</span>"."  ";
   //当前页右边
   for($i=1;$i<=$bothNum;$i++){
       if(($page + $i) > $pagenum) { // 当前页右边最多 bothnum 个数字
           break;
       }
       $lastPage=$page+$i;
       $pagestr.="<a href='product.php?page=$lastPage'>$lastPage</a>"."  ";
   
   }
   if(($page+$bothNum)<$pagenum){
       $pagestr.="<span>...</span>"."  ";
     }
  //下一页
    if($page == $pagenum) {
         $pagestr .= '<span>  下一页  </span>';
      } else {
             $nextPage=$page+1;
          $pagestr .= "<a href='product.php?page={$nextPage}'>  下一页  </a>";
      }
   //尾页
   if(($page+$bothNum)<$pagenum){
       $pagestr .= '<a href="product.php?page='.$pagenum.'">尾页</a>'."  ";
   }
   
   echo $pagestr;
   echo "<hr/>";
   echo '当前页数为：'.$page.'，总页数为：'.$pagenum;  
   echo "查询执行了",round($total,3),"秒"; 
   ?>
  <?php include_once('layouts/footer.php'); ?>
