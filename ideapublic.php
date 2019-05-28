<?php
require_once 'core/init.php';
require_once'functions/functions.php';
acessLog($absolute_url);

if(isset($_GET['id'])) {
		$post_id = $_GET['id'];
		
		$newPost = new Post();
		if($newPost->findPost($post_id)){ 
		$posts = $newPost->findPost($post_id);
		$array = json_decode(json_encode($posts), True);
		
		$newUser = new User();
		$newUser -> find($newPost->data()->user_id);
		
		$string = $newPost->data()->idea;
		} else{
		$string = "Nenhuma ideia encontrada";
	}
}
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
	<meta charset="utf-8"/>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport">
	<meta name="description" content="<?=$string;?>">
	<meta name="keywords" content="<?php echo str_replace(" ",", ", $string);?>">
	<meta name="author" content="Julião F. Kataleko">
	<title><?=$string;?> - Comentários</title>
	<link href="includes/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link rel = "stylesheet" type = "text/css" href = "includes/assets/css/home.css" />
	<link rel="icon" href="includes/img/logo.png" sizes="57x57" type="image/png">
</head>
<body>


<?php include 'includes/html/header.html';?>

<div class="content">
<?php
	if(isset($_GET['id'])) {
		$post_id = $_GET['id'];
		
		$newPost = new Post();
		if($newPost->findPost($post_id)){ 
		$posts = $newPost->findPost($post_id);
		$array = json_decode(json_encode($posts), True);
		
		$newUser = new User();
		$newUser -> find($newPost->data()->user_id);
		
		$read = new Read();
		
		$read_array = array('post_id' => $newPost->data()->post_id);
		
		$read -> countRead($read_array);
		
		$read -> findReads($post_id);
		
		$string = $newPost->data()->idea;
		?>
		<div style="border:#26a88a 6px solid; padding:10px; margin-bottom:10px; border-radius:0px;">
		<p><?php echo convertLink($string);?></p>
		<p><a href="profile?user=<?php echo $newUser->data()->user_id;?>"><?php echo $newUser->data()->name;?></a></p>
		
		<?php if($newPost->data()->details !== "") { ?>
		<p style="border:1px solid #ddd; padding:10px;"><?php echo $newPost->data()->details;?></p><hr/>
		<?php } ?>
		</div>
		<hr>
		
		<?php
		
		if(Session::exists('success')){
			echo "<div class='alert-success'>" . Session::flash('success') . "</div>";
		}

		if(Session::exists('error')){
			echo "<div class='alert-error'>" . Session::flash('error') . "</div>";
		}
		echo $read->_db->count() . " leituras, ";
		
		$comment = new Comment();
		if($comment->findComment($post_id)){
			echo $comment->_db->count()." Comentários<hr>";
			
			$where 	= array('post_id', '=', $newPost->data()->post_id);
			$comments 	= $comment->commentsList('tbl_comments', $where);
			$array 		= json_decode(json_encode($comments), True);
				
			$pag 		= new Pagination();
			$data 		= array_reverse($array);
			$numbers 	= $pag->paginate($data, 5);
			$result 	= $pag->fetchResult();
			
			foreach($result as $r): 
			
			$commentUser = new User($r['user_id']);
			$commentUser -> find($r['user_id']);
			
			?>
				<a href="profile?user=<?php echo $commentUser->data()->username;?>">
				<?php echo ucwords($commentUser->data()->name); ?></a><br/> 
			<?php	
			
			$string = $r['comment'];
			$comment_id = $r['comment_id'];
			echo convertLink($string) . "";
			
			echo "<hr/>";
			endforeach; ?>
				
				
			 <ul class="pagination justify-content-center">
			 <?php
			 foreach($numbers as $num) {
				echo"<li class='page-item'><a class='page-link' href='ideapublic?id=$post_id&page=$num'>" . $num . "</a><li>";
			}
			 ?>
			</ul>
			
			<?php	
			} else {
					echo "Ainda não há comentários. seja o primeiro <a href='login'>Entrar</a>";
			}
			
			
			
			
	} else{
		echo "nenhuma publicação encontrada.";
	}
}
?>
</div>
<?php include 'includes/html/footer.html';?>