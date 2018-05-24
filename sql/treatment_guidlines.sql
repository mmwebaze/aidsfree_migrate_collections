-- Treatment guideline collection
SELECT n.nid, n.title, fdpo.field_treatment_path_option_value, fdpo.bundle, dffr.field_regimen_value,
fl.field_first_line_value, fl.field_first_line_format, sl.	field_second_line_value, sl.field_second_line_format,
tl.field_third_line_value, tl.field_third_line_format FROM field_data_field_treatment_path_option fdpo
INNER JOIN field_data_field_regimen dffr ON (fdpo.field_treatment_path_option_value = dffr.entity_id)
INNER JOIN node n ON (n.nid = fdpo.entity_id)
INNER JOIN field_data_field_first_line fl ON (dffr.field_regimen_value = fl.entity_id)
INNER JOIN field_data_field_second_line sl ON (dffr.field_regimen_value = sl.entity_id)
INNER JOIN field_data_field_third_line tl ON (dffr.field_regimen_value = tl.entity_id)
WHERE n.nid = 17406

--Events

SELECT n.nid, n.title, s.field_meet_the_speakers_value FROM field_data_field_meet_the_speakers s
INNER JOIN node n ON (n.nid = s.entity_id)
--Events -> Meet the speakers

SELECT n.nid, n.title, s.bundle, s.field_meet_the_speakers_value, hs.field_headshot_fid, hs.field_headshot_alt,
hs.field_headshot_title, hs.field_headshot_width, hs.field_headshot_height, sn.field_speakername_value,
sn.field_speakername_format, b.field_bio_value, b.field_bio_format FROM field_data_field_meet_the_speakers s
INNER JOIN node n ON (n.nid = s.entity_id)
LEFT JOIN field_data_field_headshot hs ON (hs.	entity_id = s.field_meet_the_speakers_value)
LEFT JOIN field_data_field_speakername sn ON (sn.entity_id = s.field_meet_the_speakers_value)
LEFT JOIN field_data_field_bio b ON (b.entity_id = s.field_meet_the_speakers_value)
WHERE n.nid = 956

--Event -> Presentations

SELECT n.nid, n.title, p.bundle, p.field_presentations_value, ff.field_file_fid, au.field_author_s__value,
au.field_author_s__format, fs.field_session_value, fs.field_session_format, fd.field_day_value, fl.field_websitelink_url, fl.field_websitelink_title
FROM field_data_field_presentations p
INNER JOIN node n ON (n.nid = p.entity_id)
LEFT JOIN field_data_field_file ff ON (ff.entity_id = p.field_presentations_value)
LEFT JOIN field_data_field_author_s_ au ON (au.entity_id = p.field_presentations_value)
LEFT OUTER JOIN field_data_field_session fs ON (fs.entity_id = p.field_presentations_value)
LEFT OUTER JOIN field_data_field_day fd ON (fd.entity_id = p.field_presentations_value)
LEFT JOIN field_data_field_websitelink fl ON (fl.entity_id = p.field_presentations_value)

SELECT p.id as para_id,d.entity_id, d.field_day_value, np.entity_id, np.field_presentations_target_id,n.nid FROM paragraph__field_day d
INNER JOIN paragraphs_item p ON (p.id = d.entity_id)
INNER JOIN node__field_presentations np ON (np.field_presentations_target_id = p.id)
INNER JOIN node n ON (n.nid = np.entity_id) WHERE n.nid = 18286;

--Event -> Video
SELECT n.nid, n.title, vs.field_video_s__value,ft.field_transcript_fid,ft.field_transcript_display,ft.field_transcript_description,
fw.field_websitelink_url, fw.field_websitelink_title FROM field_data_field_video fv
INNER JOIN node n ON (n.nid = vs.entity_id)
INNER JOIN field_collection_item fc ON (fv.entity_id = fc.item_id)
LEFT JOIN field_data_field_video_s_ vs (vs.entity_id = vs.entity_id)
LEFT JOIN field_data_field_transcript ft ON (ft.entity_id = vs.entity_id)
LEFT JOIN field_data_field_websitelink fw ON (fw.entity_id = vs.entity_id)
WHERE n.nid = 956

--

SELECT n.nid, n.title, n.type, fv.entity_id, fv.field_video_video_url, fv.field_video_thumbnail_path,
fv.field_video_video_data, fv.field_video_description, vs.field_video_s__value,ft.field_transcript_fid,
ft.field_transcript_display, ft.field_transcript_description
FROM field_data_field_video fv
INNER JOIN field_data_field_video_s_ vs ON (vs.field_video_s__value = fv.entity_id)
INNER JOIN node n ON (n.nid = vs.field_video_s__value)
LEFT JOIN field_data_field_transcript ft ON (ft.entity_id = fv.entity_id)
WHERE n.nid = 18286;

--transcripts--links not added as there are no links in this current database

SELECT n.nid, n.title, n.type, vs.field_video_s__value,vs.bundle,ft.entity_id, ft.field_transcript_fid, ft.field_transcript_display,
ft.field_transcript_description,fv.field_video_video_url, fv.field_video_thumbnail_path,fv.field_video_video_data, fv.field_video_description
FROM field_data_field_video_s_ vs
LEFT JOIN node n ON (n.nid = vs.entity_id)
LEFT JOIN field_data_field_transcript ft ON (ft.entity_id = vs.field_video_s__value)
LEFT JOIN field_data_field_video fv ON (fv.entity_id = vs.field_video_s__value)
WHERE n.nid = 18286;

