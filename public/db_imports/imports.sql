CREATE TABLE `Expenses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `category` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `unit_of_measure` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unit_price` float DEFAULT NULL,
  `unit_amount` float DEFAULT NULL,
  `total_price` float DEFAULT NULL,
  `date_recorded` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `admin_tables` (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_fullname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_username` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_time_login` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `organization_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contacts` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_status` int DEFAULT '1' COMMENT '0 will be blocked and 1 will be active to login',
  `email` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CompanyName` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `priviledges` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '''[{"option":"My Clients","view":true,"readonly":false},{"option":"Transactions","view":true,"readonly":false},{"option":"Expenses","view":true,"readonly":false},{"option":"My Routers","view":true,"readonly":false},{"option":"SMS","view":true,"readonly":false},{"option":"Account and Profile","view":true,"readonly":true}]''',
  `activated` int NOT NULL DEFAULT '0',
  `dp_locale` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20230320161856',
  `deleted` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `client_tables` (
  `client_id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_address` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location_coordinates` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `client_network` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_default_gw` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `next_expiration_date` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `clients_reg_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `max_upload_download` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `monthly_payment` int DEFAULT NULL,
  `router_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_interface` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `comment` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `clients_contacts` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_account` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `client_status` int DEFAULT '1' COMMENT '1 client active, 2 client inactive',
  `payments_status` int NOT NULL DEFAULT '1' COMMENT '1 the user is to be charged, 0 the user is not to be charged',
  `wallet_amount` int NOT NULL DEFAULT '0',
  `min_amount` int NOT NULL DEFAULT '100',
  `client_username` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `freeze_date` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_freeze_status` int DEFAULT '0',
  `client_freeze_untill` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reffered_by` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `assignment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'static',
  `client_secret` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_secret_password` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_profile` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_changed` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20220801185959',
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `remote_routers` (
  `router_id` int NOT NULL AUTO_INCREMENT,
  `router_name` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sstp_username` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sstp_password` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_location` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_coordinates` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `winbox_port` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `api_port` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date_changed` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activated` int NOT NULL DEFAULT '0',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`router_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `router_tables` (
  `router_id` int NOT NULL AUTO_INCREMENT,
  `router_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_ipaddr` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_api_username` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_api_password` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `router_api_port` int NOT NULL,
  `router_status` int DEFAULT NULL COMMENT '1 = Router is active, 0 means its inactive and no user will be moniored from that router',
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`router_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `keyword` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL,
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO settings VALUES ('1', 'sms_api_key', 'adea4ca6a9ca297fc546d0a06848827f', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('2', 'sms_partner_id', '258', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('3', 'sms_shortcode', 'HypBits', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('4', 'consumer_key', '6yYoxkjeAPGbkxjfEgUZ14KT52IOPhWM', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('5', 'consumer_secret', 'L4w8Ls49ZfLxurde', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('6', 'passkey', '3f9f489b44c2bbbc9cf3cf04672825994ce39ded35c8cc24304f42ffa76397ae', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('7', 'paybill', '4061913', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('8', 'Messages', '[{\"Name\":\"Remind_payment\",\"messages\":[{\"Name\":\"day_before\",\"message\":\"Dear [client_f_name], Your account expires tomorrow. Pay using Paybill: 4061913 Account: [acc_no]. Your wallet balance is [client_wallet].\"},{\"Name\":\"de_day\",\"message\":null},{\"Name\":\"day_after\",\"message\":\"Dear [client_f_name], Your account expired yesterday. Pay using Paybill:4061913 Account: [acc_no] .Your wallet balance is [client_wallet].\"}]},{\"Name\":\"recieve_payments\",\"messages\":[{\"Name\":\"right_account\",\"message\":\"Hello [client_f_name], Your payment of [trans_amnt] has been received on [today] at [now]. New wallet balance is [client_wallet].\"},{\"Name\":\"wrong_account\",\"message\":\"INVALID ACCOUNT. [trans_amnt] recieved on [today] at [now]. Send your name and M-Pesa Transaction Id to 0717748569/0720268519 For rectification.\"},{\"Name\":\"refferer_msg\",\"message\":\"Hello [client_f_name] you have been credited with [refferer_trans_amount] from [refferer_name] on [today] at [now].Your new wallet balance is [client_wallet]\"},{\"Name\":\"refferer_msg\",\"message\":\"Hello [client_f_name] we have recieved your payment of [trans_amnt], your minimum payment allowed is [min_amnt]. Wallet balance is [client_wallet]\"}]},{\"Name\":\"renew_account\",\"messages\":[{\"Name\":\"account_blocked\",\"message\":\"Dear [client_f_name] Your Acc [acc_no] has been renewed untill [exp_date].  Your new wallet Bal is  [client_wallet]. For enquires call 0717748569/0720268519\"},{\"Name\":\"account_extended\",\"message\":\"Dear [client_f_name] Your Acc [acc_no] has been extended until [exp_date]. Your new wallet Bal: [client_wallet]. For enquires call 0717748569/0720268519\"},{\"Name\":\"account_deactivated\",\"message\":\"Dear [client_f_name] Your Acc [acc_no] has been deactivated. Paybill:4061913 Account: [acc_no]. Your wallet Bal: [client_wallet]. For enquires call 0717748569/0720268519\"}]},{\"Name\":\"new_client\",\"messages\":[{\"Name\":\"welcome_message\",\"message\":\"Welcome [client_f_name]. Your monthly payment is [monthly_fees]. Pay via  paybill No. 4061913 account no [acc_no]. Your next expiration date is [exp_date].\"}]},{\"Name\":\"sms_bill_manager\",\"messages\":[{\"Name\":\"welcome_client_sms\",\"message\":\"Vipi [client_f_name] karibu Hypbits, utafanya malipo yako ukitumia paybill No. 4061913 na akaunti namba [acc_no]  kwa [sms_rate] kwa kila sms.\"},{\"Name\":\"rcv_coracc_billsms\",\"message\":\"Vipi [client_name], tumepokea malipo yako ya [trans_amnt] leo: [today] masaa ya [now]. Balance yako mpya ni [sms_balance] SMS.\"},{\"Name\":\"rcv_incoracc_billsms\",\"message\":\"Tumepokea [trans_amnt], Akaunti namba amabayo umepeana sio sahihi, tafadahli tuma jumbe fupi ukipeana namba yako ya simu na jina yako kwa 0743551250/0720268519\"},{\"Name\":\"rcv_belowmin_billsms\",\"message\":\"Vipi, [client_f_name], Malipo chini ya elfu moja haikubaliki tafadhali! Malipo ya chini inayo kubalika ni [min_amnt]\"},{\"Name\":\"msg_reminder_bal\",\"message\":\"Vipi, [client_f_name], SMS zako zimebaki [sms_balance] fanya malipo ukitumia paybill 4061913 kwa akaunti namba hii [acc_no] ili uziongesha kabla ziishe.\"}]},{\"Name\":\"account_freezing\",\"messages\":[{\"Name\":\"account_frozen\",\"message\":\"Dear [client_f_name] Your Acc [acc_no] will be frozen for [days_frozen] untill [unfreeze_date].  Your wallet Bal as of  [today] at [now] is [client_wallet]. For enquires call 0717748569/0720268519.\"},{\"Name\":\"account_unfrozen\",\"message\":\"Welcome back [client_f_name], Your Acc [acc_no] has been activated successfully after being frozen. Your new wallet Bal is  [client_wallet]. For enquires call 0717748569/0720268519.\"}]}]', '1', '20230620182439', '0');
INSERT INTO settings VALUES ('9', 'delete', '[{\"name\":\"delete_sms\",\"period\":\"never\"},{\"name\":\"delete_transaction\",\"period\":\"never\"}]', '1', '20231221075837', '0');
INSERT INTO settings VALUES ('10', 'expenses', '[{\"name\":\"Daily Expense\",\"index\":0}]', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('14', 'repeat_value', '{\"date\":\"20230414\",\"repeat_value\":3}', '1', '20230414102333', '0');
INSERT INTO settings VALUES ('15', 'sstp_server', '{\"username\":\"SJpdcxixj\",\"password\":\"ncUTdImrLQHQAErjxp\",\"ip_address\":\"3.14.249.167\",\"port\" : \"1982\"}', '1', '20230320161856', '0');
INSERT INTO settings VALUES ('16', 'sms_sender', 'celcom', '1', '20230320161856', '0');


CREATE TABLE `sms_tables` (
  `sms_id` int NOT NULL AUTO_INCREMENT,
  `sms_content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `date_sent` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `recipient_phone` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sms_status` int DEFAULT NULL,
  `account_id` int DEFAULT NULL,
  `sms_type` int NOT NULL DEFAULT '1' COMMENT '1 is transaction 2 is Notification',
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`sms_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `transaction_sms_tables` (
  `transaction_id` int NOT NULL,
  `transaction_mpesa_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transacion_amount` int DEFAULT NULL,
  `phone_transacting` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_account` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_acc_id` int DEFAULT NULL,
  `transaction_status` int DEFAULT NULL,
  `transaction_short_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fullnames` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `transaction_tables` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `transaction_mpesa_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_date` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transacion_amount` int DEFAULT NULL,
  `phone_transacting` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_account` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `transaction_acc_id` int DEFAULT NULL,
  `transaction_status` int DEFAULT NULL,
  `transaction_short_code` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fullnames` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `verification_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` int NOT NULL,
  `phone_sent` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_generated` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` int NOT NULL COMMENT '0 = not used, 1 = already used',
  `date_changed` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '20230320161856',
  `deleted` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `client_reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `report_code` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `report_title` varchar(2000) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `report_description` mediumtext COLLATE utf8mb4_general_ci,
  `problem` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `diagnosis` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `solution` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `closed_by` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `client_id` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_reporter` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_attender` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `report_date` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `resolve_time` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci