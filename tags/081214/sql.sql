SELECT 
	gry.path as path, 
	tags.name as tag, 
	pic.pid as id, 
	pic.alttext as alt, 
	pic.filename , 
	tags.term_id as tid 
FROM 
	ft_ngg_gallery as gry, 
	ft_ngg_pictures as pic, 
	ft_terms as tags, 
	ft_term_relationships as p2t 
where	
	pic.galleryid=gry.gid and pic.pid=p2t.object_id and p2t.term_taxonomy_id=tags.term_id 
order by 
	pic.pid desc limit 1