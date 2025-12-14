<?php

class MidtransHelper
{
  /**
   * Create Midtrans Snap Token
   * 
   * @param array $orderData Order details
   * @return array Response with snap_token or error
   */
  public static function createSnapToken($orderData)
  {
    // Load Midtrans config
    require_once BASE_PATH . '/src/app/config/midtrans.php';

    // Prepare transaction details
    $params = [
      'transaction_details' => [
        'order_id' => $orderData['order_id'],
        'gross_amount' => (int) $orderData['gross_amount'],
      ],
      'customer_details' => [
        'first_name' => $orderData['customer_name'],
        'email' => $orderData['customer_email'] ?? 'customer@lokapos.com',
        'phone' => $orderData['customer_phone'] ?? '08123456789',
      ],
      'item_details' => $orderData['items'],
      'callbacks' => [
        'finish' => BASE_URL . '/pos/payment-finish'
      ]
    ];

    // Create authorization header
    $authString = base64_encode(MIDTRANS_SERVER_KEY . ':');

    // Initialize cURL
    $ch = curl_init();

    curl_setopt_array($ch, [
      CURLOPT_URL => MIDTRANS_SNAP_URL,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => json_encode($params),
      CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . $authString
      ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201) {
      $result = json_decode($response, true);
      return [
        'status' => 'success',
        'snap_token' => $result['token'],
        'redirect_url' => $result['redirect_url']
      ];
    } else {
      return [
        'status' => 'error',
        'message' => 'Failed to create Midtrans token: ' . $response
      ];
    }
  }

  /**
   * Verify transaction status from Midtrans
   * 
   * @param string $orderId Order ID
   * @return array Transaction status
   */
  public static function checkTransactionStatus($orderId)
  {
    require_once BASE_PATH . '/src/app/config/midtrans.php';

    $url = (MIDTRANS_IS_PRODUCTION
      ? 'https://api.midtrans.com/v2/'
      : 'https://api.sandbox.midtrans.com/v2/') . $orderId . '/status';

    $authString = base64_encode(MIDTRANS_SERVER_KEY . ':');

    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . $authString
      ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
      return json_decode($response, true);
    } else {
      return ['status_code' => '404', 'status_message' => 'Transaction not found'];
    }
  }
}
