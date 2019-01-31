-- создание view foto_category_view

drop VIEW foto_category_view;
CREATE VIEW foto_category_view AS 
	select 
		fuc.*,
		cat.customer_id,
		cat.name category_name,
		cat.status,
		cust.name customer_name,
		cust.name_official,
		cust.email,
		cust.phone_number,
		cust.representative,
        r.id region_id,
        r.name region_name,
		count(fcat.id) foto_number,
		(fcat.price * count(fcat.id)) AS category_sum 
	from 
		foto_upload_category fuc
		left join category cat on (cat.id=fuc.category_id)
		left join customer cust on (cust.id= cat.customer_id)
		inner join foto_category fcat on (fcat.foto_upload_category_id=fuc.id)
        inner join foto_upload fu on (fuc.`foto_upload_id`=fu.id)
        inner join region r on (fu.`region_id`=r.`id`)

	group by fuc.id;


