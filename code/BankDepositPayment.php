<?php

/**
 * @package ecommerce
 */

/**
 * Payment object representing a bank deposit payment
 */
class BankDepositPayment extends Payment {
	
	private static $bankaccountname = "";
	private static $banknumber = "";
	
	static $paidforid_as_ref = false;
	
	static function set_bank_details($bankacc, $number){
		BankDepositPayment::$bankaccountname = $bankacc;
		BankDepositPayment::$banknumber = $number;		
	}
	
	static function set_use_paidfor_id_as_ref($use =  true){
		self::$paidforid_as_ref = $use;
	}
	
	/**
	 * Process the Bank Deposit payment method
	 */
	function processPayment($data, $form) {
		if(!$this->PaymentMethod)
			$this->PaymentMethod = "BankDeposit";
			
		if(!$this->Status)
			$this->Status = "Pending";
		$result['PaymentID'] = $this->write();			
		if(!$this->Message)
			$this->Message = "<p class=\"warningMessage\">Payment accepted via bank deposit.</p>" .
					"<p>Please deposit ".DBField::create('Currency', $this->Amount)->Nice()." ".$this->Currency." into the following account:</p>" .
							"<ul><li>".self::$bankaccountname." </li>" .
							"<li>".self::$banknumber." </li>" .
							"<li>Please use '".$this->getReference()."' as the bank deposit reference number.</li>" . //ecommerce specific
							"</ul>";
		
		$result['Success'] = "Success";
		$this->write();
		return new Payment_Success();
	}
	
	function getPaymentFormFields() {
		global $emmaus_message;
		
		return new FieldSet(
			new LiteralField("Bankblurb", '<div id="BankDeposit" class="typography">' . "<p>Please note: Your goods will not be dispatched until we receive your payment.</p></div>"),
			new HiddenField("BankDeposit", "BankDeposit", 0)
		);
	}
	function getPaymentFormRequirements() {
		return null;
	}
	
	function getReference(){
		if(self::$paidforid_as_ref && $this->PaidObject()){
			$ref = $this->PaidForID;
		}else{
			$ref = $this->ID;
		}
		$this->extend('updateReference',$ref);
		return $ref;
	}
		
}

?>
