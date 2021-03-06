<?php
$order_id = 0;

$connection = Yii::$app->db;
$transaction = $connection->beginTransaction();
try
{
	$command = $connection->createCommand("
		INSERT INTO `order` (
			fio, email, phone, comment, created
		)
		VALUES (
			:fio,
			:email,
			:phone,
			:comment,
			" . new \yii\db\Expression('NOW()') . "
		)
	");
	$command->bindParam(":fio", $model->fio, PDO::PARAM_STR);
	$command->bindParam(":email", $model->email, PDO::PARAM_STR);
	$command->bindParam(":phone", $model->phone, PDO::PARAM_STR);
	$command->bindParam(":comment", $model->text, PDO::PARAM_STR);
	$command->execute();
	$order_id = $connection->lastInsertID;
	
	$command = $connection->createCommand("
		INSERT INTO order_items (
			order_id, item_id, name, announcement, description, price, size, amount, inscription, printpromolink
		)
		SELECT
			{$order_id}, item_id, name, announcement, description, price, size, amount, inscription, printpromolink
		FROM
			cart INNER JOIN
				item ON (cart.item_id = item.id)
		WHERE
			hash = {$hash}
	")->execute();
	
	$connection->createCommand("DELETE FROM cart WHERE hash = {$hash}")->execute();
	
	$transaction->commit();
}
catch(Exception $e) 
{
	$transaction->rollback();
}
