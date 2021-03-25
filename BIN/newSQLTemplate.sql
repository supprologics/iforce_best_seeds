BEGIN
	DECLARE qtySelable FLOAT(0.00);
    DECLARE qtyNonselable FLOAT(0.00);

	SELECT online INTO @onlineval FROM invoice WHERE `code` = NEW.invoice_code AND device_id = NEW.device_id;

	IF NEW.item_type = 2 THEN
    	SET qtySelable = NEW.qty_selable;
        SET qtyNonselable = NEW.qty_nonselable;
    ELSE
    	SET qtySelable = 0 - NEW.qty_selable;
        SET qtyNonselable = 0 - NEW.qty_nonselable;
    END IF;

	-- STOP STOCK TRANSACTION FOR PRE-SALES 3=PRESALES
	IF @onlineval != 3 THEN

	INSERT INTO stock(
		`device_id`,
		`brands_id`,
		`items_id`,
		`qty`,
        `qty_ns`,
		`selling`,
		`discount`,
		`total`,
		`tbl_name`,
		`p_id`,
		`f_id`,
		`created`,
		`online`)
	SELECT 
		NEW.device_id,
		items.brands_id,
		NEW.items_id,
        qtySelable,
        qtyNonselable,
		NEW.mrp,
		NEW.discount,
		NEW.total,
		'invoice',
		NEW.invoice_code,
		NEW.id,
		NOW(),
		@onlineval 
	FROM items,invoice
	WHERE items.id = NEW.items_id AND invoice.code = NEW.invoice_code AND invoice.device_id = NEW.device_id;
    
    END IF;
    
END