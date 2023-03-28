<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted' => ':attributeを承認してください。',
    'active_url' => ':attributeは、有効なURLではありません。',
    'after' => ':attributeには、:date以降の日付を指定してください。',
    'after_or_equal' => ':attributeには、:date以降もしくは同日時を指定してください。',
    'alpha' => ':attributeには、アルファベッドのみ使用できます。',
    'alpha_dash' => ":attributeには、英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num' => ":attributeには、英数字('A-Z','a-z','0-9')が使用できます。",
    'alpha_period_dash' => ":attributeには、英数字('A-Z','a-z','0-9')とピリオドとハイフンと下線('.', '-','_')が使用できます。",
    'array' => ':attributeには、配列を指定してください。',
    'before' => ':attributeには、:date以前の日付を指定してください。',
    'before_or_equal' => ':attributeには、:date以前もしくは同日時を指定してください。',
    'between' => [
        'numeric' => ':attributeには、:minから、:maxまでの数字を指定してください。',
        'file' => ':attributeには、:min KBから:max KBまでのサイズのファイルを指定してください。',
        'string' => ':attributeは、:min文字から:max文字にしてください。',
        'array' => ':attributeの項目は、:min個から:max個にしてください。',
    ],
    'boolean' => ":attributeには、'true'か'false'を指定してください。",
    'confirmed' => ':attributeと:attribute確認が一致しません。',
    'date' => ':attributeは、正しい日付ではありません。',
    'date_format' => ":attributeの形式は、':format'と合いません。",
    'different' => ':attributeと:otherには、異なるものを指定してください。',
    'digits' => ':attributeは、:digits桁にしてください。',
    'digits_between' => ':attributeは、:min桁から:max桁にしてください。',
    'dimensions' => ':attributeは、正しい縦横比ではありません。',
    'distinct' => ':attributeに重複した値があります。',
    'email' => ':attributeは、有効なメールアドレス形式で指定してください。',
    'exists' => '選択された:attributeは、有効ではありません。',
    'file' => ':attributeはファイルでなければいけません。',
    'filled' => ':attributeは必須です。',
    'hankana' => ":attributeは半角カタカナを入力してください。",
    'image' => ':attributeには、画像を指定してください。',
    'in' => '選択された:attributeは、有効ではありません。',
    'in_array' => ':attributeは、:otherに存在しません。',
    'integer' => ':attributeには、整数を指定してください。',
    'ip' => ':attributeには、有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeはIPv4アドレスを指定してください。',
    'ipv6' => ':attributeはIPv6アドレスを指定してください。',
    'json' => ':attributeには、有効なJSON文字列を指定してください。',
    'max' => [
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'file' => ':attributeには、:max KB以下のファイルを指定してください。',
        'string' => ':attributeは、:max文字以下にしてください。',
        'array' => ':attributeの項目は、:max個以下にしてください。',
    ],
    'mimes' => ':attributeには、:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには、:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには、:min以上の数字を指定してください。',
        'file' => ':attributeには、:min KB以上のファイルを指定してください。',
        'string' => ':attributeは、:min文字以上にしてください。',
        'array' => ':attributeの項目は、:max個以上にしてください。',
    ],
    'not_in' => '選択された:attributeは、有効ではありません。',
    'numeric' => ':attributeには、数字を指定してください。',
    'present' => ':attributeは、必ず存在しなくてはいけません。',
    'regex' => ':attributeは、既定の形式で入力してください。',
    'required' => ':attributeは、必ず指定してください。',
    'required_if' => ':otherが:valueの場合、:attributeを指定してください。',
    'required_unless' => ':otherが:value以外の場合、:attributeを指定してください。',
    'required_with' => ':valuesが指定されている場合、:attributeも指定してください。',
    'required_with_all' => ':valuesが全て指定されている場合、:attributeも指定してください。',
    'required_without' => ':valuesが指定されていない場合、:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを指定してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'numeric' => ':attributeには、:sizeを指定してください。',
        'file' => ':attributeには、:size KBのファイルを指定してください。',
        'string' => ':attributeは、:size文字にしてください。',
        'array' => ':attributeの項目は、:size個にしてください。',
    ],
    'string' => ':attributeには、文字を指定してください。',
    'timezone' => ':attributeには、有効なタイムゾーンを指定してください。',
    'unique' => '指定の:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeは、有効なURL形式で指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'product_name' => [
            'required_if' => '手動登録の場合、商品名を必ず指定してください。'
        ],
        'species_code' => [
            'required_if' => '手動登録の場合、品種を必ず指定してください。'
        ],
        'result_addup_abbreviation' => [
            'required_if' => '手動登録の場合、実績集計略称を必ず指定してください。'
        ],
        'product_class' => [
            'required_if' => '手動登録の場合、製品区分を必ず指定してください。'
        ],
        'sales_order_unit_quantity' => [
            'required_if' => '手動登録の場合、受注単位数を必ず指定してください。'
        ],
        'minimum_sales_order_unit_quantity' => [
            'required_if' => '手動登録の場合、最低受注数を必ず指定してください。'
        ],
        'net_weight' => [
            'required_if' => '手動登録の場合、純重量を必ず指定してください。'
        ],
        'gross_weight' => [
            'required_if' => '手動登録の場合、総重量を必ず指定してください。'
        ],
        'depth' => [
            'required_if' => '手動登録の場合、縦サイズを必ず指定してください。'
        ],
        'width' => [
            'required_if' => '手動登録の場合、横サイズを必ず指定してください。'
        ],
        'height' => [
            'required_if' => '手動登録の場合、高さサイズを必ず指定してください。'
        ],
        'country_of_origin' => [
            'required_if' => '手動登録の場合、原産国を必ず指定してください。'
        ],
        'updataed_at' => [
            'required' => 'もういちど登録をやり直してください。'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'country_of_origin'                             => '原産国',
        'itm_class2'                                    => '汎用区分2',
        'itm_class3'                                    => '汎用区分3',
        'itm_class4'                                    => '汎用区分4',
        'itm_class5'                                    => '汎用区分5',
        'itm_flag1'                                     => '汎用フラグ1',
        'itm_flag2'                                     => '汎用フラグ2',
        'itm_flag3'                                     => '汎用フラグ3',
        'itm_flag4'                                     => '汎用フラグ4',
        'itm_flag5'                                     => '汎用フラグ5',
        'custom_product_flag'                           => 'カスタム品フラグ',
        'depth'                                         => '縦サイズ',
        'export_target_flag'                            => '輸出対象フラグ',
        'gross_weight'                                  => '総重量',
        'height'                                        => '高さサイズ',
        'lot_target_flag'                               => 'ロット採取対象フラグ',
        'minimum_sales_order_unit_quantity'             => '最低受注数',
        'net_weight'                                    => '純重量',
        'pickup_slip_message'                           => '出庫伝票コメント',
        'product_class'                                 => '製品区分',
        'product_large_category'                        => '実績集計コード(大カテゴリ)',
        'product_middle_category'                       => '実績集計コード(中カテゴリ)',
        'product_code'                                  => '商品コード',
        'product_name'                                  => '商品名',
        'country_code'                                  => '国コード',
        'postal_code'                                   => '郵便番号',
        'prefecture_code'                               => '都道府県コード',
        'address'                                       => '住所1',
        'address2'                                      => '住所2',
        'address3'                                      => '住所3',
        'abroad_address'                                => '海外住所1',
        'abroad_address2'                               => '海外住所2',
        'abroad_address3'                               => '海外住所3',
        'phone_number'                                  => '電話番号',
        'extension_number'                              => '内線番号',
        'fax_number'                                    => 'FAX番号',
        'mail_address'                                  => 'メールアドレス',
        'remark'                                        => '備考',
        'reserved_text1'                                => '予備文字項目1',
        'reserved_text2'                                => '予備文字項目2',
        'reserved_text3'                                => '予備文字項目3',
        'reserved_text4'                                => '予備文字項目4',
        'reserved_text5'                                => '予備文字項目5',
        'reserved_text6'                                => '予備文字項目6',
        'reserved_text7'                                => '予備文字項目7',
        'reserved_text8'                                => '予備文字項目8',
        'reserved_text9'                                => '予備文字項目9',
        'reserved_text10'                               => '予備文字項目10',
        'reserved_number1'                              => '予備数値項目1',
        'reserved_number2'                              => '予備数値項目2',
        'reserved_number3'                              => '予備数値項目3',
        'reserved_number4'                              => '予備数値項目4',
        'reserved_number5'                              => '予備数値項目5',
        'reserved_number6'                              => '予備数値項目6',
        'reserved_number7'                              => '予備数値項目7',
        'reserved_number8'                              => '予備数値項目8',
        'reserved_number9'                              => '予備数値項目9',
        'reserved_number10'                             => '予備数値項目10',
        'base_plus_delete_flag'                         => 'BASE+削除フラグ',
        'base_plus_created_at'                          => 'BASE+作成日時',
        'base_plus_user_created_by'                     => 'BASE+作成者',
        'base_plus_program_created_by'                  => 'BASE+作成プログラム',
        'base_plus_updated_at'                          => 'BASE+更新日',
        'base_plus_user_updated_by'                     => 'BASE+更新者',
        'base_plus_program_updated_by'                  => 'BASE+更新プログラム',
        'result_addup_code'                             => '実績集計コード',
        'result_addup_name'                             => '実績集計名称',
        'result_addup_abbreviation'                     => '実績集計略称',
        'sales_order_unit'                              => '受注単位',
        'sales_order_unit_quantity'                     => '受注単位数',
        'species_code'                                  => '品種コード',
        'species_name'                                  => '品種名',
        'statement_of_delivery_name'                    => '納品書品名',
        'width'                                         => '横サイズ',
        // エンドユーザ
        'end_user_code'                                 => 'エンドユーザコード',
        'application_started_on'                        => '適用開始日',
        'end_user_name'                                 => 'エンドユーザ名称1',
        'end_user_name2'                                => 'エンドユーザ名称2',
        'end_user_abbreviation'                         => 'エンドユーザ略称',
        'end_user_name_kana'                            => 'エンドユーザカナ名称',
        'end_user_name_english'                         => 'エンドユーザ英字名称',
        'end_user_staff_name'                           => 'エンドユーザ担当者名',
        'currency_code'                                 => '通貨コード',
        'seller_code'                                   => '販売担当コード',
        'seller_name'                                   => '販売担当者名',
        'pickup_slip_message'                           => '出荷伝票コメント',
        'statement_of_delivery_class'                   => '納品書区分',
        'statement_of_delivery_price_show_class'        => '納品書価格表示区分',
        'abroad_shipment_price_show_class'              => '海外出荷指示リスト価格表示区分',
        'export_managing_class'                         => '輸出管理区分',
        'export_exchange_rate_code'                     => '輸出建値コード',
        'remarks1'                                      => 'REMARKS1',
        'remarks2'                                      => 'REMARKS2',
        'remarks3'                                      => 'REMARKS3',
        'remarks4'                                      => 'REMARKS4',
        'remarks5'                                      => 'REMARKS5',
        'remarks6'                                      => 'REMARKS6',
        'loading_port_code'                             => '積地コード',
        'loading_port_name'                             => '地名（積地）',
        'drop_port_code'                                => '降地コード',
        'drop_port_name'                                => '指名（降地）',
        'exchange_rate_port_code'                       => '建値地コード',
        'exchange_rate_port_name'                       => '地名（建値地）',
        'lot_managing_target_flag'                      => 'ロット管理対象フラグ',
        'end_user_remark'                               => 'エンドユーザ備考',
        'end_user_request_number'                       => 'エンドユーザ要求番号',
        'statement_of_delivery_remark_class'            => '納品書備考印字区分',
        'statement_of_delivery_buyer_remark_class'      => '納品書発注者使用欄印字区分',
        'group_company_flag'                            => 'グループ会社フラグ',
        'company_code'                                  => '企業コード',
        'company_name'                                  => '企業名称',
        'company_abbreviation'                          => '企業略称',
        'company_name_kana'                             => '企業カナ名称',
        'company_name_english'                          => '企業英字名称',
        'company_group_code'                            => '企業グループコード',
        'company_group_name'                            => '企業グループ名称',
        'company_group_name_english'                    => '企業グループ英字',
        'factory_code'                                  => '工場',
        'factories'                                  => '各工場',
        // 納入先
        'delivery_destination_code'                     => '納入先コード',
        'delivery_destination_name'                     => '納入先名称1',
        'delivery_destination_name2'                    => '納入先名称2',
        'delivery_destination_abbreviation'             => '納入先略称',
        'delivery_destination_name_kana'                => '納入先カナ名称',
        'delivery_destination_name_english'             => '納入先英字名称',
        'staff_abbreviation'                            => '納入先担当者略称',
        'statement_of_delivery_message'                 => '納品書コメント',
        'statement_of_delivery_output_class'            => '納品書出力区分',
        'shipping_label_unnecessary_flag'               => '送り状不要フラグ',
        'export_target_flag'                            => '輸出対象フラグ',
        'shipment_way_class'                            => '納入先配送方法区分',
        'delivery_destination_class'                    => '納入先区分',
        'cii_company_code'                              => 'CII統一企業コード',
        'collection_request_remark'                     => '集荷依頼書備考',
        'fsystem_statement_of_delivery_output_class'    => 'Fシステム納品書出力区分',
        'statement_of_shipment_output_class'            => '出荷案内書出力区分',
        'can_display'                                   => '表示区分',
        // 得意先
        'customer_code'                                 => '得意先コード',
        'customer_name'                                 => '得意先名称',
        'customer_name1'                                => '得意先名称１',
        'customer_name2'                                => '得意先名称２',
        'customer_abbreviation'                         => '得意先略称',
        'customer_name_kana'                            => '得意先カナ名称',
        'customer_name_english'                         => '得意先英字名称',
        'closing_date'                                  => '請求締日',
        'payment_timing_month'                          => '入金サイト(月)',
        'payment_timing_date'                           => '入金サイト(日)',
        'basis_for_recording_sales'                     => '売上計上',
        'rounding_type'                                 => '端数処理',
        'order_cooperation'                             => 'Base+注文連携',
        // 法人
        'corporation_code'                              => '法人コード',
        'corporation_name'                              => '法人名',
        'corporation_abbreviation'                      => '法人略称',
        // 倉庫
        'warehouse_code'                                => '倉庫コード',
        'warehouse_name'                                => '倉庫名',
        'warehouse_abbreviation'                        => '倉庫略称',
        // 納入倉庫
        'delivery_lead_time'                            => '配送リードタイム',
        'shipment_lead_time'                            => '出荷リードタイム',
        // 工場商品特価
        'factory_product_sequence_number'               => '工場取扱商品',
        'application_started_on.*'                      => '適用開始日',
        'application_ended_on.*'                        => '適用終了日',
        'unit_price.*'                                  => '価格',
        'currency_code.*'                               => '通貨コード',
        // ユーザ
        'user_code'                                     => 'ユーザコード',
        'user_name'                                     => 'ユーザ名',
        'affiliation'                                   => '所属',
        'permissions'                                   => '権限',
        // 品種
        'add_species_code'                              => '品種コード',
        'add_species_name'                              => '品種名',
        'edit_species_name'                             => '品種名',
        'species_abbreviation'                          => '品種名略称',
        'species_converters'                            => '変換元実績集計コード',
        'species_converters.*.product_large_category'   => '実績集計コード(大カテゴリ)',
        'species_converters.*.product_middle_category'  => '実績集計コード(中カテゴリ)',
        // 仕入先
        'supplier_code'                                 => '仕入先コード',
        'supplier_name'                                 => '仕入先名称1',
        'supplier_name2'                                => '仕入先名称2',
        'supplier_abbreviation'                         => '仕入先略称',
        'supplier_name_kana'                            => '仕入先カナ名称',
        'supplier_name_english'                         => '仕入先英字名称',
        'supplier_staff_name'                           => '仕入先担当者名',
        'supplier_class'                                => '仕入先区分',
        // 工場
        'priority.*'                                    => '優先度',
        'factory_name'                                  => '工場名',
        'factory_abbreviation'                          => '工場略称',
        'symbolic_code'                                 => '工場識別コード',
        'global_gap_number'                             => 'GGAP認証ナンバー',
        'invoice_corporation_name'                      => '請求元の会社名',
        'invoice_postal_code'                           => '請求元の郵便番号',
        'invoice_address'                               => '請求元の住所',
        'invoice_phone_number'                          => '請求元の電話番号',
        'invoice_fax_number'                            => '請求元の住所',
        'invoice_bank_name'                             => '銀行名',
        'invoice_bank_branch_name'                      => '銀行支店名',
        'invoice_bank_account_number'                   => '口座番号',
        'invoice_bank_account_holder'                   => '振込先名義',
        'cycle_pattern_name'                            => 'サイクルパターン名',
        'pattern.*'                                     => 'サイクルパターン',
        'row'                                           => 'ベッド段',
        'column'                                        => 'ベッド列',
        'number_of_panels.*.*'                          => '移動パネル数',
        'number_of_floors'                              => '階数',
        'number_of_columns'                             => '列数',
        'number_of_circulation'                         => '循環数',
        'column_name.*'                                 => '列名',
        'factory_beds.*.*.*.x_coordinate_panel'         => 'X軸パネル数',
        'factory_beds.*.*.*.y_coordinate_panel'         => 'Y軸パネル数',
        'factory_beds.*.*.*.irradiation'                => '照明照射',
        'circulations.*'                                => '循環',
        // 工場取扱品種
        'species'                                       => '品種',
        'factory_species'                               => '工場品種コード',
        'factory_species_name'                          => '工場品種名',
        'weight'                                        => '重量',
        'growing_stage_name.*'                          => 'ステージ名',
        'growing_term.*'                                => '生育期間',
        'number_of_hole.*'                              => 'トレイ/パネル',
        'yield_rate.*'                                  => '歩留率',
        'cycle_pattern_sequence_number'                 => 'サイクルパターン',
        'cycle_pattern_sequence_number.*'               => 'サイクルパターン',
        // 工場取扱商品
        'factory_product_name'                          => '工場商品名',
        'factory_product_abbreviation'                  => '工場商品名略称',
        'number_of_heads'                               => '基本入り株数',
        'weight_per_number_of_heads'                    => '基本入り株数あたり重量',
        'input_group'                                   => '出来高入力グループ',
        'number_of_cases'                               => 'ケース入り数',
        'unit'                                          => '単位',
        'cost.*'                                        => '単価',
        // カレンダーマスタ
        'event'                                         => '行事',
        // 各階栽培株数 一覧
        'moving_panel_count_pattern.*.*.*'              => '移動パネル数パターン',
        'moving_bed_count_floor_pattern.*.*.*'          => '移動ベッド数フロアパターン',
        // 生産シミュレーション確定
        'fixed_at_begin'                                => '確定日FROM日時',
        'fixed_at_end'                                  => '確定日TO日時',
        // フォーキャストExcel取込
        'factory_producct_sequence_number'              => '工場取扱商品',
        // 注文データ変更
        'updated_at'                                    => '更新日時',
        'end_user_order_number'                         => 'エンドユーザ注文番号',
        'received_date'                                 => '注文日',
        'delivery_date'                                 => '納期',
        'shipping_date'                                 => '出荷日',
        'supplier_product_name'                         => '仕入先品名',
        'customer_product_name'                         => '得意先品名',
        'order_quantity'                                => '注文数',
        'place_order_unit_code'                         => '単位',
        'order_unit'                                    => '単価',
        'order_amount'                                  => '合価',
        'statement_delivery_price_display_class'        => '納品書価格表示区分',
        'basis_for_recording_sales_class'               => '売上計上基準区分',
        'recived_order_unit'                            => '受注単価',
        'customer_recived_order_unit'                   => '得意先受注合価',
        'small_peace_of_peper_type_code'                => '発注伝票種別コード',
        'collection_time_sequence_number'               => '集荷時間',
        'own_company_code'                              => '会社コード',
        'organization_name'                             => '組織名',
        'base_plus_end_user_code'                       => '最終顧客コード',
        'customer_staff_name'                           => '得意先担当者名',
        'purchase_staff_name'                           => '購買担当者名',
        'place_order_work_staff_name'                   => '発注業務担当者',
        'order_message'                                 => '備考',
        // 返品入力
        'returned_on'                                   => '返品日',
        //VVF基幹発注データ取込
        'supplier_place_order_unit'                     => '仕入先発注単価',
        'place_order_amount'                            => '発注合価',
        'place_order_unit'                              => '発注単価',
        'orders_sheet_issue_flag'                       => '注文書発行フラグ',
        'compleate_flag'                                => '完了フラグ',
        'unofficial_recived_order_flag'                 => '内示受注フラグ',
        'pickup_type_class'                             => '伝票種別区分',
        'pickup_type_code'                              => '伝票種別コード',
        'lease_flag'                                    => 'リースフラグ',
        'statement_delivery_class'                      => '納品書区分',
        'suite_class'                                   => '一式区分',
        'payment_installments_flag'                     => '分納フラグ',
        'delivery_compleate_flag'                       => '納入済フラグ',
        'shipment_stop_flag'                            => '出荷停止フラグ',
        'free_reason_code'                              => '無代理由コード',
        'invoice_display_total'                         => 'インボイス表示合価',
        'supplier_place_order_quantity'                 => '仕入先発注数',
        'supplier_purchase_compleate_quantity'          => '仕入先仕入済数',
        'supplier_closed_quantity'                      => '仕入先打切数',
        'place_order_quantity'                          => '発注数',
        'arrival_plan_quantity'                         => '仕入先入荷予定数',
        'arrival_compleate_quantity'                    => '入荷済数',
        'purchase_compleate_quantity'                   => '仕入済数',
        'cancellation_quantity'                         => '解約数',
        'cancellation_delivery_compleate_quantity'      => '解約受入済数',
        'inspection_circulation'                        => '検査部数',
        'recived_order_sales_compleate_quantity'        => '受注売上済数',
        'maintain_period_flag'                          => '期間保守フラグ',
        'compleat_flag'                                 => '完了フラグ',
        'edl_send_compleate_flag'                       => 'EDI送信済フラグ',
        'contract_flag'                                 => '請負契約フラグ',
        'repair_order_flag'                             => '修理オーダフラグ',
        'goods_quantity'                                => '現品数量',
        'lc_trade_flag'                                 => 'Ｌ／Ｃ取引フラグ',
        'invoice_issue_flag'                            => '送り状出力フラグ',
        'oversea_flag'                                  => '海外フラグ',
        'detail_payment_installments_flag'              => '明細分納フラグ',
        'customer_recived_order_quantity'               => '得意先受注数',
        'customer_closed_quantity'                      => '得意先打切数',
        'recived_order_quantity'                        => '受注数',
        'recived_order_unit_amount'                     => '受注単位',
        'customer_recived_order_total'                  => '得意先受注合価',
        'invoice_display_unit'                          => 'インボイス表示単価',
        // 作業指示書
        'working_date_from'                             => '作業日（FROM）',
        'working_date_to'                               => '作業日（TO）',
        'factory_species_code'                          => '工場品種',
        'working_date'                                  => '作業日',
        // 生産計画表
        'date_from'                                     => '日付',
        'date_range'                                    => '期間',
        // 生産シミュレーション追加
        'display_period'                                => '日付',
        // 生産・販売管理表サマリー
        'display_type'                                  => '表示切替',
        'display_term'                                  => '表示期間',
        'display_from'                                  => '表示期間',
        'week_term'                                     => '表示期間',
        'display_unit'                                  => '出荷表示',
        // 生販管理表出力
        'harvest_date'                                  => '収穫日',
        // 製品化実績一覧
        'harvesting_date'                               => '収穫日',
        'productized_result.triming'                    => 'トリミング',
        'productized_result.product_failure'            => '障害品',
        'productized_result.packing'                    => 'パッキング',
        'productized_result.crop_failure'               => '収穫廃棄',
        'productized_result.sample'                     => '検査サンプル',
        'productized_result.advanced_harvest'           => '前採り',
        'productized_result.weight_of_discarded'        => '廃棄重量',
        'productized_result_details.*.product_quantity' => '実績数量',
        // 出荷データ出力
        'harvesting_date.from'                          => '収穫日（FROM）',
        'harvesting_date.to'                            => '収穫日（TO）',
        'shipping_date.from'                            => '出荷日（FROM）',
        'shipping_date.to'                              => '出荷日（TO）',
        'delivery_date.from'                            => '納期（FROM）',
        'delivery_date.to'                              => '納期（TO）',
        // パスワード変更
        'current_password'                              => '現在のパスワード',
        'password'                                      => '新しいパスワード',
        // 運送会社
        'transport_company_name'                        => '運送会社名',
        'transport_branch_name'                         => '支店名',
        'transport_company_abbreviation'                => '運送会社略称',
        'transport_company_code'                        => '運送会社コード',
        // 集荷時間
        'collection_time'                               => '集荷時間',
        // 請求書出力
        'delivery_month'                                => '年月',
        // 出荷確定
        'shipping_date_from'                            => '出荷日（FROM）',
        'shipping_date_to'                              => '出荷日（TO）',
        'delivery_date_from'                            => '納入日（FROM）',
        'delivery_date_to'                              => '納入日（TO）',
        'order_number'                                  => '注文番号',
        'base_plus_order_number'                        => 'BASE+注文番号',
        'base_plus_order_chapter_number'                => 'BASE+注文項番',
        // 在庫一覧
        'harvesting_date_from'                          => '収穫日（FROM）',
        'harvesting_date_to'                            => '収穫日（TO）',
        'working_date_from'                             => '作業日（FROM）',
        'working_date_to'                               => '作業日（TO）',
        // 在庫移動
        'stock_quantity'                                => '在庫数量',
        'moving_start_at'                               => '移動開始日',
        'moving_lead_time'                              => '移動LT',
        // 在庫棚卸
        'stocktaking_month'                             => '棚卸年月',
        //JCcores原価システム
        'date_month'                                    => '対象年月'
    ],
];
