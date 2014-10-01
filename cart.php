<?php
$title = 'Корзина';
require_once 'chunks/head.php';
require_once 'chunks/menu.php';
$hash = get_hash_for_sql();
Yii::app()->db->createCommand("DELETE FROM cart WHERE created < DATE_SUB(NOW(), INTERVAL 2 DAY)")->execute();
?>
<div class="container">
	<div class="page-header">
		<div class="row">
			<div class="col-md-10">
				<h1>Корзина</h1>
			</div>
			<div class="col-md-2">
				<a href="index.php" class="pull-right">&laquo; Вернуться в каталог</a>
			</div>
		</div>
	</div>
	<?php
	$rows = Yii::app()->db->createCommand("
				SELECT *, cart.id as id FROM cart 
					INNER JOIN dress ON (cart.dress_id = dress.id)
				WHERE hash = {$hash}
				ORDER BY cart.id")->queryAll();
	if(!empty($rows)) { ?>
	<div id="cart-wrapper">
		<p>
			Позиций: <span class="badge" id="cart-count"><?php echo Yii::app()->db->createCommand("SELECT COUNT(id) FROM cart WHERE hash = {$hash}")->queryScalar() ?></span>,
			Футболок: <span class="badge" id="cart-sum"><?php echo (int)Yii::app()->db->createCommand("SELECT SUM(amount) FROM cart WHERE hash = {$hash}")->queryScalar() ?></span>
		</p>
		<p>
			<button type="button" class="btn btn-info btn-order">
				<span class="glyphicon glyphicon-heart"></span> Оформить заказ
			</button>
		</p>
		<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover">
		  		<thead>
		  			<tr>
						<th>Рисунок</th>
						<th style="white-space: nowrap;">№ Футболки</th>
						<th>Артикул</th>
						<th>Надпись</th>
						<th>Ссылка на группу</th>
						<th>Размер <a href="help.php"><span class="glyphicon glyphicon-flag"></span></a></th>
						<th>Цена, руб.</th>
						<th>Количество</th>
						<th>Сумма, руб.</th>
						<th>Обновить / удалить позицию</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$total = 0;
					foreach ($rows as $k => $row) { 
						$total += $row['price'] * $row['amount'];
					?>
					<tr>
						<td>
							<a href="images/gallery/<?php echo $row['dress_id'] ?>.png" class="dress-image zoom-in" title="<?php echo CHtml::encode($row['name']) ?>">
								<img class="img-responsive" src="images/gallery/thumbs/<?php echo $row['dress_id'] ?>.png" alt="" width="150"><span class="glyphicon glyphicon-zoom-in"></span>
							</a>
						</td>
						<td><?php echo $row['dress_id'] ?></td>
						<td><?php echo CHtml::encode($row['name']) ?></td>
						<td id="cart-inscription-<?php echo $row['id'] ?>">
							<div>
							<?php if(!empty($row['inscription'])) {
								echo nl2br(CHtml::encode($row['inscription']));
							} else { 
								echo '&#8211;';
							} ?>
							</div>
							<a href="#">
								<span class="glyphicon glyphicon-pencil"></span> 
							</a>
						</td>
						<td>
							<div class="form-group">
								<div class="checkbox">
									<label>
										<input type="checkbox" id="cart-item-printpromolink-<?php echo $row['id'] ?>"<?php echo $row['printpromolink'] ? ' checked="checked"' : ''?>>
									</label>
								</div>
							</div>
						</td>
						<td>
							<div class="form-group">
								<select class="form-control" id="cart-item-size-<?php echo $row['id'] ?>">
									<?php foreach (get_size_array() as $value) {?>
										<option<?php echo $row['size'] == $value ? ' selected="selected"' : '' ?>><?php echo $value ?></option>
									<?php } ?>
								</select>
							</div>
						</td>
						<td class="text-right" id="cart-item-price-<?php echo $row['id'] ?>">
							<div><?php echo price_format($row['price']) ?></div>
							<?php echo $row['printpromolink'] ? '<span class="small label label-default">-10%</span>' : '' ?>
						</td>
						<td>
							<div class="form-group">
								<input class="form-control cart-item-amount" type="number" id="cart-item-amount-<?php echo $row['id'] ?>" placeholder="0" min="1" step="1" value="<?php echo $row['amount'] ?>">
							</div>
						</td>
						<td class="text-right" id="cart-item-price-amount-<?php echo $row['id'] ?>"><?php echo price_format($row['price'] * $row['amount']) ?></td>
						<td>
							<form class="form-inline" role="form">
								<div class="form-group">
									<button class="btn btn-sm btn-primary" type="button" id="cart-idu-<?php echo $row['id'] ?>">
										<span class="glyphicon glyphicon-pencil"></span> Обновить
									</button>
									<button class="btn btn-sm btn-danger" type="button" id="cart-id-<?php echo $row['id'] ?>">
										<span class="glyphicon glyphicon-remove"></span> Удалить
									</button>
								</div>
							</form>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="9" class="text-right"><b>Итого</b></td>
						<td><b id="cart-total"><?php echo price_format($total) ?></b><b> руб.</b></td>
					</tr>
		  		</tbody>
			</table>
		</div>
		<p><span class="label label-warning">Внимание!</span> Уважаемый покупатель, если Вы редактировали позиции, то убедитесь, пожалуйста, что вы нажимали кнопку «<span class="glyphicon glyphicon-pencil"></span> Обновить» напротив каждой отредактированной позиции и если неуверены нажмите ее еще раз. При нажатии этой кнопки параметры футболки, которую вы хотите купить редактируются на сервере, чтобы при переходе на страницу <a href="order.php">оформления заказа</a> они не потерялись.</p>
		<p>
			<button type="button" class="btn btn-info btn-order pull-right">
				<span class="glyphicon glyphicon-heart"></span> Оформить заказ
			</button>
		</p>
		<?php if(count($rows) > 5) { ?>
		<div class="text-center">
	    	<a href="#top" class="label label-default" id="back-to-top">Перейти наверх</a>
	    </div>
	    <?php } ?>
	</div>
	<br><br>
	<?php } ?>
	<div class="alert alert-info"<?php echo !empty($rows) ? ' style="display: none;"' : ''?>>В Вашей корзине пока пусто. Перейдите, пожалуйста, в <a href="/<?php echo get_base_url($page); ?>" style="white-space: nowrap;"><span class="glyphicon glyphicon-link"></span> каталог</a> для выбора футболки, которая Вам понравится и положите ее в Вашу корзину нажав на кнопку «Положить в корзину» рядом с каждой футболкой.</div>
</div>
<!-- Modal cart -->
<div class="modal fade" id="modalCartItem" tabindex="-1" role="dialog" aria-labelledby="modalCartItemLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalCartItemLabel">Редактирование надписи на футболке</h4>
			</div>
			<div class="modal-body">
				<textarea class="form-control" placeholder="Ваша надпись на футболке"></textarea><br>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php require_once 'chunks/footer.php' ?>