DROP procedure IF EXISTS `replication_panel_state`;
CREATE PROCEDURE `replication_panel_state`(
    IN in_factory_code varchar(15),
    IN in_bed_row tinyint(3),
    IN in_bed_column tinyint(3),
    IN in_date date
)
root:BEGIN
    # 変数宣言
    DECLARE tmp_panel_id bigint(20) unsigned;
    DECLARE tmp_factory_code varchar(15);
    DECLARE tmp_factory_species_code varchar(15);
    DECLARE tmp_growing_stage_sequence_number tinyint(3) unsigned;
    DECLARE tmp_cycle_pattern varchar(1);
    DECLARE tmp_date date;
    DECLARE tmp_x_coordinate_panel tinyint(3);
    DECLARE tmp_y_movement_panel tinyint(3);
    DECLARE tmp_number_of_panels tinyint(3) unsigned;
    DECLARE tmp_growing_term tinyint(3) unsigned;
    DECLARE tmp_work_on_saturday tinyint(1);
    DECLARE tmp_work_on_sunday tinyint(1);
    # 失敗時はロールバックに設定
    DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;
    # 指定ベッドの最終日を取得
    SELECT max(date) into tmp_date FROM panel_state WHERE factory_code = in_factory_code and bed_row = in_bed_row and bed_column = in_bed_column;
    # 最終日が指定日以降だった場合
    IF tmp_date >= in_date THEN
        # その日までデータがあるので、処理を終了
        LEAVE root;
    END IF;
    # トランザクション開始
    START TRANSACTION;
    # 指定ベッドの最終日の情報でテンポラリーテーブルを作成
    DROP TABLE IF EXISTS tmp_panel_state;
    CREATE TEMPORARY TABLE tmp_panel_state
    SELECT * FROM panel_state 
    WHERE 
        factory_code = in_factory_code 
        AND bed_row = in_bed_row 
        AND bed_column = in_bed_column 
        AND date = tmp_date
    ORDER BY panel_id;
    # 営業日フラグを取得
    SELECT work_on_saturday, work_on_sunday INTO tmp_work_on_saturday, tmp_work_on_sunday FROM factories WHERE factory_code = in_factory_code;
    # 指定日を登録するまで繰り返し
    while tmp_date < in_date do
        # テンポラリーテーブルの情報から一番奥にあるパネルの情報を取得
        SELECT 
            factory_code, factory_species_code, growing_stage_sequence_number, cycle_pattern, date ,x_coordinate_panel 
            INTO tmp_factory_code, tmp_factory_species_code, tmp_growing_stage_sequence_number, tmp_cycle_pattern, tmp_date, tmp_x_coordinate_panel 
        FROM 
            tmp_panel_state ORDER BY y_current_bed_position desc, x_current_bed_position desc LIMIT 1;
        # 営業日判定
        IF (tmp_work_on_saturday = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 day))=7) or (tmp_work_on_sunday = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 day)) = 1) THEN
            # パネル移動数を0に設定（挿入予定日が稼働日でない場合）
            set tmp_number_of_panels := 0;
            set tmp_y_movement_panel := 0;
        ELSE
            # パネル移動数を取得（挿入予定日が稼働日の場合）
            SELECT
                factory_growing_stages.growing_term,factory_cycle_pattern_items.number_of_panels into tmp_growing_term,tmp_number_of_panels
            FROM factory_growing_stages join factory_cycle_pattern_items 
                on factory_growing_stages.factory_code = factory_cycle_pattern_items.factory_code
                and factory_growing_stages.cycle_pattern_sequence_number = factory_cycle_pattern_items.cycle_pattern_sequence_number
            WHERE
                factory_growing_stages.factory_code             = tmp_factory_code
                AND factory_growing_stages.factory_species_code = tmp_factory_species_code
                AND factory_growing_stages.sequence_number      = tmp_growing_stage_sequence_number
                AND factory_cycle_pattern_items.pattern         = tmp_cycle_pattern
                AND factory_cycle_pattern_items.day_of_the_week = DAYOFWEEK(tmp_date)
            LIMIT 1;
            # パネルY軸移動距離を算出
            SELECT truncate((tmp_number_of_panels/tmp_x_coordinate_panel)+.9,0) INTO tmp_y_movement_panel;
        END IF;
        # 追加用情報を避難
        DROP TABLE IF EXISTS into_panel_state;
        CREATE TEMPORARY TABLE into_panel_state SELECT * FROM tmp_panel_state WHERE y_current_bed_position=1 and x_current_bed_position=1 LIMIT 1;
        UPDATE into_panel_state 
        SET date = DATE_ADD(tmp_date, INTERVAL 1 day),
            stage_start_date = DATE_ADD(tmp_date, INTERVAL 1 day),
            next_growth_stage_date = DATE_ADD(tmp_date, INTERVAL (tmp_growing_term+1) day),
            created_by = 'BATCH', created_at = now(),
            updated_by = 'BATCH', updated_at = now();
        # パネル情報更新
        UPDATE tmp_panel_state 
        SET date = DATE_ADD(date, INTERVAL 1 day), 
            y_current_bed_position=y_current_bed_position + tmp_y_movement_panel, 
            created_by = 'BATCH', created_at = now(), 
            updated_by = 'BATCH', updated_at = now();
        # 次生育ステージ移植日を過ぎた情報やY軸の上限を超えたパネルを削除
        DELETE FROM tmp_panel_state 
        WHERE (date >= next_growth_stage_date) or (y_current_bed_position > y_coordinate_panel);
        # 新しいパネルを追加
        SELECT max(panel_id) into tmp_panel_id from panel_state where factory_code = in_factory_code;
        set @num:= 0;
        while @num < tmp_number_of_panels do
            # パネルID,X座標,Y座標を更新
            UPDATE into_panel_state 
            SET 
                panel_id = tmp_panel_id + @num + 1,
                x_current_bed_position = (@num % tmp_x_coordinate_panel) + 1,
                y_current_bed_position = truncate(@num / tmp_x_coordinate_panel , 0) + 1;        
            INSERT INTO tmp_panel_state SELECT * FROM into_panel_state;
            set @num := @num+1;
        end while;
        DROP TABLE IF EXISTS into_panel_state;
        # 元テーブルに情報を挿入
        INSERT INTO panel_state SELECT * FROM tmp_panel_state;
        # 登録した日付を取得
        SELECT max(date) into tmp_date FROM tmp_panel_state;
    end while;
    # テンポラリーテーブルの削除
    DROP TABLE tmp_panel_state;
    # コミット
    COMMIT;
END