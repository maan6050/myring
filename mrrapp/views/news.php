<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>">Home</a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader">
			<h3><?= $title; ?></h3>
			<a href="<?= base_url('content/newsCreateForm'); ?>">Create news</a>
			<div class="clear"></div>
		</div>
		<div class="searchDiv">
			<form name="search" id="search" action="<?= base_url('content/newsList'); ?>" method="post">
				<input type="text" id="searchNews" name="searchNews" value="<? if(isset($searchNews)) echo $searchNews; ?>" placeholder="Search by title or content">
				<input type="submit" value="Search">
			</form>
		</div>
		<table cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Date</th><th>Title</th><th>Image</th><th>Actions</th>
				</tr>
			</thead>
			<tbody><?
				if(count($news) > 0)
				{
					foreach($news as $n)
					{ ?>
						<tr>
							<td><?= $n->created; ?></td>
							<td><?= $n->title; ?></td>
							<td><? if($n->image != '') { ?><img src="<?= base_url(UPLOADS.$n->image); ?>" height="50"><? } ?></td>
							<td class="actionsTd">
								<a href="<?= base_url('content/newsEditForm/'.$n->id); ?>"><i class="fa fa-refresh" aria-hidden="true"></i>Update</a>
								<a href="#" data-id="<?= $n->id; ?>" class="deleteItem"><i class="fa fa-times" aria-hidden="true"></i>Delete</a>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr>
						<td align="center" colspan="4">No news found.</td>
					</tr><?
				} ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>

	jQuery(document).ready(function($){
		$(document).on('click', '.deleteItem', function() {
			if(confirm('Are you sure you want to remove this news?')){
				window.location = '<?= base_url('content/newsDelete/'); ?>' + $(this).attr('data-id');
			}
		});
	});
</script>