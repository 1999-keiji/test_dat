CREATE PROCEDURE `replication_panel_state`(
    IN in_factory_code VARCHAR(15),
    IN in_bed_row TINYINT,
    IN in_bed_column TINYINT,
    IN in_date DATE
)
root:BEGIN
    # 変数宣言
    DECLARE tmp_panel_id BIGINT UNSIGNED;
    DECLARE tmp_factory_code VARCHAR(15);
    DECLARE tmp_factory_species_code VARCHAR(15);
    DECLARE tmp_growing_stage_sequence_number TINYINT UNSIGNED;
    DECLARE tmp_cycle_pattern VARCHAR(1);
    DECLARE tmp_date DATE;
    DECLARE tmp_x_coordinate_panel TINYINT;
    DECLARE tmp_y_movement_panel TINYINT;
    DECLARE tmp_number_of_panels TINYINT UNSIGNED;
    DECLARE tmp_growing_term TINYINT UNSIGNED;
    DECLARE tmp_work_on_monday BOOLEAN;
    DECLARE tmp_work_on_tuesday BOOLEAN;
    DECLARE tmp_work_on_wednesday BOOLEAN;
    DECLARE tmp_work_on_thursday BOOLEAN;
    DECLARE tmp_work_on_friday BOOLEAN;
    DECLARE tmp_work_on_saturday BOOLEAN;
    DECLARE tmp_work_on_sunday BOOLEAN;

    # 失敗時はロールバックに設定
    DECLARE EXIT HANDLER FOR SQLEXCEPTION ROLLBACK;

    # 指定ベッドの最終日を取得
    SELECT
        MAX(`date`) INTO tmp_date
    FROM
        panel_state
    WHERE
        factory_code = in_factory_code
        AND bed_row = in_bed_row
        AND bed_column = in_bed_column;
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
    SELECT
        *
    FROM
        panel_state
    WHERE
        factory_code = in_factory_code
        AND bed_row = in_bed_row
        AND bed_column = in_bed_column
        AND date = tmp_date
    ORDER BY panel_id;

    # 営業日フラグを取得
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_monday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 1;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_tuesday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 2;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_wednesday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 3;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_thursday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 4;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_friday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 5;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_saturday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 6;
    SELECT
        CASE WHEN COUNT(day_of_the_week) <> 0 THEN 1 ELSE 0 END
    INTO
        tmp_work_on_sunday
    FROM factory_working_days
    WHERE factory_code = in_factory_code
    AND day_of_the_week = 0;

    # 指定日を登録するまで繰り返し
    WHILE tmp_date < in_date DO
        # テンポラリーテーブルの情報から一番奥にあるパネルの情報を取得
        SELECT
            factory_code,
            factory_species_code,
            growing_stage_sequence_number,
            cycle_pattern,
            `date`,
            x_coordinate_panel
        INTO
            tmp_factory_code,
            tmp_factory_species_code,
            tmp_growing_stage_sequence_number,
            tmp_cycle_pattern,
            tmp_date,
            tmp_x_coordinate_panel
        FROM
            tmp_panel_state
        ORDER BY
            y_current_bed_position DESC,
            x_current_bed_position DESC
        LIMIT 1;

        # 営業日判定
        IF
            (tmp_work_on_monday    = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 2) OR
            (tmp_work_on_tuesday   = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 3) OR
            (tmp_work_on_wednesday = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 4) OR
            (tmp_work_on_thursday  = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 5) OR
            (tmp_work_on_friday    = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 6) OR
            (tmp_work_on_saturday  = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 7) OR
            (tmp_work_on_sunday    = 0 AND DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) = 1)
        THEN
            # パネル移動数を0に設定（挿入予定日が稼働日でない場合）
            SET tmp_number_of_panels := 0;
            SET tmp_y_movement_panel := 0;
        ELSE
            # パネル移動数を取得（挿入予定日が稼働日の場合）
            SELECT
                fgs.growing_term,
                fcpi.number_of_panels
            INTO
                tmp_growing_term,
                tmp_number_of_panels
            FROM
                factory_growing_stages fgs
            JOIN
                factory_cycle_pattern_items fcpi
                ON fcpi.factory_code = fgs.factory_code
                AND fcpi.cycle_pattern_sequence_number = fgs.cycle_pattern_sequence_number
            WHERE
                fgs.factory_code             = tmp_factory_code
                AND fgs.factory_species_code = tmp_factory_species_code
                AND fgs.sequence_number      = tmp_growing_stage_sequence_number
                AND fcpi.pattern             = tmp_cycle_pattern
                AND fcpi.day_of_the_week     = (DAYOFWEEK(DATE_ADD(tmp_date, INTERVAL 1 DAY)) - 1)
            LIMIT 1;

            # パネルY軸移動距離を算出
            SELECT truncate((tmp_number_of_panels / tmp_x_coordinate_panel) + .9, 0) INTO tmp_y_movement_panel;
        END IF;

        # 追加用情報を避難
        DROP TABLE IF EXISTS into_panel_state;

        CREATE TEMPORARY TABLE into_panel_state
        SELECT
            *
        FROM
            tmp_panel_state
        WHERE
            y_current_bed_position = 1
            AND x_current_bed_position = 1
        LIMIT 1;

        UPDATE into_panel_state
        SET
            `date` = DATE_ADD(tmp_date, INTERVAL 1 DAY),
            stage_start_date = DATE_ADD(tmp_date, INTERVAL 1 DAY),
            next_growth_stage_date = DATE_ADD(tmp_date, INTERVAL(tmp_growing_term + 1) DAY),
            created_by = 'BATCH',
            created_at = now(),
            updated_by = 'BATCH',
            updated_at = now();

        # パネル情報更新
        UPDATE tmp_panel_state
        SET
            `date` = DATE_ADD(`date`, INTERVAL 1 DAY),
            y_current_bed_position = y_current_bed_position + tmp_y_movement_panel,
            created_by = 'BATCH',
            created_at = now(),
            updated_by = 'BATCH',
            updated_at = now();

        # 次生育ステージ移植日を過ぎた情報やY軸の上限を超えたパネルを削除
        DELETE
        FROM
            tmp_panel_state
        WHERE
            (`date` >= next_growth_stage_date)
            OR (y_current_bed_position > y_coordinate_panel);

        # 新しいパネルを追加
        SELECT
            MAX(panel_id)
        INTO
            tmp_panel_id
        FROM
            panel_state
        WHERE
            factory_code = in_factory_code;

        SET @num:= 0;
        WHILE @num < tmp_number_of_panels DO
            # パネルID, X座標, Y座標を更新
            UPDATE
                into_panel_state
            SET
                panel_id = tmp_panel_id + @num + 1,
                x_current_bed_position = (@num % tmp_x_coordinate_panel) + 1,
                y_current_bed_position = TRUNCATE(@num / tmp_x_coordinate_panel, 0) + 1;

            INSERT INTO tmp_panel_state SELECT * FROM into_panel_state;
            set @num := @num+1;
        END while;

        DROP TABLE IF EXISTS into_panel_state;

        # 元テーブルに情報を挿入
        INSERT INTO panel_state SELECT * FROM tmp_panel_state;

        # 登録した日付を取得
        SELECT max(date) INTO tmp_date FROM tmp_panel_state;
    END WHILE;

    # テンポラリーテーブルの削除
    DROP TABLE tmp_panel_state;

    # コミット
    COMMIT;
END
