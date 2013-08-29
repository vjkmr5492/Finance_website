############################
# pset7_view_cache_newest_epochs
############################
CREATE VIEW pset7_view_cache_newest_epochs AS

	SELECT
		MAX( quote_epoch ) AS epoch,
		sid
		FROM  `pset7_cache` 
		GROUP BY sid;
	
############################	
#pset7_view_cache_newest_price
############################
CREATE VIEW pset7_view_cache_newest_price AS

	SELECT 
		epochs.epoch,
		cache.price,
		epochs.sid
	FROM pset7_cache AS cache
	LEFT JOIN pset7_view_cache_newest_epochs AS epochs
		ON cache.sid = epochs.sid AND epochs.epoch=cache.quote_epoch	
		WHERE  epochs.epoch IS NOT NULL 
	GROUP BY epochs.sid;	
	
############################
#pset7_view_users_totals
############################
CREATE VIEW pset7_view_users_totals AS

	SELECT 
		users.uid,
		users.displayname,
		IFNULL(SUM(portfolios.number_of_stocks*cache.price),0)  AS stock_value,
		users.cash,
		IFNULL(SUM(portfolios.number_of_stocks*cache.price),0) + users.cash AS total_portfolio_value
		FROM
		pset7_users AS users
		LEFT JOIN pset7_portfolios AS portfolios
			ON portfolios.uid=users.uid
		LEFT JOIN pset7_view_cache_newest_price  AS cache
			ON cache.sid = portfolios.sid
		GROUP BY users.uid;


#################################
# pset7_portfolio_with_latest_price
#################################
CREATE VIEW pset7_view_portfolios_with_latest_price AS

	SELECT
		portfolios.uid,
		portfolios.sid,
		portfolios.number_of_stocks,
		portfolios.purchase_price,
		portfolios.purchase_time,
		cache.price as current_price
		FROM pset7_portfolios AS portfolios
		LEFT JOIN pset7_view_cache_newest_price AS cache
			ON cache.sid=portfolios.sid;
