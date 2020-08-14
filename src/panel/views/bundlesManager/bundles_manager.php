<div class="container">
	<div class="view_panel">
    	<h1 class="view_header">Bundles</h1>
    	<div class="view_content">
            <?php if (count($bundles) == 0): ?>
            	<h2>There are no bundles</h2>
            <?php else: ?>
            	<a class="btn_theme" href="<?php BASE_URL; ?>courses/add">Add</a>
                <table class="table table-hover table-stripped text_centered">
                	<thead>
                		<tr>
                			<th></th>
                    		<th>Name</th>
                    		<th>Description</th>
                    		<th>Students</th>
                    		<th>Actions</th>
                		</tr>
                	</thead>
                	<tbody>
                		<?php foreach($bundles as $bundle): ?>
                    		<tr>
                    			<?php if (empty($bundle->getLogo())): ?>
                    				<td class="course_logo"><img class="img img-responsive" src="<?php echo BASE_URL."../assets/img/default/noImage"; ?>" /></td>
                    			<?php else: ?>
                    				<td class="course_logo"><img class="img img-responsive" src="<?php echo BASE_URL."../assets/img/logos/bundles/".$bundle->getLogo(); ?>" /></td>
                    			<?php endif; ?>
                    			<td><a href="<?php echo BASE_URL."courses/edit/".$bundle->getId(); ?>"><?php echo $bundle->getName(); ?></a></td>
                    			<td><?php echo $bundle->getDescription(); ?></td>
                    			<td><?php echo $bundle->getTotalStudents(); ?></td>
                    			<td class="actions">
                    				<a class="btn_theme" href="<?php echo BASE_URL."courses/edit/".$bundle->getId(); ?>">Edit</a>
                    				<a class="btn_theme btn_theme_danger" href="<?php echo BASE_URL."courses/delete/".$bundle->getId(); ?>">Delete</a>
                				</td>
                    		</tr>
                		<?php endforeach; ?>
                	</tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>