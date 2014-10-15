<?php //defined ('MICRODATA') or exit ( 'Forbidden Access' ); ?>

<div id="content-header">
	<h1>Trash</h1>
</div> <!-- #content-header -->	

<div id="content-container">

<h3 class="heading">Removed Article List</h3>

<div class="row">

	<div class="col-md-12">

		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover table-highlight table-checkable" data-provide="datatable" 
								data-display-rows="10"
								data-info="true"
								data-search="true"
								data-length-change="true"
								data-paginate="true">
			<thead>
			
			<tr>
				<th>No</th>
				<th data-sortable="true">Title</th>
				<th data-sortable="true">Category</th>
				<th data-sortable="true">Post Date</th>
				<th data-sortable="true">Expire Date</th>
				<th data-sortable="true">Status</th>
				<th class="text-center">Actions</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$no = 1;
			if($data){
				foreach($data as $key => $value){
				$expire = strtotime($value['expiredate']);
				if(!empty($expire)) $expire = date('d-m-Y',strtotime($expire));
			?>
			<tr>
				<td><?=$no++?></td>
				<td><?=$value['title']?></td>
				<td><?=$value['category']?></td>
				<td><?=date('d-m-Y',strtotime($value['postdate']));?></td>
				<td><?=$expire;?></td>
				<td><?=$value['n_status']==1 ? '<label style="color:green">Publish</label>' : '<label style="color:red">Unpublish</label>';?></td>
				<!--<td><?=($value['authorid']==$_SESSION['user']['id'] ? $_SESSION['user']['nickname'] : '')?></td>-->

				<td class="text-center" width="15%">
					<a href="<?=$basedomain?>article/articlerest/?id=<?=$value['id']?>" onclick="return confirm('Restore Data?');"><button class="btn btn-xs ui-tooltip btn-info" data-toggle="tooltip" data-placement="left" title="Restore">Restore</i></button></a>&nbsp;
					<a href="<?=$basedomain?>article/articledelp/?id=<?=$value['id']?>" onclick="return confirm('Data akan dihapus secara permanen. Lanjutkan?');"><button class="btn btn-xs ui-tooltip btn-primary" data-toggle="tooltip" data-placement="top" title="Delete Permanently">Delete</i></button></a>
					</td>
			</tr>
			<?php }} ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="8"></td>
			</tr>
			</tfoot>
		</table>
		</div>
	</div>
</div>
</div> <!-- /#content-container -->