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

	protected function verifyGroup($request, &$response){
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
				$response["group_id"] = $groupId;
			}
			return $player;
		}
		return null;
	}

	public function executeOfferProduct(sfWebRequest $request){
		/// offer a product to a group
		$response = array();
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			/// check group id of recipient, price and product id
			$isGood = true;
			$recipientGrp = $request->getParameter("recipient");
			$product = $request->getParameter("product");
			$price = $request->getParameter("price");
			if(is_null($recipientGrp) || is_null($product) || is_null($price)){
				$response["status"] = "fail";
				$response["message"] = "recipient group id, product id, price must be provided";
				$isGood = false;
			}
			$recipientGrpObj = null;
			$productObj = null;
			if($isGood){
				$recipientGrpObj = Doctrine_Core::getTable("Player")->find($recipientGrp);
				if(!$recipientGrpObj){
					$response["status"] = "fail";
					$response["message"] = "invalid recipient specified, please check";
				}
				$productObj = Doctrine_Core::getTable("Product")->find($product);
				if(!$productObj){
					$isGood = false;
					$response["status"] = "fail";
					$response["message"] = "invalid product id specified, please check";
				}
				/// check price format
				if(!is_numeric($price)){
					$isGood = false;
					$response["status"] = "fail";
					$response["message"] = "price must be numeric";
				}
			}
			if($recipientGrp == $groupId){
				$isGood = false;
				$response["status"] = "fail";
				$response["message"] = "sell to yourself? sounds an interesting idea:-)";
			}
			if($isGood){
				/// further checkk whether user really owns the product!
				$result = Doctrine_Core::getTable("GroupProduct")->createQuery("q")->where("q.group_id=?",$groupId)->andWhere("product_id=?",$product)->execute();
				if(count($result) == 0){
					$isGood = false;
					$response["status"] = "fail";
					$response["message"] = "oops... only sell your own products!";					
				}
			}
			if($isGood){
				/// make the offer!
				$transaction = new Transaction();
				$transaction->setFromId($groupId);
				$transaction->setToId($recipientGrp);
				$transaction->setProduct($product);
				$transaction->setPrice($price);
				$date = date("Y-m-d H:i:s",time());
				$transaction->setStartTime($date);
				$transaction->setEndTime($date);
				$transaction->setStatus(Transaction::STATUS_PENDING);
				$transaction->save();
			}
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}


	public function executeQuerySetting(sfWebRequest $request){
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$response["num_groups"] = self::$NUM_GROUPS;
			$response["num_products"] = self::$NUM_PRODUCTS;
			$this->getResponse()->setContent(json_encode($response));
		}
		return sfView::NONE;
	}

	public function preExecute(){
		$this->initStatic();
		parent::preExecute();	
	}
	
	public function executeQueryProductInfo(sfWebRequest $request){
		/// intialize the static variables
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			/// pull out all products
			$productInfo= $this->getProductInfo($response["group_id"]);
			$response["product_info"] = $productInfo;
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}
}
