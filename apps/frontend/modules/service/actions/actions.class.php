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
	public static $SETTING = NULL;
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request)
	{
		$this->forward('default', 'module');
	}

	public function executeTest(sfWebRequest $request){

		$this->getResponse()->setContent("just for test");
		return sfView::NONE;
	}

	public static  function getClockInfo(){
		$curTime = time();
		/// convert the start time string into time stamp
		$startTimestamp = strtotime(serviceActions::$SETTING["trading.start.time"]);
		$curTimestamp = time();
		$secondsElapsed = $curTimestamp - $startTimestamp;
		$periodLength = 60 * serviceActions::$SETTING["trading.circle"];
		$periodIdx = (int)($secondsElapsed / $periodLength);
		$rndIdx = ($secondsElapsed % $periodLength) > ($periodLength/2) ? 1 : 0;
		return array("period.idx" => $periodIdx,"rnd.a" => (int)!$rndIdx, "rnd.b" => $rndIdx);
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
		// 		if(is_null(self::$SETTING)){
		/// load the setting from the configuration file
		$configFile = __DIR__ . "/experiment-setting.ini";
		/// check existence
		if(!file_exists($configFile)){
			throw new Exception("experimental setting file:" + $configFile + " does not exist");
		}
		self::$SETTING = parse_ini_file($configFile);
		// 		}
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
			$tmpProduct = array("id" => $product->getId(), "is_producer" => 0, "is_consumer" => 0);
			if(isset($producedIds[$productId])){
				$tmpProduct["is_producer"] = 1;
				$tmpProduct["cost"] = $product->getCost();
			}
			if(isset($consumedIds[$productId])){
				$tmpProduct["is_consumer"] = 1;
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

	public function executeQueryClock(sfWebRequest $request){
		$response = self::getClockInfo();
		$this->getResponse()->setContentType("application/json");
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}

	public function executeOfferProduct(sfWebRequest $request){
		/// offer a product to a group
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			/// check group id of recipient, price and product id
			$isGood = true;
			$recipientGrp = $request->getParameter("recipient");
			$product = $request->getParameter("product");
			$price = $request->getParameter("price");
			$firstRefFee = $request->getParameter("firstRefFee");
			$secondRefFee = $request->getParameter("secondRefFee");
			if(is_null($recipientGrp) || is_null($product) || is_null($price) || is_null($firstRefFee) || is_null($secondRefFee)){
				$response["status"] = "fail";
				$response["message"] = "recipient group id, product id, price, first degree referral fee and second degree referral fee must be provided";
				$isGood = false;
			}
			if($isGood){
				$group = Doctrine_Core::getTable("Player")->find($groupId);
				$group->offerProduct($recipientGrp,$product,$price,$firstRefFee,$secondRefFee,$response);
			}
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}

	public function executeReferProduct(sfWebRequest $request){
		/// offer a product to a group
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			/// check group id of recipient, price and product id
			$isGood = true;
			$recipientGrp = $request->getParameter("recipient");
			$transactionId = $request->getParameter("transactionId");
			if(is_null($recipientGrp) || is_null($transactionId)){
				$response["status"] = "fail";
				$response["message"] = "recipient group id, transaction id must be supplied";
				$isGood = false;
			}
			if($isGood){
				$group = Doctrine_Core::getTable("Player")->find($groupId);
				$group->referProduct($recipientGrp,$transactionId,$response);
			}
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}

	public function executeQueryMyProductInfo(sfWebRequest $request){
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			$group = Doctrine_Core::getTable("Player")->find($groupId);
			$group->checkMyProductInfo($response);
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}
	
	public function executeQueryInTransactions(sfWebRequest $request){
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			$group = Doctrine_Core::getTable("Player")->find($groupId);
			$group->checkIncomeTransactions($response);
		}
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}

	public function executeQueryOutTransactions(sfWebRequest $request){
		$response = array();
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			$group = Doctrine_Core::getTable("Player")->find($groupId);
			$group->checkOutgoTransactions($response);
		}
		$this->getResponse()->setContentType("application/json");
		$this->getResponse()->setContent(json_encode($response));
		return sfView::NONE;
	}
	
	public function executeAcceptOffer(sfWebRequest $request){
		$response = array();
		$this->getResponse()->setContentType("application/json");
		if($this->verifyGroup($request, $response)){
			$groupId = $response["group_id"];
			$group = Doctrine_Core::getTable("Player")->find($groupId);
			$transactionId = $request->getParameter("transactionId");
			if(!$transactionId){
				$response["status"] = "fail";
				$response["message"] = "transaction id must be provided";
			}else{
				$group->acceptOfferOrReferal($transactionId,$response);
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
		}
		$this->getResponse()->setContent(json_encode($response));
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
