<?php 
/*
*	HDEV Payment Gateway 
*	@email :  info@hev.rw
*	@link : https://github.com/IZEREROGER
*
*/
/*
    Master payment controller
*/
if (!defined('hdev_payment')) {
	class hdev_payment
	{
		private static $api_id = null;
		private static $api_key = null;
		public static function 	api_key($value='')
		{
			self::$api_key = $value;
		}
		public static function api_id($value='')
		{
			self::$api_id = $value;
		}
		public static function pay($tel,$amount,$transaction_ref,$link=''){
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://payment.hdevtech.cloud/api_pay/api/'.self::$api_id.'/'.self::$api_key,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('ref'=>'pay','tel' => $tel,'tx_ref' => $transaction_ref,'amount' => $amount,'link'=>$link),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return json_decode($response);
		}
		public static function get_pay($tx_ref='')
		{
			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => 'https://payment.hdevtech.cloud/api_pay/api/'.self::$api_id.'/'.self::$api_key,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => '',
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => 'POST',
			  CURLOPT_POSTFIELDS => array('ref'=>'read','tx_ref' => $tx_ref),
			));

			$response = curl_exec($curl);

			curl_close($curl);
			return json_decode($response);
		}
	}

    hdev_payment::api_id("HDEV-90621992-dc1c-4aa0-b5ff-5c393649ecf5-ID");
    hdev_payment::api_key("HDEV-6a11e9c7-009b-45d7-87d6-3c5507c3fbc2-KEY");

}
?>