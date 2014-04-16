<?php

/**
 * service actions.
 *
 * @package    ProductTrading
 * @subpackage service
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class serviceActions extends sfActions
{
	protected static $NUM_GROUPS = 0;
	protected static $NUM_PRODUCTS = 0;
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request)
	{
		$this->forward('default', 'module');
	}

	protected function initStatic(){
		/// intialize static variables
		if(serviceActions::$NUM_GROUPS == 0){
			$q = Doctrine_Core::getTable("Player")->createQuery("q")->select("id");
			$results = $q->execute();
			serviceActions::$NUM_GROUPS = count($results);
		}
		if(serviceActions::$NUM_PRODUCTS == 0){
			$q = Doctrine_Core::getTable("Product")->createQuery("q")->select("id");
			$results = $q->execute();
			serviceActions::$NUM_PRODUCTS = count($results);
		}
	}

	protected function verifyGroupByToken($token){
		$player = Doctrine_Core::getTable("Player")->findOneBy("token", $token);
		return $player;
	}

	protected function getProducedByGroup($groupId){
		$q = Doctrine_Core::getTable("Product")->createQuery("q")->where("q.producer=?",$groupId);
		$results = $q->execute();
		$ids = array();
		foreach($results as $result){
			$ids[$result->getId()] = 1;
		}
		return $ids;
	}

	protected function getAcquiredByGroup($groupId){
		$q = Doctrine_Core::getTable("Product")->createQuery("q")->where("q.consumer=?",$groupId);
		$results = $q->execute();
		$ids = array();
		foreach($results as $result){
			$ids[$result->getId()] = 1;
		}
		return $ids;
	}


	protected function getProductInfo($groupId){
		$q = Doctrine_Core::getTable("Product")->createQuery("q");
		$products = $q->execute();
		$productArray = array();
		$producedIds = $this->getProducedByGroup($groupId);
		$consumedIds = $this->getAcquiredByGroup($groupId);
		foreach($products as $product){
			$productId = $product->getId();
			$tmpProduct = array("id" => $product->getId(), "is_producer" => false, "is_consumer" => false);
			if(isset($producedIds[$productId])){
				$tmpProduct["is_producer"] = true;
				$tmpProduct["cost"] = $product->getCost();
			}
			if(isset($consumedIds[$productId])){
				$tmpProduct["is_consumer"] = true;
				$tmpProduct["utility"] = $product->getUtility();
			}
			$productArray[] = $tmpProduct;
		}
		return $productArray;		
	}

	
	
	public function executeQueryProductInfo(sfWebRequest $request){
		/// intialize the static variables
		$this->initStatic();
		$response = array("status" => "success", "message" =>"nothing to worry about");
		/// query products of this group or other groups
		/// expect token and group id from $request
		$token = $request->getParameter("token",null);
		if(!$token){
			$response["status"] = "fail";
			$response["message"] = "token is needed to verify the group identity";
		}else{
			$player = $this->verifyGroupByToken($token);
			if(!$player){
				$response["status"] = "fail";
				$response["message"] = "invalid token, please check. Contact TA (manazhao@soe.ucsc.edu) if needed";
			}else{
				/// valid token, try to identify the group id
				$groupId = $player->getId();
				/// pull out all products
				$productInfo= $this->getProductInfo($groupId);
				$response["product_info"] = $productInfo;
			}
		}
		$this->getResponse()->setContentType("application/json");
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}
}
