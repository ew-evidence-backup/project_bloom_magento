ALTER TABLE  `udropship_vendor` ADD  `delivery_charge` DECIMAL( 10, 3 ) NOT NULL DEFAULT  '5.000' AFTER  `cut_off_time`; //-- executed on live already -->

UPDATE `udropship_vendor` SET cut_off_time = 12 WHERE cut_off_time is NULL; //-- executed on live already -->

ALTER TABLE  `udropship_vendor` CHANGE  `email`  `email` VARCHAR( 127 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL; //-- executed on live already -->

UPDATE udropship_vendor AS uv,
vendor AS v SET uv.delivery_charge = CONVERT( v.delivery_fee, DECIMAL( 10, 3 ) ) WHERE v.email = uv.email; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD `is_approved` TINYINT NOT NULL DEFAULT '0' ; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD UNIQUE `email_unq` ( `email` ) ; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD `facebook_page` VARCHAR( 128 ) NOT NULL; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD `facebook_page_id` VARCHAR( 25 ) NOT NULL; //-- executed on live already -->
 
ALTER TABLE `udropship_vendor`  ADD `subdomain` VARCHAR(15) NOT NULL; //-- executed on live already -->

ALTER TABLE `sales_flat_order_item` ADD `delivery_date` DATE NOT NULL DEFAULT '2000-01-01'; //-- executed on live already -->

ALTER TABLE `udropship_vendor`
DROP `password_enc` ,
DROP `allow_shipping_extra_charge` ,
DROP `default_shipping_extra_charge_suffix` ,
DROP `default_shipping_extra_charge_type` ,
DROP `default_shipping_extra_charge` ,
DROP `is_extra_charge_shipping_default` ; //-- executed on live already -->

ALTER TABLE `udropship_vendor` CHANGE `cut_off_time` `cut_off_time` TIME NULL DEFAULT '12:00:00'; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD `login_at` TIMESTAMP NOT NULL AFTER `created_at`; //-- executed on live already -->

ALTER TABLE `udropship_vendor` CHANGE `created_at` `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ; //-- executed on live already -->

ALTER TABLE `udropship_vendor` ADD `design_config` TEXT NOT NULL //-- executed on live already -->

ALTER TABLE `udropship_vendor` CHANGE `subdomain` `subdomain` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL //-- executed on live already -->

ALTER TABLE `bloomnation_prod`.`impl_product_zip` ADD INDEX `zipcode` ( `zip` ) //-- executed on live already  -->