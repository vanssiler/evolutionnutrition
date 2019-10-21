SELECT * FROM `eav_attribute` WHERE `attribute_code` = 'mgs_lookbook'
UPDATE `evolution_magento`.`eav_attribute` SET `is_user_defined` = '1' WHERE `eav_attribute`.`attribute_id` = 135;