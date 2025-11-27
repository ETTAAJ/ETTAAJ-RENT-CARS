-- Update all cars with default insurance values
-- Basic Insurance: Deposit MAD 8,079.12
-- Smart Insurance: MAD 142.90/day, Deposit MAD 4,039.56
-- Premium Insurance: MAD 223.50/day, Deposit MAD 1,795.36

UPDATE `cars` 
SET 
  `insurance_basic_price` = 0.00,
  `insurance_basic_deposit` = 8079.12,
  `insurance_smart_price` = 142.90,
  `insurance_smart_deposit` = 4039.56,
  `insurance_premium_price` = 223.50,
  `insurance_premium_deposit` = 1795.36
WHERE 
  `insurance_basic_price` IS NULL 
  OR `insurance_smart_price` IS NULL 
  OR `insurance_premium_price` IS NULL
  OR `insurance_basic_deposit` IS NULL
  OR `insurance_smart_deposit` IS NULL
  OR `insurance_premium_deposit` IS NULL;

-- Alternative: Update ALL cars (including those with existing values)
-- Uncomment the following if you want to override existing values:

-- UPDATE `cars` 
-- SET 
--   `insurance_basic_price` = 0.00,
--   `insurance_basic_deposit` = 8079.12,
--   `insurance_smart_price` = 142.90,
--   `insurance_smart_deposit` = 4039.56,
--   `insurance_premium_price` = 223.50,
--   `insurance_premium_deposit` = 1795.36;

