DROP procedure IF EXISTS `fix_planned_arrangement_status_work`;
CREATE PROCEDURE `fix_planned_arrangement_status_work`(
    IN in_factory_code varchar(15),
    IN in_simulation_id int(11),
    IN in_factory_species_code varchar(15),
    IN in_updated_by varchar(15)
)
BEGIN
# 生産計画配置状況WTの確定情報と仕掛かり情報をマージ
DROP TABLE IF EXISTS tmp_pasw;
CREATE TEMPORARY TABLE tmp_pasw 
SELECT 
    proc_table.factory_code,
    proc_table.simulation_id,
    proc_table.factory_species_code,
    1 as display_kubun,
    proc_table.date,
    proc_table.bed_column,
    proc_table.bed_row_number,
    ifnull(proc_table.growing_stages_count_1,fix_table.growing_stages_count_1) as growing_stages_count_1,
    ifnull(proc_table.growing_stages_count_2,fix_table.growing_stages_count_2) as growing_stages_count_2,
    ifnull(proc_table.growing_stages_count_3,fix_table.growing_stages_count_3) as growing_stages_count_3,
    ifnull(proc_table.growing_stages_count_4,fix_table.growing_stages_count_4) as growing_stages_count_4,
    ifnull(proc_table.growing_stages_count_5,fix_table.growing_stages_count_5) as growing_stages_count_5,
    ifnull(proc_table.growing_stages_count_6,fix_table.growing_stages_count_6) as growing_stages_count_6,
    ifnull(proc_table.growing_stages_count_7,fix_table.growing_stages_count_7) as growing_stages_count_7,
    ifnull(proc_table.growing_stages_count_8,fix_table.growing_stages_count_8) as growing_stages_count_8,
    ifnull(proc_table.growing_stages_count_9,fix_table.growing_stages_count_9) as growing_stages_count_9,
    ifnull(proc_table.growing_stages_count_10,fix_table.growing_stages_count_10) as growing_stages_count_10,
    ifnull(proc_table.growing_stages_count_11,fix_table.growing_stages_count_11) as growing_stages_count_11,
    ifnull(proc_table.growing_stages_count_12,fix_table.growing_stages_count_12) as growing_stages_count_12,
    ifnull(proc_table.growing_stages_count_13,fix_table.growing_stages_count_13) as growing_stages_count_13,
    ifnull(proc_table.growing_stages_count_14,fix_table.growing_stages_count_14) as growing_stages_count_14,
    ifnull(proc_table.growing_stages_count_15,fix_table.growing_stages_count_15) as growing_stages_count_15,
    ifnull(proc_table.growing_stages_count_16,fix_table.growing_stages_count_16) as growing_stages_count_16,
    ifnull(proc_table.growing_stages_count_17,fix_table.growing_stages_count_17) as growing_stages_count_17,
    ifnull(proc_table.growing_stages_count_18,fix_table.growing_stages_count_18) as growing_stages_count_18,
    ifnull(proc_table.growing_stages_count_19,fix_table.growing_stages_count_19) as growing_stages_count_19,
    ifnull(proc_table.growing_stages_count_20,fix_table.growing_stages_count_20) as growing_stages_count_20,
    ifnull(proc_table.growing_stages_count_21,fix_table.growing_stages_count_21) as growing_stages_count_21,
    ifnull(proc_table.growing_stages_count_22,fix_table.growing_stages_count_22) as growing_stages_count_22,
    ifnull(proc_table.growing_stages_count_23,fix_table.growing_stages_count_23) as growing_stages_count_23,
    ifnull(proc_table.growing_stages_count_24,fix_table.growing_stages_count_24) as growing_stages_count_24,
    ifnull(proc_table.growing_stages_count_25,fix_table.growing_stages_count_25) as growing_stages_count_25,
    ifnull(proc_table.growing_stages_count_26,fix_table.growing_stages_count_26) as growing_stages_count_26,
    ifnull(proc_table.growing_stages_count_27,fix_table.growing_stages_count_27) as growing_stages_count_27,
    ifnull(proc_table.growing_stages_count_28,fix_table.growing_stages_count_28) as growing_stages_count_28,
    ifnull(proc_table.growing_stages_count_29,fix_table.growing_stages_count_29) as growing_stages_count_29,
    ifnull(proc_table.growing_stages_count_30,fix_table.growing_stages_count_30) as growing_stages_count_30,
    ifnull(proc_table.pattern_row_count_1,fix_table.pattern_row_count_1) as pattern_row_count_1,
    ifnull(proc_table.pattern_row_count_2,fix_table.pattern_row_count_2) as pattern_row_count_2,
    ifnull(proc_table.pattern_row_count_3,fix_table.pattern_row_count_3) as pattern_row_count_3,
    ifnull(proc_table.pattern_row_count_4,fix_table.pattern_row_count_4) as pattern_row_count_4,
    ifnull(proc_table.pattern_row_count_5,fix_table.pattern_row_count_5) as pattern_row_count_5,
    ifnull(proc_table.pattern_row_count_6,fix_table.pattern_row_count_6) as pattern_row_count_6,
    ifnull(proc_table.pattern_row_count_7,fix_table.pattern_row_count_7) as pattern_row_count_7,
    ifnull(proc_table.pattern_row_count_8,fix_table.pattern_row_count_8) as pattern_row_count_8,
    ifnull(proc_table.pattern_row_count_9,fix_table.pattern_row_count_9) as pattern_row_count_9,
    ifnull(proc_table.pattern_row_count_10,fix_table.pattern_row_count_10) as pattern_row_count_10,
    ifnull(proc_table.pattern_row_count_11,fix_table.pattern_row_count_11) as pattern_row_count_11,
    ifnull(proc_table.pattern_row_count_12,fix_table.pattern_row_count_12) as pattern_row_count_12,
    ifnull(proc_table.pattern_row_count_13,fix_table.pattern_row_count_13) as pattern_row_count_13,
    ifnull(proc_table.pattern_row_count_14,fix_table.pattern_row_count_14) as pattern_row_count_14,
    ifnull(proc_table.pattern_row_count_15,fix_table.pattern_row_count_15) as pattern_row_count_15,
    ifnull(proc_table.pattern_row_count_16,fix_table.pattern_row_count_16) as pattern_row_count_16,
    ifnull(proc_table.pattern_row_count_17,fix_table.pattern_row_count_17) as pattern_row_count_17,
    ifnull(proc_table.pattern_row_count_18,fix_table.pattern_row_count_18) as pattern_row_count_18,
    ifnull(proc_table.pattern_row_count_19,fix_table.pattern_row_count_19) as pattern_row_count_19,
    ifnull(proc_table.pattern_row_count_20,fix_table.pattern_row_count_20) as pattern_row_count_20,
    ifnull(proc_table.pattern_row_count_21,fix_table.pattern_row_count_21) as pattern_row_count_21,
    ifnull(proc_table.pattern_row_count_22,fix_table.pattern_row_count_22) as pattern_row_count_22,
    ifnull(proc_table.pattern_row_count_23,fix_table.pattern_row_count_23) as pattern_row_count_23,
    ifnull(proc_table.pattern_row_count_24,fix_table.pattern_row_count_24) as pattern_row_count_24,
    ifnull(proc_table.pattern_row_count_25,fix_table.pattern_row_count_25) as pattern_row_count_25,
    ifnull(proc_table.pattern_row_count_26,fix_table.pattern_row_count_26) as pattern_row_count_26,
    ifnull(proc_table.pattern_row_count_27,fix_table.pattern_row_count_27) as pattern_row_count_27,
    ifnull(proc_table.pattern_row_count_28,fix_table.pattern_row_count_28) as pattern_row_count_28,
    ifnull(proc_table.pattern_row_count_29,fix_table.pattern_row_count_29) as pattern_row_count_29,
    ifnull(proc_table.pattern_row_count_30,fix_table.pattern_row_count_30) as pattern_row_count_30,
    now() as fixed_at,
    proc_table.created_by,
    proc_table.created_at,
    in_updated_by as updated_by,
    now() as updated_at
FROM
    (SELECT * FROM planned_arrangement_status_work where display_kubun=2) as proc_table
    left outer join (SELECT * FROM planned_arrangement_status_work where display_kubun=1) as fix_table
        on proc_table.factory_code = fix_table.factory_code
        and proc_table.simulation_id = fix_table.simulation_id
        and proc_table.factory_species_code = fix_table.factory_species_code
        and proc_table.date = fix_table.date
        and proc_table.bed_column = fix_table.bed_column
WHERE 
    proc_table.factory_code = in_factory_code and 
    proc_table.simulation_id = in_simulation_id and 
    proc_table.factory_species_code = in_factory_species_code;
# テーブル更新
REPLACE INTO planned_arrangement_status_work select * from tmp_pasw;
# テンポラリーテーブル削除
DROP TABLE IF EXISTS tmp_pasw;
END
