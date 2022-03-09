<?php include("includes/header.php"); ?>

<?php
//  $photos= Photo::find_all(); 
 ?>

<?php
    $page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;
    $items_per_page =5;
    $items_total_count = Photo::count_all();

    $paginate = new Paginate($page, $items_per_page, $items_total_count);

    $sql  = "SELECT * FROM photos ";
    $sql .= "LIMIT {$items_per_page} ";
    $sql .= "OFFSET {$paginate ->offset()}";

    $photos = Photo::find_this_query($sql);


?>

    <div class="container">

        <div class="row">

            <!-- Blog Entries Column -->
            <div class="col-lg-12">

            <div class="row thumbnails">
            <?php foreach ($photos as $photo) :?>
                <a href="photo.php?id=<?php echo $photo->id; ?>">
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    <div class="thumbnail" style="height:280px; overflow:hidden;">
                        
                            <img class="main-page-image" src="admin/<?php echo $photo->picture_path(); ?>" alt="" >
                        <div class="caption" >
                            <h1><?php echo $photo->title; ?></h1>
                            <p style="overflow:hidden;">
                                <?php echo $photo->caption; ?>
                            </p>
                        </div>
                    </div>
                </div>
                </a>



                

                <!-- 0000000000000 -->
                
                
<!-- function get_products(){
    $query = "SELECT * FROM products";
    $query = query($query);
    confirm($query);
    while ($row = fetch_array($query)) {
        $product = <<< DELIMETER
        <div class="col-sm-4 col-lg-4 col-md-4">
        <div class="thumbnail">
            <a href="item.php?id={$row['product_id']}"> <img src="{$row['product_image']}" alt=""> </a>
            <div class="caption">
                <h4 class="pull-right">&#36;{$row['product_price']}</h4>
                <h4><a href="item.php?id={$row['product_id']}">{$row['product_title']}</a>
                </h4>
                <p>See more snippets like this online store item at <a target="_blank" href="http://www.bootsnipp.com">Bootsnipp - http://bootsnipp.com</a>.</p>
                <a class="btn btn-primary" target="_blank" href="item.php?id={$row['product_id']}">Add to cart</a>
            </div>

            
        </div>
    </div>
DELIMETER;
echo $product;
    }
} -->

                
                <!-- 000000 -->

                <?php endforeach; ?>
                </div>
            
                </div>
            <!-- Blog Sidebar Widgets Column -->
            <!-- <div class="col-md-4">
            -->
                  
        </div>
        <!-- /.row -->
        <div class="row">
            <ul class="pagination">
                <?php 
                    if ($paginate->page_total() > 1) {
                        if ($paginate->has_next()) {
                            echo " <li class='next'> <a href='index.php?page={$paginate->next_page()}'>Next</a></li>";
                        }

                    ?>
                    <?php
                        for ($i=1; $i <=$paginate->page_total() ; $i++) { 
                            if ($i == $paginate->current_page) {
                                
                                echo " <li class= 'active'><a href='index.php?page={$i}'>${i}</a></li>";
                            }
                            else{
                                echo " <li><a href='index.php?page={$i}'>${i}</a></li>";
                            }
                        }

                        if($paginate->has_previous()){
                            echo " <li class='previous'> <a href='index.php?page= {$paginate->previous_page()}'>Previous</a></li>";
                        }
                    }

                  
                ?>
                
                
            </ul>
        </div>
       
        

        </div>

        
        <?php include("includes/footer.php"); ?>
        