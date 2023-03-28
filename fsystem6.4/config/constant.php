<?php

return [
    'master' => [
        'factory' => [
            'default_warehouse_name' => '出荷倉庫'
        ],
        'factory_products' => [
            'input_group' => [
                'A' => 'A',
                'B' => 'コストコ',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
                'H' => '廃棄',
                'I' => 'I',
                'J' => 'バルク',
                'K' => '個包装',
                'L' => 'L',
                'M' => 'M',
                'N' => 'N',
                'O' => 'O',
                'P' => 'P',
                'Q' => 'Q',
                'R' => 'R',
                'S' => 'S',
                'T' => 'T',
                'U' => 'U',
                'V' => 'V',
                'W' => 'W',
                'X' => 'X',
                'Y' => 'Y',
                'Z' => 'Z'
            ],
            'unit' => [
                'ﾊﾟｯｸ',
                'ｹｰｽ'
            ]
        ]
    ],
    'plan' => [
        'facility_status_list' => [
            'excel_file_name'       => '施設利用状況一覧',
            'excel_sheet_name'      => '施設利用状況一覧表',
            'list_days'             => 27,
            'excel_header_bed'      => ['', '作業日 / ベッド数', ''],
            'excel_header_factory'  => ['', '品種', '工場取扱品種'],
            'excel_header_date'     => ['', 'ステージ名', 'ベッド数', 'パネル数', '使用パネル', '株数'],
            'excel_ratio'           => ['', '利用率', ''],
            'excel_header_color'    => '#C5D9F1',
            'species_row_num'       => 5,
            'data_begin_row_num'    => 6,
            'data_begin_column_num' => 3,
        ]
    ],
    'order' => [
        'purchase_order_excel_import' => [
            'sheet_name' => 'アップロード',
            'import_key_list' => [
                0 => 'base_plus_order_number',
                1 => 'base_plus_order_chapter_number',
                2 => 'requestor_organization_code',
                3 => 'end_user_code',
                4 => 'supplier_flag',
                5 => '5',
                6 => 'base_plus_end_user_code',
                7 => '7',
                8 => 'delivery_destination_code',
                9 => 'delivery_destination_code_2',
                10 => 'maker_code',
                11 => 'product_code',
                12 => 'product_name',
                13 => '13',
                14 => '14',
                15 => '15',
                16 => 'special_spec_code',
                17 => 'order_quantity',
                18 => '18',
                19 => '19',
                20 => '20',
                21 => '21',
                22 => 'purchase_staff_code',
                23 => 'place_order_work_staff_code',
                24 => 'supplier_product_name',
                25 => 'order_unit',
                26 => 'currency_code',
                27 => 'delivery_date',
                28 => 'tax_class',
                29 => '29',
                30 => 'supplier_instructions',
                31 => 'buyer_remark',
                32 => 'order_message',
                33 => '33',
                34 => '34',
                35 => '35',
                36 => '36',
                37 => '37',
                38 => '38',
                39 => '39',
                40 => 'base_plus_recived_order_number',
                41 => 'base_plus_recived_order_chapter_number',
                42 => '42',
                43 => 'seller_code',
                44 => 'customer_product_name',
                45 => 'recived_order_unit',
                46 => 'currency_code',
                47 => 'delivery_date_2',
                48 => '48',
                49 => 'end_user_order_number',
                50 => '50',
                51 => '51',
                52 => '52',
                53 => 'statement_delivery_price_display_class',
                54 => '54',
                55 => '55',
                56 => 'tax_class_2',
                57 => 'basis_for_recording_sales_class',
                58 => '58',
                59 => 'customer_staff_name',
                60 => '60',
                61 => '61',
                62 => '62',
                63 => '63',
                64 => '64',
                65 => '65',
                66 => '66',
                67 => '67',
                68 => '68'
            ],
            'not_linked_delivery_factory_product' => '指定された納入先と商品が紐づけられていません。',
            'disabled_to_apply_unit_price' => '適用可能な商品単価が存在しません。'
        ],
        'order_list' => [
            'excel_file_name'  => '注文一覧',
            'order_excel_list' => '注文Excel一覧',
            'output_condition' => '出力条件'
        ],
        'whiteboard_reference' => [
            'excel_file_name' => 'ホワイトボード情報'
        ],
        'vvf_backbone_import' => [
            'message' => [
                'stop_message'     => 'マッチングした注文がありませんでした。',
                'comparison_error' => '注文番号%sは注文数量合計が引当数量合計よりも少ないためマッチングされませんでした。',
                'matching_success' => '%d件マッチングされました。'
            ]
        ]
    ],
    'factory_production_work' => [
        'work_instruction' => [
            'file_name'        => '作業指示書',
            'sheet_title_base' => [
                'work'           => '作業指示書',
                'productization' => '製品化指示書'
            ],
            'work' => [
                'font_family' => 'Meiryo UI',
                'font_size'   => 11,
                'width'       => 7,
                'height'      => 22,
                'iterative_data_start_column'      => 15,
                'oneset_iterative_data_culumn_num' => 4,
                'width_list' => [
                    'A' => 2,
                    'B' => 7,
                    'C' => 19,
                    'D' => 6,
                    'E' => 6,
                    'F' => 7,
                    'G' => 6,
                    'H' => 7,
                    'I' => 9,
                    'J' => 14,
                    'K' => 12,
                    'L' => 11,
                    'M' => 7,
                    'N' => 7,
                    'O' => 11
                ],
                'fixed_merge_cells' => [
                    'B1:O3',
                    'B9:C10',
                    'D9:F10',
                    'G9:K10',
                    'L9:O10',
                    'B11:C13',
                    'D11:F14',
                    'G11:K12',
                    'L11:O14'
                ],
                'fixed_outline_borders' => [
                    'B9',
                    'D9',
                    'G9',
                    'L9',
                    'B11:C14',
                    'D11',
                    'G11:K14',
                    'L11'
                ],
                'fixed_formats' => [
                    'B4' => 'yyyy"/"m"/"d (aaa)'
                ],
                'iterative_data_merge_set_list' => [
                    [
                        'target_first_row' => 'D',
                        'target_second_row' => 'F',
                        'target_first_culumn_plus_num' => 0,
                        'target_second_culumn_plus_num' => 3
                    ],
                    [
                        'target_first_row' => 'G',
                        'target_second_row' => 'H',
                        'target_first_culumn_plus_num' => 0,
                        'target_second_culumn_plus_num' => 1
                    ],
                    [
                        'target_first_row' => 'I',
                        'target_second_row' => 'I',
                        'target_first_culumn_plus_num' => 0,
                        'target_second_culumn_plus_num' => 1
                    ],
                    [
                        'target_first_row' => 'J',
                        'target_second_row' => 'K',
                        'target_first_culumn_plus_num' => 0,
                        'target_second_culumn_plus_num' => 1
                    ],
                    [
                        'target_first_row' => 'L',
                        'target_second_row' => 'O',
                        'target_first_culumn_plus_num' => 0,
                        'target_second_culumn_plus_num' => 3
                    ]
                ]
            ],
            'productization' => [
                'font_family'                      => 'Meiryo UI',
                'font_size'                        => 11,
                'width'                            => 8,
                'height'                           => 15,
                'iterative_data_start_column'      => 19,
                'oneset_iterative_data_culumn_num' => 3,
                'fixed_merge_cells' => [
                    'B1:P4',
                    'B5:P5',
                    'B7:B9',
                    'C8:E9',
                    'H8:J9',
                    'M8:O9',
                    'B13:B14',
                    'C13:C14',
                    'D13:D14',
                    'N13:P13',
                    'B17:C18',
                    'D17:D18',
                    'E17:F18',
                    'G17:G18',
                ],
                'fixed_borders' => [
                    'B7:O9',
                    'F10:P11',
                    'B13:P14',
                    'B17:P18'
                ],
                'fixed_outline_thick_borders' => [
                    'B17:P18'
                ],
                'fixed_new_lines' => [
                    'C7',
                    'F7',
                    'H7',
                    'K7',
                    'M7',
                    'F10',
                    'H10',
                    'K10',
                    'M10',
                    'P10',
                    'B13'
                ],
                'iterative_data_merge_list' => [
                    ['first' => 'D', 'second' => ':D'],
                    ['first' => 'E', 'second' => ':F'],
                    ['first' => 'G', 'second' => ':G'],
                    ['first' => 'H', 'second' => ':I'],
                    ['first' => 'J', 'second' => ':J'],
                    ['first' => 'K', 'second' => ':K'],
                    ['first' => 'L', 'second' => ':M'],
                    ['first' => 'N', 'second' => ':N'],
                    ['first' => 'O', 'second' => ':P'],
                ],
                'data_totals_merge_list' => [
                    ['first' => 'B', 'second' => ':G'],
                    ['first' => 'H', 'second' => ':I'],
                    ['first' => 'J', 'second' => ':J'],
                    ['first' => 'K', 'second' => ':K'],
                    ['first' => 'L', 'second' => ':M'],
                    ['first' => 'N', 'second' => ':N'],
                    ['first' => 'O', 'second' => ':P']
                ],
                'discard_related_merge_list' => [
                    ['first' => 'B', 'second' => ':F'],
                    ['first' => 'G', 'second' => ':H'],
                    ['first' => 'I', 'second' => ':K'],
                    ['first' => 'L', 'second' => ':M'],
                    ['first' => 'N', 'second' => ':P']
                ]
            ]
        ]
    ],
    'shipment' => [
        'invoice' => [
            'file_name'     => '請求書_',
            'font_family'   => 'kozgopromedium',
            'weight_format' => "%skg",
            'cover_item'    => [
                'max'                  => 25,
                'format'               => "invoice_cover.%s.pdf",
                'border'               => 0,
                'text_large'           => 12,
                'text_middle'          => 10,
                'text_small'           => 8,
                'customer_name'        => [
                    'x'      => 25.7,
                    'y'      => 45,
                    'width'  => 100,
                    'suffix' => '御中'
                ],
                'invoice_date'         => [
                    'x'      => 170,
                    'y'      => 24.6,
                    'width'  => 33,
                    'format' => 'Y年m月d日'
                ],
                'invoice_number'       => [
                    'x'     => 170,
                    'y'     => 29.5,
                    'width' => 33
                ],
                'delivery_date'        => [
                    'x'      => 29,
                    'y'      => 66,
                    'width'  => 70,
                    'format' => "%s ～ %s"
                ],
                'invoice_amount'       => [
                    'x'     => 29,
                    'y'     => 74,
                    'width' => 70
                ],
                'factory_postal_code'  => [
                    'x'      => 137.5,
                    'y'      => 54.5,
                    'width'  => 70,
                    'format' => '〒%s'
                ],
                'factory_address'      => [
                    'x'     => 137.5,
                    'y'     => 59.5,
                    'width' => 100
                ],
                'factory_phone_number' => [
                    'x'      => 137.5,
                    'y'      => 64.5,
                    'width'  => 70,
                    'format' => 'TEL: %s'
                ],
                'factory_fax_number'   => [
                    'x'      => 137.5,
                    'y'      => 69.5,
                    'width'  => 70,
                    'format' => 'FAX: %s'
                ],
/* GGN 出力
                'global_gap_number'    => [
                    'x'      => 137.5,
                    'y'      => 74.5,
                    'width'  => 70,
                    'format' => 'GGN: %s'
                ],
*/
                'currency'             => [
                    'x'     => 178.5,
                    'y'     => 79.2,
                    'width' => 15
                ],
                'bank_name'            => [
                    'x'     => 25.7,
                    'y'     => 252.2,
                    'width' => 39
                ],
                'bank_branch_name'     => [
                    'x'     => 68.5,
                    'y'     => 252.2,
                    'width' => 78
                ],
                'bank_account_number'  => [
                    'x'     => 68.5,
                    'y'     => 256.4,
                    'width' => 78
                ],
                'bank_account_holder'  => [
                    'x'     => 68.5,
                    'y'     => 260.65,
                    'width' => 78
                ],
                'payment_date'         => [
                    'x'      => 25.7,
                    'y'      => 278.65,
                    'width'  => 100,
                    'format' => 'Y年n月j日迄にお支払ください'
                ],
                'order_details' => [
                    'base'   => ['x' => 26.0, 'y' => 88],
                    'height' => ['inner' => 5.96, 'outer' => 6.41],
                    'width'  => [
                        'end_user_name'             => 39.2,
                        'delivery_destination_name' => 39.3,
                        'sub_total'                 => 21.7,
                        'tax_total'                 => 21.7,
                        'total'                     => 22.7,
                        'separate_line'             => 1.14,
                        'weight_total'              => 21.5,
                        'border'                    => 0.3
                    ]
                ],
                'sub_total'    => ['x' => 172.1, 'y' => 248.5, 'width' => 23],
                'tax_total'    => ['x' => 172.1, 'y' => 252.8, 'width' => 23],
                'total'        => ['x' => 172.1, 'y' => 257.1, 'width' => 23],
                'weight_total' => ['x' => 172.1, 'y' => 262.8, 'width' => 23],
                'page'         => ['x' => 0,     'y' => 290,   'format' => "%s / %s"]
            ],
            'detail_item' => [
                'max'                  => 39,
                'format'               => "invoice_details.%s.pdf",
                'border'               => 0,
                'text_large'           => 10,
                'text_middle'          => 8,
                'text_small'           => 6,
                'customer_name'        => [
                    'x'      => 23.75,
                    'y'      => 48,
                    'width'  => 95,
                    'suffix' => '御中'
                ],
                'invoice_date'         => [
                    'x' => 165,
                    'y' => 24,
                    'width' => 25,
                    'format' => 'Y年m月d日'
                ],
                'delivery_month'       => [
                    'x'      => 23.75,
                    'y'      => 63.45,
                    'width'  => 95,
                    'format' => 'Y年n月'
                ],
                'end_user'             => [
                    'x'      => 23.75,
                    'y'      => 68,
                    'width'  => 95,
                    'suffix' => '様 納品分'
                ],
                'factory_postal_code'  => [
                    'x'      => 139.5,
                    'y'      => 50.5,
                    'width'  => 70,
                    'format' => '〒%s'
                ],
                'factory_address'      => [
                    'x'     => 139.5,
                    'y'     => 54,
                    'width' => 100
                ],
                'factory_phone_number' => [
                    'x'      => 139.5,
                    'y'      => 57.5,
                    'width'  => 70,
                    'format' => 'TEL: %s'
                ],
                'factory_fax_number'   => [
                    'x'      => 139.5,
                    'y'      => 61,
                    'width'  => 70,
                    'format' => 'FAX: %s'
                ],
/* GGN出力停止
                'global_gap_number'    => [
                    'x'      => 139.5,
                    'y'      => 64.5,
                    'width'  => 70,
                    'format' => 'GGN: %s'
                ],
*/
                'currency'             => [
                    'x'     => 166.3,
                    'y'     => 69.5,
                    'width' => 15
                ],
                'order_details'  => [
                    'base'   => ['x' => 24.6, 'y' => 77.5],
                    'height' => ['inner' => 4, 'outer' => 4.53],
                    'width'  => [
                        'delivery_date'          => 11.7,
                        'base_plus_order_number' => 18,
                        'product_name'           => 42.35,
                        'order_quantity'         => 11.85,
                        'place_order_unit_code'  => 11.85,
                        'order_unit'             => 17.95,
                        'order_amount'           => 17.95,
                        'separate_line'          => 0.4,
                        'order_message'          => 24,
                        'border'                 => 0.3
                    ]
                ],
                'order_amount' => ['x' => 159.05, 'y' => 254.55, 'width' => 24],
                'tax'          => ['x' => 159.05, 'y' => 259.05, 'width' => 24],
                'total'        => ['x' => 159.05, 'y' => 263.55, 'width' => 24],
                'weight'       => ['x' => 159.05, 'y' => 268.85, 'width' => 24],
                'page'         => ['x' => 0,      'y' => 273,    'format' => "%s / %s"]
            ]
        ],
        'form_output' => [
            'shipment_pdf' => [
                'file_name'     => '出荷案内書',
                'template_name' => "%s_shipment.pdf",
                'font_family'   => 'kozgopromedium',
                'coordinates'   => [
                    'downloaded_mark' => [
                        'text' => '*', 'font_size' => 10, 'x' => 5, 'y' => 5
                    ],
                    'delivery_destination' => [
                        'text' => '%s 御中', 'font_size' => 14, 'x' => 20, 'y' => 52
                    ],
                    'end_user' => [
                        'font_size' => 12, 'x' => 30, 'y' => 58
                    ],
                    'shipping_date' => [
                        'font_size' => 14, 'x' => 224, 'y' => 21
                    ],
                    'customer_postal_code' => [
                        'text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 38
                    ],
                    'customer_address' => [
                        'font_size' => 10, 'x' => 227, 'y' => 43
                    ],
                    'customer_name' => [
                        'font_size' => 10, 'x' => 227, 'y' => 48
                    ],
                    'seller_name' => [
                        'default' => ['text' => '担当者: %s', 'font_size' => 10, 'x' => 227, 'y' => 53],
                        'other'   => ['text' => '担当者: %s', 'font_size' => 10, 'x' => 227, 'y' => 62]
                    ],
                    'customer_phone_number' => [
                        'text' => 'TEL: %s', 'font_size' => 10, 'x' => 227, 'y' => 58
                    ],
                    'factory_postal_code' => [
                        'default' => ['text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 76],
                        'other'   => ['text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 42]
                    ],
                    'factory_address' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 81],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 47]
                    ],
                    'corporation_name' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 86],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 52]
                    ],
                    'factory_name' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 91],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 57]
                    ],
/* GGN出力停止
                    'global_gap_number' => [
                        'default' => ['text' => 'GGN %s', 'font_size' => 10, 'x' => 255, 'y' => 91],
                        'other'   => ['text' => 'GGN %s', 'font_size' => 10, 'x' => 255, 'y' => 57]
                    ],
*/
                    'factory_phone_number' => [
                        'other'   => ['text' => 'TEL: %s', 'font_size' => 10, 'x' => 227, 'y' => 67]
                    ],
                    'table' => [
                        'base_y'                  => 109,
                        'font_size'               => 9,
                        'font_size_small'         => 8,
                        'font_size_more_small'    => 7,
                        'product_name_x'          => 27,
                        'order_quantity_x'        => 88.5,
                        'order_quantity_width'    => 14,
                        'unit_x'                  => 102,
                        'delivery_date_x'         => 116.5,
                        'end_user_order_number_x' => 129.5,
                        'order_number_x'          => 167,
                        'remark_x'                => 194,
                        'add_y'                   => 8.6
                    ]
                ],
            ],
            'delivery_pdf' => [
                'file_name'     => '%d.納品書',
                'template_name' => "%s_delivery.pdf",
                'font_family'   => 'kozgopromedium',
                'coordinates'   => [
                    'downloaded_mark' => [
                        'text' => '*', 'font_size' => 10, 'x' => 5, 'y' => 5
                    ],
                    'delivery_destination' => [
                        'text' => '%s 御中', 'font_size' => 14, 'x' => 20, 'y' => 52
                    ],
                    'end_user' => [
                        'font_size' => 12, 'x' => 30, 'y' => 58
                    ],
                    'shipping_date' => [
                        'font_size' => 14, 'x' => 224, 'y' => 21
                    ],
                    'customer_postal_code' => [
                        'text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 38
                    ],
                    'customer_address' => [
                        'font_size' => 10, 'x' => 227, 'y' => 43
                    ],
                    'customer_name' => [
                        'font_size' => 10, 'x' => 227, 'y' => 48
                    ],
                    'seller_name' => [
                        'default' => ['text' => '担当者: %s', 'font_size' => 10, 'x' => 227, 'y' => 53],
                        'other'   => ['text' => '担当者: %s', 'font_size' => 10, 'x' => 227, 'y' => 62]
                    ],
                    'customer_phone_number' => [
                        'text' => 'TEL: %s', 'font_size' => 10, 'x' => 227, 'y' => 58
                    ],
                    'factory_postal_code' => [
                        'default' => ['text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 76],
                        'other'   => ['text' => '〒 %s', 'font_size' => 10, 'x' => 227, 'y' => 42]
                    ],
                    'factory_address' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 81],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 47]
                    ],
                    'corporation_name' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 86],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 52]
                    ],
                    'factory_name' => [
                        'default' => ['font_size' => 10, 'x' => 227, 'y' => 91],
                        'other'   => ['font_size' => 10, 'x' => 227, 'y' => 57]
                    ],
/* GGN出力停止
                    'global_gap_number' => [
                        'default' => ['text' => 'GGN %s', 'font_size' => 10, 'x' => 255, 'y' => 91],
                        'other'   => ['text' => 'GGN %s', 'font_size' => 10, 'x' => 255, 'y' => 57]
                    ],
*/
                    'factory_phone_number' => [
                        'other'   => ['text' => 'TEL: %s', 'font_size' => 10, 'x' => 227, 'y' => 67]
                    ],
                    'table' => [
                        'base_y'                  => 108,
                        'font_size'               => 9,
                        'font_size_small'         => 7,
                        'cell_width'              => 18,
                        'product_name_x'          => 22,
                        'order_quantity_x'        => 77.5,
                        'order_quantity_width'    => 14,
                        'delivery_quantity_x'     => 94,
                        'delivery_quantity_width' => 14,
                        'unit_x'                  => 107.5,
                        'delivery_date_x'         => 122,
                        'order_unit_x'            => 133,
                        'order_amount_x'          => 149,
                        'end_user_order_number_x' => 167,
                        'order_number_x'          => 200,
                        'remark_x'                => 232,
                        'add_y'                   => 8.6
                    ],
                    'tax_amount'   => [
                        'font_size' => 11, 'x' => 234.5, 'y' => 193.5, 'width' => 56
                    ],
                    'tax_included' => [
                        'font_size' => 11, 'x' => 234.5, 'y' => 202.5, 'width' => 56
                    ]
                ]
            ],
            'receipt_pdf' => [
                'file_name'     => '%d.受領書',
                'template_name' => "receipt%s.pdf",
                'zip_name'      => '納品受領書',
                'font_family'   => 'kozgopromedium',
                'coordinates'   => [
                    'downloaded_mark' => [
                        'text' => '*', 'font_size' => 10, 'x' => 5, 'y' => 5
                    ],
                    'customer' => [
                        'text' => '%s 御中', 'font_size' => 13, 'x' => 28, 'y' => 43
                    ],
                    'factory' => [
                        'font_size' => 11, 'x' => 34, 'y' => 55
                    ],
                    'shipping_date' => [
                        'font_size' => 14, 'x' => 224, 'y' => 17
                    ],
                    'delivery_destination_postal_code' => [
                        'text' => '〒 %s', 'font_size' => 11, 'x' => 205, 'y' => 26
                    ],
                    'delivery_destination_address' => [
                        'font_size' => 11, 'font_size_small' => 8.5, 'x' => 205, 'y' => 31
                    ],
                    'delivery_destination_name' => [
                        'font_size' => 11, 'font_size_small' => 9, 'x' => 205, 'y' => 36
                    ],
                    'delivery_destination_phone_number' => [
                        'text' => 'TEL: %s', 'font_size' => 11, 'x' => 205, 'y' => 41
                    ],
                    'table' => [
                        'base_y'                  => 104,
                        'font_size'               => 9,
                        'font_size_small'         => 7,
                        'cell_width'              => 18,
                        'product_name_x'          => 22,
                        'order_quantity_x'        => 77.5,
                        'order_quantity_width'    => 14,
                        'delivery_quantity_x'     => 94,
                        'delivery_quantity_width' => 14,
                        'unit_x'                  => 107.5,
                        'delivery_date_x'         => 122,
                        'order_unit_x'            => 133,
                        'order_amount_x'          => 149,
                        'end_user_order_number_x' => 167,
                        'order_number_x'          => 200,
                        'remark_x'                => 232,
                        'add_y'                   => 8.6
                    ],
                    'tax_amount'   => [
                        'font_size' => 11, 'x' => 234.5, 'y' => 163.5, 'width' => 56
                    ],
                    'tax_included' => [
                        'font_size' => 11, 'x' => 234.5, 'y' => 172.5, 'width' => 56
                    ]
                ]
            ]
        ]
    ],
    'data_link' => [
        'global' => [
            'results' => [
                'success' => '正常',
                'fail'    => '異常(内容はlaravel.logを参照)'
            ],
            'error_list' => [
                'end_file_not_found'        => ' エンドファイル確認失敗',
                'tsv_file_not_found'        => ' TSVファイル確認失敗',
                'failed_to_import_tsv_file' => ' TSVファイル取り込み失敗',
                'validation_error'          => ' バリデーションエラー',
                'failed_to_save_data'       => ' データ登録失敗'
            ],
            'error_message_system'     => 'システムエラーが発生しました。ログを確認してください。',
            'error_message_validation' => '入力エラーがあります。',
            'error_message_not_enough' => " 情報が不足している行があります。(row:%d, columns:%d, need:%d)"
        ],
        'master' => [
            'end_user' => [
                'error_message_not_exist' => " 得意先マスタにデフォルト得意先が存在しません。"
            ],
            'product' => [
                'error_message_not_exist' =>
                    " 商品カテゴリに紐づく品種変換が存在しません(product_large_category:%s, product_middle_category:%s)"
            ],
            'product_prices' => [
                'error_message_not_exist' => " 仕入先に紐付く工場が存在しません(supplier_code:%s)"
            ]
        ],
        'shipment' => [
            'error_list' => [
                'not_confirm_data' => ' 出荷確定済注文取得失敗',
                'failed_to_save_data' => ' データ登録失敗'
            ]
        ]
    ],
    'get_template' => [
        'global' => [
            'save_path' => storage_path('exports/')
        ],
        'shipment' => [
            'excel_form' => [
                'collection_request_path' =>
                    storage_path('app/export/shipment/excel_form/collection_request/collection_request_template.xlsx')
            ],
            'pdf_form'   => [
                'shipment_path' => storage_path('app/export/shipment/pdf_form/shipment/'),
                'receipt_path'  => storage_path('app/export/shipment/pdf_form/receipt/'),
                'delivery_path' => storage_path('app/export/shipment/pdf_form/delivery/'),
                'invoice_path'  => storage_path('app/export/shipment/pdf_form/invoice/')
            ]
        ]
    ],
    'stock' => [
        'screens' => [
            'productized_results_input' => '製品化実績入力',
            'product_allocations'       => '製品引当',
            'shipment_fix'              => '出荷確定',
            'return_products_input'     => '返品入力',
            'stock_move'                => '在庫移動',
            'stock_adjustment'          => '在庫調整',
            'stock_destruction'         => '在庫破棄'
        ]
    ]
];
