<?php

class importProductTask extends sfBaseTask
{
	protected function configure()
	{
		// // add your own arguments here
		$this->addOptions(array(
				new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'TIM211 Course Project - Product Trading'),
				new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
				new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
				// add your own options here
				new sfCommandOption('group-product-file', null, sfCommandOption::PARAMETER_REQUIRED, 'group product information', null)
				
		));

		$this->namespace        = '';
		$this->name             = 'importProduct';
		$this->briefDescription = 'import product generated by R into database';
		$this->detailedDescription = <<<EOF
The [importProduct|INFO] task does things.
Call it with:

  [php symfony importProduct|INFO]
EOF;
	}

	protected function generatePlayers($numPlayers){
		/// add a group to the database. A token is assigned to each group
		for($i = 0; $i < $numPlayers; $i++){
			$player = new Player();
			$player->setToken(md5(time()));			
			$player->save();
			$player->setName("group_" . $player->getId());
			$player->save();
			sleep(1);
		}
	}

	protected function generateProducts($numProducts){
		for($i = 0; $i < $numProducts; $i++){
			$product = new Product();
			$product->save();
		}
	}
	

	
	protected function execute($arguments = array(), $options = array())
	{
		// initialize the database connection
		$databaseManager = new sfDatabaseManager($this->configuration);
		$connection = $databaseManager->getDatabase($options['connection'])->getConnection();
		$groupProductFile = $options["group-product-file"];
		if(!file_exists($groupProductFile)){
			echo "file not exist, please check\n" ;
			return;
		}
		// add your code here
		/// read the file
		$fileContent = file_get_contents($groupProductFile);
		$groupProductsJSON = json_decode($fileContent,true);
		$numGroups = $groupProductsJSON["num_groups"];
		$numProducts = $groupProductsJSON["num_products"];
		$this->generatePlayers($numGroups);
		$this->generateProducts($numProducts);
		/// extract for each group and insert into datatabase
		foreach($groupProductsJSON["grp_prod"] as $arrIdx => $groupProduct){
			/// first generate a player
			$playerId = $arrIdx + 1; /// mysql auto increment key starts at 1 instead of 0
			$toProduce = $groupProduct["to_produce"];
			$productIds = $toProduce["id"];
			$productCosts = $toProduce["cost"];
			foreach($productIds as $idx => $id){
				$tmpCost = $productCosts[$idx];
				/// update the product cost and produce information
				$product = Doctrine_Core::getTable("Product")->find($id);
				if($product){
					$product->setProducer($playerId);
					$product->setCost($tmpCost);
					$product->save();
				}
				/// generate group->product entries
				$grpProduct = new GroupProduct();
				$grpProduct->setGroupId($playerId);
				$grpProduct->setProductId($id);
				$grpProduct->save();
			}
			
			$toAcquire = $groupProduct["to_acquire"];
			/// generate production cost and acquire utility
			$productIds = $toAcquire["id"];
			$productUtilitis = $toAcquire["utility"];
			foreach($productIds as $idx => $id){
				$tmpUtility = $productUtilitis[$idx];
				/// update the product cost and produce information
				$product = Doctrine_Core::getTable("Product")->find($id);
				if($product){
					$product->setConsumer($playerId);
					$product->setUtility($tmpUtility);
					$product->save();
				}
			}
		}
	}
}