<?php

define('MIDTRANS_IS_PRODUCTION', false);
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-p6hE0Qcksdj2owT0DYXDePJn');
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-T5RKrufuFds5eN_i');

define(
  'MIDTRANS_SNAP_URL',
  MIDTRANS_IS_PRODUCTION
    ? 'https://app.midtrans.com/snap/v1/transactions'
    : 'https://app.sandbox.midtrans.com/snap/v1/transactions'
);
