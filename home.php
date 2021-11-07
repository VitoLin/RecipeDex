<?php
session_start();

if(!isset($_SESSION['username'])) {
	header("Location: index.php");
}
?>

<!DOCTYPE html>

<html>
	<head>
		<title>RecipeDex</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<style>
			button[name='addfav'] {
				display: inline;
			}
			.favorite button[name='addfav'] {
				display: none;
			}
			button[name='remfav'] {
				display: none;
			}
			.favorite button[name='remfav'] {
				display: inline;
			}
		</style>
        <?php include 'include/include.php' ?>
	</head>
	<body>

        <h1 style="text-align: center;">RECIPEDEX</h1>
        <div style="width:1200px; margin:0 auto;">
		<h4 style="text-align: center;">Hello, <?php echo $_SESSION['username'];?></h4>
        

		<!-- form -->
		<div class="row">
			<div class="col-xs-6">
        		<form action="home.php" method="get" autocomplete="off">
            		Search: <input type="textbox" name ="search" id ="search" value ="">
				</form>
			</div>
			<div class="col-xs-6">
				<button type="button" class="btn btn-link pull-right" onclick="location.href='login/account.php'">Account</button>
			</div>
		</div>

		
        <!-- table -->
        <table class="table table-striped table-bordered">
        <thead>
        <tr>
        <th style='width:50px;'></th>
        <th style='width:150px;'>Name</th>
        <th style='width:50px;'>Ingredients</th>
        <th style='width:50px;'>Calories</th>
        <th style='width:50px;'>Carbs</th>
        <th style='width:50px;'>Protein</th>
		<th style='width:50px;'>Fats</th>
		<th style='width:50px;'>Favorite</th>
		
        </tr>
        </thead>
        <tbody>
        
        <!-- php script to get data from mysql and display it in a table -->
        <?php 
            // pagingiation
            if (isset($_GET['page_no']) && $_GET['page_no']!="") {
                $page_no = $_GET['page_no'];
            } 
            else {
                $page_no = 1;
            }
            $total_records_per_page = 10;
            $offset = ($page_no-1) * $total_records_per_page;
            $previous_page = $page_no - 1;
            $next_page = $page_no + 1;
            $adjacents = "2"; 
            
            // connect to database
            $link = connect();

            // logic for searches, make modules for search by name, nutrition, and ingredients
            if (isset($_GET['search']) && $_GET['search']!="") {
                $search = $_GET['search'];
            } 
            else {
                $search = '';
            }
            
            // modify route to fit more wanted things
            $route = 'search='.$search;
            
            // modify query as to get a table with wanted information
            $query = 'select * from recipes where name regexp \'(.*'.$search.'.*)\' ';
            
            // get the section from the database
            $full_query = getTable($query, $total_records_per_page, $offset);
            
            // final result no need to change from below here
            $result = mysqli_query($link, $full_query);
            
            $result_count = mysqli_query($link, "select count(*) as total_records from ($query) as q;");

            $total_records = mysqli_fetch_array($result_count);
            
	        $total_records = $total_records['total_records'];
            $total_no_of_pages = ceil($total_records / $total_records_per_page);
	        $second_last = $total_no_of_pages - 1; // total page minus 1

    
            

			// Function for favorites
			function inFavorites($id, $username, $link) {
				$sql = "SELECT * FROM favorites WHERE recipe_id = '$id' AND username = '$username'";
				$result = mysqli_query($link, $sql);
				return mysqli_num_rows($result) == 0;
			}

			// what the table looks like when filled in
            while($row = mysqli_fetch_array($result)){
                $ingredients = mysqli_query($link, "select * from ingredients where id = '".$row['id']."';");
                $nutrients = mysqli_query($link, "select * from nutrients where id = '".$row['id']."';");
                echo 
                "<tr>
                    <td> <img src=".$row['image']." width=\"200\" height=\"200\" alt=\"\"> </td> 
                    <td><a href=\"".$row['url']."\" target=\"_blank\" >".$row['name']."</a> </td>";

                    // ingredients
                    echo "<td><ul>";
                    while ($ingredients_row = mysqli_fetch_array($ingredients)){
                        echo "<li>"
                        .$ingredients_row['ingredient']."</li>";
                    }
                    echo "</ul></td>";    

                    // nutrients
                    $nutrients_row = mysqli_fetch_array($nutrients);
                    if ($nutrients_row){
                        echo 
                            "<td>".$nutrients_row['calories']."</td>
                            <td>".$nutrients_row['carbohydrateContent']."</td>
                            <td>".$nutrients_row['proteinContent']."</td>
							<td>".$nutrients_row['fatContent']."</td>";
                    }
                    else{
                        echo    
                            "<td></td>
                            <td></td>
                            <td></td>
                            <td></td>";
					}

					// favorites
					if (inFavorites($row['id'], $_SESSION['username'], $link)){
						$class = "recipe";
						$btn_class = "btn btn-primary";
						$btn_text = "Add";
						$btn_name = "addfav";
					} else {
						$class = "recipe favorite";
						$btn_class = "btn btn-danger btn-xs";
						$btn_text = "Remove";
						$btn_name = "remfav";
					}
					echo "<td>
							<div class='text-center'>
							<div id='".$row['id']."' class='".$class."'>
								<button class='".$btn_class."' name='".$btn_name."'>".$btn_text."</button>
							</div>
							</div>
							</td>";
					
                    echo "</tr>";
				}



        // close connection to database
        mysqli_close($link);

		?>


		<script type="text/javascript">
			// for table
			document.getElementById("search").value = "<?php echo $search; ?>";

			// for favorites
			function favorite() {
				// get id to div
				var parent = this.parentElement;
				// create xml http request
				var xhr = new XMLHttpRequest();
				xhr.open('POST', 'favorite.php', true);
				xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
				xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
				xhr.onreadystatechange = function() {
					if(xhr.readyState == 4 && xhr.status == 200) {
						var result = xhr.responseText;
						console.log('Result: ' + result);
						if(result == 'true'){
							parent.classList.add("favorite");
							location.reload();
						} else {
							parent.classList.remove("favorite");
							location.reload();
						}
					}
				};
				xhr.send("id="+parent.id);
			}

			// listen to buttons
			var buttons = document.getElementsByName("addfav");
			for(i=0; i < buttons.length; i++){
				buttons[i].addEventListener("click", favorite);
			}
			var buttons = document.getElementsByName("remfav");
			for(i=0; i < buttons.length; i++){
				buttons[i].addEventListener("click", favorite);
			}
        </script>

<!-- pagination -->
</tbody>
</table>

<div style='padding: 10px 20px 0px; border-top: dotted 1px #CCC;'>
<strong>Page <?php echo $page_no." of ".$total_no_of_pages; ?></strong>
</div>

<ul class="pagination">
	<?php // if($page_no > 1){ echo "<li><a href='?page_no=1'>First Page</a></li>"; } ?>
	<li <?php if($page_no <= 1){ echo "class='disabled'"; } ?>>
	<a <?php if($page_no > 1){ echo "href=?".$route."&page_no=$previous_page"; } ?>>Previous</a>
	</li>
       
    <?php 
        if ($total_no_of_pages <= 10){  	 
            for ($counter = 1; $counter <= $total_no_of_pages; $counter++){
                if ($counter == $page_no) {
            echo "<li class='active'><a>$counter</a></li>";	
                    }else{
            echo "<li><a href=?".$route."&page_no=$counter>$counter</a></li>";
                    }
            }
        }
        elseif($total_no_of_pages > 10){
            
        if($page_no <= 4) {			
        for ($counter = 1; $counter < 8; $counter++){		 
                if ($counter == $page_no) {
            echo "<li class='active'><a>$counter</a></li>";	
                    }else{
            echo "<li><a href='?".$route."&page_no=$counter'>$counter</a></li>";
                    }
            }
            echo "<li><a>...</a></li>";
            echo "<li><a href='?".$route."&page_no=$second_last'>$second_last</a></li>";
            echo "<li><a href='?".$route."&page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";
            }

        elseif($page_no > 4 && $page_no < $total_no_of_pages - 4) {		 
            echo "<li><a href='?".$route."&page_no=1'>1</a></li>";
            echo "<li><a href='?".$route."&page_no=2'>2</a></li>";
            echo "<li><a>...</a></li>";
            for ($counter = $page_no - $adjacents; $counter <= $page_no + $adjacents; $counter++) {			
            if ($counter == $page_no) {
            echo "<li class='active'><a>$counter</a></li>";	
                    }else{
            echo "<li><a href='?".$route."&page_no=$counter'>$counter</a></li>";
                    }                  
        }
        echo "<li><a>...</a></li>";
        echo "<li><a href='?".$route."&page_no=$second_last'>$second_last</a></li>";
        echo "<li><a href='?".$route."&page_no=$total_no_of_pages'>$total_no_of_pages</a></li>";      
        }
        
        else {
        echo "<li><a href='?".$route."&page_no=1'>1</a></li>";
        echo "<li><a href='?".$route."&page_no=2'>2</a></li>";
        echo "<li><a>...</a></li>";

        for ($counter = $total_no_of_pages - 6; $counter <= $total_no_of_pages; $counter++) {
        if ($counter == $page_no) {
        echo "<li class='active'><a>$counter</a></li>";	
                }else{
        echo "<li><a href='?".$route."&page_no=$counter'>$counter</a></li>";
                }                   
                }
            }
        }
    ?>
    <li <?php if($page_no >= $total_no_of_pages){ echo "class='disabled'"; } ?>>
    <a <?php if($page_no < $total_no_of_pages) { echo "href='?".$route."&page_no=$next_page'"; } ?>>Next</a>
    </li>
    <?php if($page_no < $total_no_of_pages){
        echo "<li><a href='?".$route."&page_no=$total_no_of_pages'>Last &rsaquo;&rsaquo;</a></li>";
        } ?>
    </ul>

    </body>
</html>

