-- HTS KB content type

SELECT n.nid, n.vid, n.type, n.title, n.uid, n.created, n.changed, b.body_value, b.body_summary, b.body_format,
c.field_citation_value, c.field_citation_format, y.field_yeartext_value, o.field_order_value, w.field_new_value,
l.field_link_url, l.field_link_title, k.field_hts_kb_type_tid
FROM node n
INNER JOIN field_data_body b ON (n.nid = b.entity_id)
INNER JOIN field_data_field_citation c ON (n.nid = c.entity_id)
INNER JOIN field_data_field_yeartext y ON (n.nid = y.entity_id)
INNER JOIN field_data_field_new w ON (n.nid = w.entity_id)
INNER JOIN field_data_field_hts_kb_type k ON (n.nid = k.entity_id)
LEFT JOIN field_data_field_order o ON (n.nid = o.entity_id)
LEFT JOIN field_data_field_link l ON (n.nid = l.entity_id)
WHERE type = 'hts_kb_summaries'