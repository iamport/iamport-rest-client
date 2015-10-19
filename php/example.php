<?php
	$api_key 		= 'your_rest_api_key'; //아임포트 관리자 페이지(https://admin.iamport.kr)에서 확인할 수 있습니다.<시스템설정 / 내정보>
	$api_secret 	= 'your_rest_api_secret'; //아임포트 관리자 페이지(https://admin.iamport.kr)에서 확인할 수 있습니다.<시스템설정 / 내정보>
	
	$imp_uid 		= $_POST['imp_uid']; //아임포트 결제 고유번호. Notification URL에 의한 호출이라면 imp_uid를 POST변수에서 얻을 수 있음 ( http://www.iamport.kr/manual#server-notice )
	$merchant_uid 	= $_POST['merchant_uid']; //상점 주문 고유번호. Notification URL에 의한 호출이라면 imp_uid를 POST변수에서 얻을 수 있음 ( http://www.iamport.kr/manual#server-notice )
	// 또는, imp_uid, merchant_uid는 <결제 후 callback>을 통해서도 서버로 전달받을 수 있음 ( http://www.iamport.kr/manual#payment-response )

	$iamport = new Iamport($api_key, $api_secret);

	$response_by_imp_uid 		= $iamport->findByImpUID($imp_uid);
	$response_by_merchant_uid 	= $iamport->findByMerchantUID($merchant_uid);

	$response = $response_by_imp_uid; //or $response_by_merchant_uid

	if ( !$response->success )	exit('아임포트 API서버로부터 결제결과 수신에 실패하였습니다.');

	$payment_data = $response->data;
	if ( $payment_data->status == 'paid' ) {
		$nonce = $payment_data->getCustomData('delivered_custom_data_name');
		$paid_amount = intval($payment_data->amount); //status, amount와 같은 Payment 객체의 기본 attribute는 __get으로 구현됨

		$real_merchant_uid = $payment_data->merchant_uid; //imp_uid를 가지고 REST API조회했다면, POST변수에 있는 merchant_uid보다는 REST API의 response에 들어있는 merchant_uid가 보다 신뢰도있다.

		//Do your business logic
		$order_amount = how_much_you_should_get($real_merchant_uid); //TODO

		if ( $order_amount === $paid_amount ) {
			//결제완료에 대한 DB update
		}
	} else {
		//something goes on
	}