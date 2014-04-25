<?php

/**
 * Player
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ProductTrading
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Player extends BasePlayer
{
	public static function verifyGroupId($grpId){
		$results = Doctrine_Core::getTable("Player")->createQuery("q")->select("max(id) AS mid")->execute();
		foreach($results as $result){
			$maxId = $result["mid"];
			if($grpId > $maxId || $grpId <= 0){
				return false;
			}
			break;
		}
		return true;
	}
	
	public function checkMyProductInfo(&$response){
		/// get products produced by me
		$produced = Doctrine_Core::getTable("Product")->findByProducer($this->getId());
		$response["produced"] = array();
		$response["to.sell"] = array();
		$response["consumed"] = array();
		foreach($produced as $product){
			$response["produced"][] = array("id" => $product->getId(), "cost" => $product->getCost());				
			if($product->getHolder() == $this->getId()){
				$response["to.sell"][] = array("id" => $product->getId(), "cost" => $product->getCost());
			}
		}
		$consumed = Doctrine_Core::getTable("Product")->findByConsumer($this->getId());
		foreach($consumed as $product){
			$response["consumed"][] = array("id" => $product->getId(), "utility" => $product->getUtility());
		}
	}
	
	/**
	 * check products that are offered or referred to me
	 */
	public function checkIncomeTransactions(&$response){
		$transactions = Doctrine_Core::getTable("Transaction")->findByToId($this->getId());
		/// categorize the transactions by their status
		$clockInfo = serviceActions::getClockInfo();
		$curPeriod = $clockInfo["period.idx"];
		$response["offers"] = array("pending" => array(), "expired" => array(), "purchased" => array(), "referred" => array());
		$response["referrals"] = array("pending" => array(), "expired" => array(), "purchased" => array(), "referred" => array());
		foreach($transactions as $transaction){
			$periodIdx = $transaction->getRnd();
			$expirePeriodIdx = $transaction->getExpire();
			$status = $transaction->getStatus();
			if($expirePeriodIdx < $curPeriod && $status == Transaction::STATUS_PENDING){
				/// mark it as expired
				$transaction->setStatus(Transaction::STATUS_EXPIRED);
				$status = Transaction::STATUS_EXPIRED;
				$transaction->save();
			}
			$type = $transaction->getType();
			$response[Transaction::getTransactionTypeName($type)][Transaction::getStatusName($status)][] = 
			array("id" => $transaction->getId(), "product.id" => $transaction->getProduct(),"price" => $transaction->getPrice(),
					"first.ref.fee" => $transaction->getFirstRefFee(),
					"second.ref.fee" => $transaction->getSecondRefFee(),"from.id" => $transaction->getFromId(),"post.rnd" => $transaction->getRnd(),
					"ref.degree" => $transaction->getRefDegree());
		}
		
	}
	
	
	public function checkOutgoTransactions(&$response){
		$transactions = Doctrine_Core::getTable("Transaction")->findByFromId($this->getId());
		/// categorize the transactions by their status
		$clockInfo = serviceActions::getClockInfo();
		$curPeriod = $clockInfo["period.idx"];
		$response["offers"] = array("pending" => array(), "expired" => array(), "purchased" => array(), "referred" => array());
		$response["referrals"] = array("pending" => array(), "expired" => array(), "purchased" => array(), "referred" => array());
		foreach($transactions as $transaction){
			$periodIdx = $transaction->getRnd();
			$expirePeriodIdx = $transaction->getExpire();
			$status = $transaction->getStatus();
			if($expirePeriodIdx < $curPeriod && $status == Transaction::STATUS_PENDING){
				/// mark it as expired
				$transaction->setStatus(Transaction::STATUS_EXPIRED);
				$status = Transaction::STATUS_EXPIRED;
				$transaction->save();
			}
			$type = $transaction->getType();
			$response[Transaction::getTransactionTypeName($type)][Transaction::getStatusName($status)][] =
			array("id" => $transaction->getId(), "product.id" => $transaction->getProduct(),"price" => $transaction->getPrice(),
					"first.ref.fee" => $transaction->getFirstRefFee(),
					"second.ref.fee" => $transaction->getSecondRefFee(),"from.id" => $transaction->getFromId(),"post.rnd" => $transaction->getRnd(),
					"ref.degree" => $transaction->getRefDegree());
		}
	}
	
	public function acceptOfferOrReferal($transactionId, &$response){
		/// check the transaction is made to current user
		$transaction = Doctrine_Core::getTable("Transaction")->find($transactionId);
		if(!$transaction){
			$response["status"] = "fail";
			$response["message"] = "invalid transaction id";
			return;
		}
		if($transaction->getToId() != $this->getId()){
			$response["status"] = "fail";
			$response["message"] = "you can only accept offer or referral that is targeted on you";
			return;
		}
		$clockInfo = serviceActions::getClockInfo();
		$curPeriod = $clockInfo["period.idx"];
		$isRndB = $clockInfo["rnd.b"];
		if(!$isRndB){
			$response["status"] = "fail";
			$response["message"] = "please wait until next offer/referral claiming round";
			return;
		}
		/// check whether the transaction expired
		$expirePeriod = $transaction->getExpire();
		if($expirePeriod > $curPeriod){
			$transaction->setStatus(Transaction::STATUS_EXPIRED);
			$transaction->save();
			$response["status"] = "fail";
			$response["message"] = "the offer/referral has expired";
			return;
		}
		/// check whether it's the product the group need
		$product = Doctrine_Core::getTable("Product")->find($transaction->getProduct());
		if($product->getConsumer() != $this->getId()){
			$response["status"] = "fail";
			$response["message"] = "only buy product you need";
			return;
		}
		/// generate buyer payoff
		$refDegree = $transaction->getRefDegree();
		$price = $transaction->getPrice();
		$utility = $product->getUtility();
		$buyerPayoff = $utility - $price;
		$this->setProfit($this->getProfit() + $buyerPayoff);
		/// also generate the profit for the referrals and sellers
		$prevTransaction = $transaction;
		$refFee = 0;
		while($prevTransaction){
			$prevTransaction->setStatus(Transaction::STATUS_ACCEPTED);
			$prevTransaction->save();
			$refDegree = $prevTransaction->getRefDegree();
			$fromGroup = Doctrine_Core::getTable("Player")->find($prevTransaction->getFromId());
			/// get the second degree referral fee
			$profit = 0;
			if($refDegree == 2){
				$profit = $prevTransaction->getSecondRefFee();
				$refFee += $prevTransaction->getSecondRefFee();
			}
			if($refDegree == 1){
				$profit = $prevTransaction->getFirstRefFee();
				$refFee += $prevTransaction->getFirstRefFee();
			}
			if($refDegree > 0){
				$fromGroup->setProfit($fromGroup->getProfit() + $profit);
				$prevTransaction = Doctrine_Core::getTable("Transaction")->find($prevTransaction->getReferId());
			}else{
				$cost = $product->getCost();
				$fromGroup->setProfit($fromGroup->getProfit() + $prevTransaction->getPrice() - $cost - $refFee);
				$prevTransaction = null;
			}
		}		
	}
	
	
	/**
	 *
	 * @param unknown_type $toGroupId the group that will take the offer
	 * @param unknown_type $productId the product that will be offered
	 * @param unknown_type $price the price for the product
	 * @param unknown_type $referFees the referral fees
	 */
	public function offerProduct($toGroupId, $productId, $price, $firstRefFee, $secondRefFee, &$response){
		/// validate the group id
		if(!is_numeric($price) || !is_numeric($secondRefFee) || !is_numeric($firstRefFee) || $price <= 0 || $firstRefFee <= 0 || $secondRefFee <= 0){
			$response["status"] = "fail";
			$response["message"] = "price, referral fees must be positive numeric";
			return;
		}
		if($toGroupId == $this->getId()){
			$response["status"] = "fail";
			$response["message"] = "you are not allowed to sell products to yourself";
			return;
		}

		if(!self::verifyGroupId($toGroupId)){
			$response["status"] = "fail";
			$response["message"] = "invalid recipient group id";
			return;
		}

		/// check the ownership of the product
		$results = Doctrine_Core::getTable("Product")->createQuery("q")
		->where("q.producer = ?", $this->getId())->andWhere("id = ?",$productId)
		->andWhere("q.holder = ?", $this->getId())->execute();
		if(count($results) == 0){
			$response["status"] = "fail";
			$response["message"] = "only the producer and the owner can sell the product";
			return;
		}
		
		/// check the clock
		$clockInfo = serviceActions::getClockInfo();
		/// $clockInfo["rnd.idx"] indicates whether it's making offer round or receiving offer round
		if(!$clockInfo["rnd.a"]){
			$response["status"] = "fail";
			$response["message"] = "please wait until next offering round";
			return;
		}
		/// check whether user exceeds the offer limit
		$results = Doctrine_Core::getTable("Transaction")->createQuery("q")->where("q.from_id=?",$this->getId())
		->andWhere("q.type=?",Transaction::TYPE_DIRECT_OFFER)->andWhere("q.rnd = ?", $clockInfo["period.idx"])->execute();
		if(count($results) == serviceActions::$SETTING["max.offer.send"] ){
			$response["status"] = "fail";
			$response["message"] = "you already hit the maximum number of product offers per trading period: " . serviceActions::$SETTING["max.offer.send"];
			return;
		}
		/// post the offer
		$transaction = new Transaction();
		$transaction->setFromId($this->getId());
		$transaction->setToId($toGroupId);
		$transaction->setProduct($productId);
		$transaction->setType(Transaction::TYPE_DIRECT_OFFER);
		$transaction->setPrice($price);
		$transaction->setRnd($clockInfo["period.idx"]);
		/// set when the offer will expire
		$transaction->setExpire($clockInfo["period.idx"] + serviceActions::$SETTING["offer.expire"]);
		$transaction->setFirstRefFee($firstRefFee);
		$transaction->setSecondRefFee($secondRefFee);
		$transaction->setStatus(Transaction::STATUS_PENDING);
		$transaction->save();
		/// change the ownership of the product
		$product = Doctrine_Core::getTable("Product")->find($productId);
		$product->setHolder(0);
		$product->save();
	}
	
	public function referProduct($toGroupId, $transactionId, &$response){
		if($toGroupId == $this->getId()){
			$response["status"] = "fail";
			$response["message"] = "you are not allowed to refer offer to yourself";
			return;
		}
		/// validate the group id
		if(!self::verifyGroupId($toGroupId)){
			$response["status"] = "fail";
			$response["message"] = "invalid recipient group";
			return;
		}
		/// check the transaction is made to current user
		$transaction = Doctrine_Core::getTable("Transaction")->find($transactionId);
		if(!$transaction){
			$response["status"] = "fail";
			$response["message"] = "invalid transaction id";
			return;
		}
		if($transaction->getToId() != $this->getId()){
			$response["status"] = "fail";
			$response["message"] = "you can only refer offers that were made to you";
			return;
		}
		$clockInfo = serviceActions::getClockInfo();
		$curPeriod = $clockInfo["period.idx"];
		$isRndA = $clockInfo["rnd.a"];
		/// check whether it's a offering round
		if(!$isRndA){
			$response["status"] = "fail";	/// check whether offer has expired
			$expire = $transaction->getExpire();
			$response["message"] = "please wait until next offering circle";
			return;
		}
		/// check whether offer has expired
		$expire = $transaction->getExpire();
		if($expire > $curPeriod){
			$response["status"] = "fail";
			$response["message"] = "offer has expired";
			return;
		}
		/// check whether group has reached the maximum number of refers per trading period
		$results = Doctrine_Core::getTable("Transaction")->createQuery("q")->where("q.from_id=?",$this->getId())
		->andWhere("q.type=?",Transaction::TYPE_REFER)->andWhere("q.rnd = ?", $curPeriod)->execute();
		if(count($results) == serviceActions::$SETTING["max.offer.send"] ){
			$response["status"] = "fail";
			$response["message"] = "you already hit the maximum number of product referrals per trading period:" + serviceActions::$SETTING["max.offer.send"];
			return;
		}
		/// good to go
		$referTransaction = new Transaction();
		$referTransaction->setFromId($this->getId());
		$referTransaction->setToId($toGroupId);
		$referTransaction->setProduct($transaction->getProduct());
		$referTransaction->setPrice($transaction->getPrice());
		$referTransaction->setFirstRefFee($transaction->getFirstRefFee());
		$referTransaction->setSecondRefFee($transaction->getSecondRefFee());
		$referTransaction->setType(Transaction::TYPE_REFER);
		/// update the degree of referral
		$referTransaction->setRefDegree($transaction->getRefDegree() + 1);
		///
		$referTransaction->setReferId($transaction->getId());
		$referTransaction->setRnd($curPeriod);
		/// set when the offer will expire
		$referTransaction->setExpire($curPeriod + serviceActions::$SETTING["offer.expire"]);
		$referTransaction->setStatus(Transaction::STATUS_PENDING);
		$referTransaction->save();
		/// also update the offer transaction
		$transaction->setStatus(Transaction::STATUS_REFERRED);
		$transaction->save();
	}
}
