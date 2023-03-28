DROP procedure IF EXISTS `fix_planned_arrangement_detail_status_work`;
CREATE PROCEDURE `fix_planned_arrangement_detail_status_work`(
    IN in_factory_code varchar(15),
    IN in_simulation_id int(11),
    IN in_factory_species_code varchar(15),
    IN in_updated_by varchar(15)
)
BEGIN
# 生産計画配置状況WTの確定情報と仕掛かり情報をマージ
DROP TABLE IF EXISTS tmp_padsw;
CREATE TEMPORARY TABLE tmp_padsw 
SELECT 
    proc_table.factory_code,
    proc_table.simulation_id,
    proc_table.factory_species_code,
    1 as display_kubun,
    proc_table.date,
    proc_table.bed_row,
    proc_table.bed_column,
    ifnull(proc_table.panel_status_1, fix_table.panel_status_1) as panel_status_1,
    ifnull(proc_table.panel_status_2, fix_table.panel_status_2) as panel_status_2,
    ifnull(proc_table.panel_status_3, fix_table.panel_status_3) as panel_status_3,
    ifnull(proc_table.panel_status_4, fix_table.panel_status_4) as panel_status_4,
    ifnull(proc_table.panel_status_5, fix_table.panel_status_5) as panel_status_5,
    ifnull(proc_table.panel_status_6, fix_table.panel_status_6) as panel_status_6,
    ifnull(proc_table.panel_status_7, fix_table.panel_status_7) as panel_status_7,
    ifnull(proc_table.panel_status_8, fix_table.panel_status_8) as panel_status_8,
    ifnull(proc_table.panel_status_9, fix_table.panel_status_9) as panel_status_9,
    ifnull(proc_table.panel_status_10,fix_table.panel_status_10) as panel_status_10,
    ifnull(proc_table.panel_status_11,fix_table.panel_status_11) as panel_status_11,
    ifnull(proc_table.panel_status_12,fix_table.panel_status_12) as panel_status_12,
    ifnull(proc_table.panel_status_13,fix_table.panel_status_13) as panel_status_13,
    ifnull(proc_table.panel_status_14,fix_table.panel_status_14) as panel_status_14,
    ifnull(proc_table.panel_status_15,fix_table.panel_status_15) as panel_status_15,
    ifnull(proc_table.panel_status_16,fix_table.panel_status_16) as panel_status_16,
    ifnull(proc_table.panel_status_17,fix_table.panel_status_17) as panel_status_17,
    ifnull(proc_table.panel_status_18,fix_table.panel_status_18) as panel_status_18,
    ifnull(proc_table.panel_status_19,fix_table.panel_status_19) as panel_status_19,
    ifnull(proc_table.panel_status_20,fix_table.panel_status_20) as panel_status_20,
    ifnull(proc_table.panel_status_21,fix_table.panel_status_21) as panel_status_21,
    ifnull(proc_table.panel_status_22,fix_table.panel_status_22) as panel_status_22,
    ifnull(proc_table.panel_status_23,fix_table.panel_status_23) as panel_status_23,
    ifnull(proc_table.panel_status_24,fix_table.panel_status_24) as panel_status_24,
    ifnull(proc_table.panel_status_25,fix_table.panel_status_25) as panel_status_25,
    ifnull(proc_table.panel_status_26,fix_table.panel_status_26) as panel_status_26,
    ifnull(proc_table.panel_status_27,fix_table.panel_status_27) as panel_status_27,
    ifnull(proc_table.panel_status_28,fix_table.panel_status_28) as panel_status_28,
    ifnull(proc_table.panel_status_29,fix_table.panel_status_29) as panel_status_29,
    ifnull(proc_table.panel_status_30,fix_table.panel_status_30) as panel_status_30,
    ifnull(proc_table.panel_status_31,fix_table.panel_status_31) as panel_status_31,
    ifnull(proc_table.panel_status_32,fix_table.panel_status_32) as panel_status_32,
    ifnull(proc_table.panel_status_33,fix_table.panel_status_33) as panel_status_33,
    ifnull(proc_table.panel_status_34,fix_table.panel_status_34) as panel_status_34,
    ifnull(proc_table.panel_status_35,fix_table.panel_status_35) as panel_status_35,
    ifnull(proc_table.panel_status_36,fix_table.panel_status_36) as panel_status_36,
    ifnull(proc_table.panel_status_37,fix_table.panel_status_37) as panel_status_37,
    ifnull(proc_table.panel_status_38,fix_table.panel_status_38) as panel_status_38,
    ifnull(proc_table.panel_status_39,fix_table.panel_status_39) as panel_status_39,
    ifnull(proc_table.panel_status_40,fix_table.panel_status_40) as panel_status_40,
    ifnull(proc_table.panel_status_41,fix_table.panel_status_41) as panel_status_41,
    ifnull(proc_table.panel_status_42,fix_table.panel_status_42) as panel_status_42,
    ifnull(proc_table.panel_status_43,fix_table.panel_status_43) as panel_status_43,
    ifnull(proc_table.panel_status_44,fix_table.panel_status_44) as panel_status_44,
    ifnull(proc_table.panel_status_45,fix_table.panel_status_45) as panel_status_45,
    ifnull(proc_table.panel_status_46,fix_table.panel_status_46) as panel_status_46,
    ifnull(proc_table.panel_status_47,fix_table.panel_status_47) as panel_status_47,
    ifnull(proc_table.panel_status_48,fix_table.panel_status_48) as panel_status_48,
    ifnull(proc_table.panel_status_49,fix_table.panel_status_49) as panel_status_49,
    ifnull(proc_table.panel_status_50,fix_table.panel_status_50) as panel_status_50,
    ifnull(proc_table.panel_status_51,fix_table.panel_status_51) as panel_status_51,
    ifnull(proc_table.panel_status_52,fix_table.panel_status_52) as panel_status_52,
    ifnull(proc_table.panel_status_53,fix_table.panel_status_53) as panel_status_53,
    ifnull(proc_table.panel_status_54,fix_table.panel_status_54) as panel_status_54,
    ifnull(proc_table.panel_status_55,fix_table.panel_status_55) as panel_status_55,
    ifnull(proc_table.panel_status_56,fix_table.panel_status_56) as panel_status_56,
    ifnull(proc_table.panel_status_57,fix_table.panel_status_57) as panel_status_57,
    ifnull(proc_table.panel_status_58,fix_table.panel_status_58) as panel_status_58,
    ifnull(proc_table.panel_status_59,fix_table.panel_status_59) as panel_status_59,
    ifnull(proc_table.panel_status_60,fix_table.panel_status_60) as panel_status_60,
    ifnull(proc_table.panel_status_61,fix_table.panel_status_61) as panel_status_61,
    ifnull(proc_table.panel_status_62,fix_table.panel_status_62) as panel_status_62,
    ifnull(proc_table.panel_status_63,fix_table.panel_status_63) as panel_status_63,
    ifnull(proc_table.panel_status_64,fix_table.panel_status_64) as panel_status_64,
    ifnull(proc_table.panel_status_65,fix_table.panel_status_65) as panel_status_65,
    ifnull(proc_table.panel_status_66,fix_table.panel_status_66) as panel_status_66,
    ifnull(proc_table.panel_status_67,fix_table.panel_status_67) as panel_status_67,
    ifnull(proc_table.panel_status_68,fix_table.panel_status_68) as panel_status_68,
    ifnull(proc_table.panel_status_69,fix_table.panel_status_69) as panel_status_69,
    ifnull(proc_table.panel_status_70,fix_table.panel_status_70) as panel_status_70,
    ifnull(proc_table.panel_status_71,fix_table.panel_status_71) as panel_status_71,
    ifnull(proc_table.panel_status_72,fix_table.panel_status_72) as panel_status_72,
    ifnull(proc_table.panel_status_73,fix_table.panel_status_73) as panel_status_73,
    ifnull(proc_table.panel_status_74,fix_table.panel_status_74) as panel_status_74,
    ifnull(proc_table.panel_status_75,fix_table.panel_status_75) as panel_status_75,
    ifnull(proc_table.panel_status_76,fix_table.panel_status_76) as panel_status_76,
    ifnull(proc_table.panel_status_77,fix_table.panel_status_77) as panel_status_77,
    ifnull(proc_table.panel_status_78,fix_table.panel_status_78) as panel_status_78,
    ifnull(proc_table.panel_status_79,fix_table.panel_status_79) as panel_status_79,
    ifnull(proc_table.panel_status_80,fix_table.panel_status_80) as panel_status_80,
    ifnull(proc_table.panel_status_81,fix_table.panel_status_81) as panel_status_81,
    ifnull(proc_table.panel_status_82,fix_table.panel_status_82) as panel_status_82,
    ifnull(proc_table.panel_status_83,fix_table.panel_status_83) as panel_status_83,
    ifnull(proc_table.panel_status_84,fix_table.panel_status_84) as panel_status_84,
    ifnull(proc_table.panel_status_85,fix_table.panel_status_85) as panel_status_85,
    ifnull(proc_table.panel_status_86,fix_table.panel_status_86) as panel_status_86,
    ifnull(proc_table.panel_status_87,fix_table.panel_status_87) as panel_status_87,
    ifnull(proc_table.panel_status_88,fix_table.panel_status_88) as panel_status_88,
    ifnull(proc_table.panel_status_89,fix_table.panel_status_89) as panel_status_89,
    ifnull(proc_table.panel_status_90,fix_table.panel_status_90) as panel_status_90,
    ifnull(proc_table.panel_status_91,fix_table.panel_status_91) as panel_status_91,
    ifnull(proc_table.panel_status_92,fix_table.panel_status_92) as panel_status_92,
    ifnull(proc_table.panel_status_93,fix_table.panel_status_93) as panel_status_93,
    ifnull(proc_table.panel_status_94,fix_table.panel_status_94) as panel_status_94,
    ifnull(proc_table.panel_status_95,fix_table.panel_status_95) as panel_status_95,
    ifnull(proc_table.panel_status_96,fix_table.panel_status_96) as panel_status_96,
    ifnull(proc_table.panel_status_97,fix_table.panel_status_97) as panel_status_97,
    ifnull(proc_table.panel_status_98,fix_table.panel_status_98) as panel_status_98,
    ifnull(proc_table.panel_status_99,fix_table.panel_status_99) as panel_status_99,
    ifnull(proc_table.panel_status_100,fix_table.panel_status_100) as panel_status_100,
    proc_table.created_by,
    proc_table.created_at,
    in_updated_by as updated_by,
    now() as updated_at
FROM 
    (SELECT * FROM planned_arrangement_detail_status_work where display_kubun=2) as proc_table
    left outer join (SELECT * FROM planned_arrangement_detail_status_work where display_kubun=1) as fix_table
        on proc_table.factory_code = fix_table.factory_code
        and proc_table.simulation_id = fix_table.simulation_id
        and proc_table.factory_species_code = fix_table.factory_species_code
        and proc_table.date = fix_table.date
        and proc_table.bed_row = fix_table.bed_row
        and proc_table.bed_column = fix_table.bed_column
WHERE 
    proc_table.factory_code = in_factory_code and 
    proc_table.simulation_id = in_simulation_id and 
    proc_table.factory_species_code = in_factory_species_code;
# テーブル更新
REPLACE INTO planned_arrangement_detail_status_work select * from tmp_padsw;
# テンポラリーテーブル削除
DROP TABLE IF EXISTS tmp_padsw;
END
