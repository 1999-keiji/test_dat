<?php

return [
    'auth' => [
        'login' => [
            'fail' => [
                'class'   => 'warning',
                'message' => 'ログインIDもしくはパスワードに誤りがあります。'
            ]
        ],
        'password' => [
            'change' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'パスワードを変更しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'パスワードの変更に失敗しました。'
                ],
                'not_matched' => [
                    'class'   => 'warning',
                    'message' => '現在のパスワードに誤りがあります。'
                ]
            ]
        ]
    ],
    'master' => [
        'products' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '商品マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '商品マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '商品マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '商品マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '商品マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '商品マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => 'BASE+から連携された商品、もしくは工場取扱商品として登録済の商品は削除できません。'
                ]
            ]
        ],
        'customers' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '得意先マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '得意先マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '得意先マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '得意先マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '得意先マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '得意先マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => 'エンドユーザと紐づけ済の得意先は削除できません。'
                ]
            ]
        ],
        'end_users' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'エンドユーザマスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'エンドユーザマスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'エンドユーザマスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'エンドユーザマスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'エンドユーザマスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'エンドユーザマスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => 'BASE+から連携されたエンドユーザは削除できません。'
                ]
            ],
            'factories' => [
                'create' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => 'エンドユーザ工場マスタを追加しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => 'エンドユーザ工場マスタの追加に失敗しました。'
                    ],
                ],
                'delete' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => 'エンドユーザ工場マスタを削除しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => 'エンドユーザ工場マスタの削除に失敗しました。'
                    ]
                ]
            ],
        ],
        'factory_species' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱品種マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱品種マスタの追加に失敗しました。'
                ],
                'regist' => [
                    'class'   => 'warning',
                    'message' => '対象の工場取扱品種マスタは既に追加されています。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱品種マスタを更新しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱品種マスタの更新に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱品種マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱品種マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '生産シミュレーションで利用されている工場取扱品種は削除できません。'
                ]
            ]
        ],
        'factory_products' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱商品マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱商品マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱商品マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱商品マスタの修正に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '引当済の工場商品の製品規格/ケース入数を変更することはできません。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場取扱商品マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場取扱商品マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '納入先と紐づけ済、もしくは注文実績のある工場取扱商品マスタは削除できません。'
                ]
            ]
        ],
        'factory_rest' => [
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場カレンダーマスタを登録しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場カレンダーマスタの登録に失敗しました。'
                ]
            ],
        ],
        'delivery_destinations' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入先マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入先マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入先マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入先マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入先マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入先マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => 'BASE+から連携された納入先、もしくは納入先倉庫、納入工場商品、工場商品特価として登録済の納入先は削除できません。'
                ]
            ]
        ],
        'delivery_warehouses' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入倉庫マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入倉庫マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入倉庫マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入倉庫マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入倉庫マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入倉庫マスタの削除に失敗しました。'
                ]
            ]
        ],
        'delivery_factory_products' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入工場商品マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入工場商品マスタの追加に失敗しました。'
                ],
                'overlapped' => [
                    'class'   => 'warning',
                    'message' => '特価の適用期間が重複しています。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '単一の納入先に対して、同一の商品を複数紐づけることはできません。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入工場商品マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入工場商品マスタの修正に失敗しました。'
                ],
                'overlapped' => [
                    'class'   => 'warning',
                    'message' => '特価の適用期間が重複しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '納入工場商品マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '納入工場商品マスタの削除に失敗しました。'
                ]
            ]
        ],
        'corporations' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '法人マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '法人マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '法人マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '法人マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '法人マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '法人マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '工場と紐づけ済の法人は削除できません。'
                ]
            ]
        ],
        'users' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'ユーザマスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'ユーザマスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'ユーザマスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'ユーザマスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'ユーザマスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'ユーザマスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '工場マスタで使用されている為、削除できません。'
                ]
            ],
            'reset' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'パスワードをリセットしました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'パスワードのリセットに失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ]
        ],
        'warehouses' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '倉庫マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '倉庫マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '倉庫マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '倉庫マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '倉庫マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '倉庫マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '工場倉庫、納入倉庫と紐づけ済の倉庫は削除できません。'
                ]
            ]
        ],
        'factories' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '工場マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '工場マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '工場取扱品種、工場取扱商品、商品価格、商品特価を登録済の工場は削除できません。'
                ]
            ],
            'beds' => [
                'update' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場レイアウトを更新しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場レイアウトの更新に失敗しました。'
                    ],
                    'interuptted' => [
                        'class'   => 'warning',
                        'message' => 'ほかの担当者が修正しています。'
                    ]
                ]
            ],
            'warehouses' => [
                'create' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場倉庫マスタを追加しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場倉庫マスタの追加に失敗しました。'
                    ],
                    'overlapped' => [
                        'class'   => 'warning',
                        'message' => '工場と紐づけ済の倉庫は追加できません。'
                    ]
                ],
                'update' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場倉庫マスタを修正しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場倉庫マスタの修正に失敗しました。'
                    ]
                ],
                'delete' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場倉庫マスタを削除しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場倉庫マスタの削除に失敗しました。'
                    ],
                    'forbidden' => [
                        'class'   => 'warning',
                        'message' => '最も優先度の高い倉庫、または未引当在庫が残っている倉庫は削除はできません。'
                    ]
                ]
            ],
            'panels' => [
                'create' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場パネルマスタを追加しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場パネルマスタの追加に失敗しました。'
                    ]
                ],
                'delete' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場パネルマスタを削除しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場パネルマスタの削除に失敗しました。'
                    ],
                    'forbidden' => [
                        'class'   => 'warning',
                        'message' => '工場生育ステージと紐づけ済のパネルは削除できません。'
                    ]
                ]
            ],
            'cycle_patterns' => [
                'update' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場サイクルパターンマスタを更新しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場サイクルパターンマスタの更新に失敗しました。'
                    ],
                    'interuptted' => [
                        'class'   => 'warning',
                        'message' => 'ほかの担当者が修正しています。'
                    ]
                ],
                'delete' => [
                    'success' => [
                        'class'   => 'info',
                        'message' => '工場サイクルパターンマスタを削除しました。'
                    ],
                    'fail' => [
                        'class'   => 'danger',
                        'message' => '工場サイクルパターンマスタの削除に失敗しました。'
                    ],
                    'forbidden' => [
                        'class'   => 'warning',
                        'message' => '工場生育ステージと紐づけ済の工場サイクルパターンは削除できません。'
                    ]
                ]
            ],
        ],
        'lead_time' => [
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'リードタイムを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'リードタイムの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ]
        ],
        'species' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '品種マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '品種マスタの追加に失敗しました。'
                ],
                'distinct' => [
                    'class'   => 'warning',
                    'message' => '変換元実績集計コードで重複している行があります。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '品種マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '品種マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'distinct' => [
                    'class'   => 'warning',
                    'message' => '変換元実績集計コードで重複している行があります。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '品種マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '品種マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '商品マスタ、工場取扱品種マスタと紐づけ済の品種は削除できません。'
                ]
            ]
        ],
        'transport_companies' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '運送会社マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '運送会社マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '運送会社マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '運送会社マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '運送会社マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '運送会社マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '集荷時間をすべて削除してください。'
                ]
            ]
        ],
        'collection_times' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '集荷時間マスタを追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '集荷時間マスタの追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '集荷時間マスタを修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '集荷時間マスタの修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '集荷時間マスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '集荷時間マスタの削除に失敗しました。'
                ],
                'forbidden' => [
                    'class'   => 'warning',
                    'message' => '納入先マスタに紐づけ済の集荷時間は削除できません。'
                ]
            ]
        ],
        'calendars' => [
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'カレンダーマスタを登録しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'カレンダーマスタの登録に失敗しました。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'カレンダーマスタを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'カレンダーマスタの削除に失敗しました。'
                ]
            ]
        ]
    ],
    'order' => [
        'order_forecasts' => [
            'export' => [
                'not_found' => [
                    'class'   => 'warning',
                    'message' => '該当する納入工場商品が見つかりませんでした。'
                ]
            ],
            'import' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'フォーキャストを取り込みました。'
                ],
                'not_matched_file' => [
                    'class'   => 'danger',
                    'message' => 'アップロードされたファイルが正しくありません。'
                ],
                'import_data_not_exsit' => [
                    'class'   => 'info',
                    'message' => '取込対象データがありませんでした。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'フォーキャストの取込に失敗しました。'
                ]
            ]
        ],
        'order_input' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報を追加しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報の追加に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報を修正しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報の修正に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'forbidden' => [
                    'class'   => 'danger',
                    'message' => '修正できない注文情報です。'
                ],
                'allocated' => [
                    'class'   => 'danger',
                    'message' => '引当済の注文情報です。'
                ],
                'shipped' => [
                    'class'   => 'danger',
                    'message' => '出荷済の注文情報です。'
                ]
            ],
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報を削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報の削除に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'forbidden' => [
                    'class'   => 'danger',
                    'message' => '削除できない注文情報です。'
                ],
                'allocated' => [
                    'class'   => 'danger',
                    'message' => '引当済の注文情報です。'
                ],
                'shipped' => [
                    'class'   => 'danger',
                    'message' => '出荷済の注文情報です。'
                ]
            ]
        ],
        'order_list' => [
            'search' => [
                'excel_fail' => [
                    'class'   => 'danger',
                    'message' => '注文一覧のExcel出力に失敗しました。'
                ],
                'matching_fail' => [
                    'class'   => 'danger',
                    'message' => 'マッチング処理に失敗しました。'
                ],
                'matching_success' => [
                    'class'   => 'info',
                    'message' => 'マッチング処理を実施しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報を変更しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報の変更に失敗しました。'
                ],
                'exceeding_allocation_quantity' => [
                    'class'   => 'danger',
                    'message' => '注文数に対して引当数が過剰になっています。在庫引当をし直してから注文数を変更してください。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'has_allocated' => [
                    'class'   => 'warning',
                    'message' => 'すでに在庫が引当されています。商品を変更する場合、引当を解除してください。'
                ]
            ],
            'cancel' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報をキャンセルしました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'allocated' => [
                    'class'   => 'danger',
                    'message' => '製品が引き当てられています。引当を解除してからキャンセルしてください。'
                ],
                'canceled' => [
                    'class'   => 'danger',
                    'message' => 'キャンセル済みの注文情報です。'
                ],
                'shipped' => [
                    'class'   => 'danger',
                    'message' => '出荷済みの注文情報です。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報のキャンセルに失敗しました。'
                ],
            ],
            'link' => [
                'over_allocation' => [
                    'message' => "確定注文の注文数合計に対して、仮注文の引当数量が過剰になっています。\n".
                        '確定注文の注文数を修正するか、仮注文の在庫引当をし直してください。'
                ],
                'not_matched_shipping_date' => [
                    'message' => "仮注文の出荷日と、確定注文の出荷日に差異があります。\n".
                        '確定注文の出荷日を修正してから紐づけしてください。'
                ],
                'shipped_already' => [
                    'message' => "仮注文はすでに出荷確定されています。確定注文の注文数の合計が\n".
                        '仮注文の注文数と一致するように修正してください。'
                ],
            ]
        ],
        'returned_products' => [
            'create' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '返品情報を登録しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '返品情報の登録に失敗しました。'
                ]
            ],
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '返品情報を更新しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '返品情報の更新に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ]
        ],
        'purchase_order_excel_import' => [
            'import' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文書を取り込みました。(取込件数: %d件)'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文書の取込に失敗しました。'
                ],
                'not_matched_file' => [
                    'class'   => 'danger',
                    'message' => 'アップロードされたファイルが正しくありません。'
                ],
                'invalid' => [
                    'class'   => 'warning',
                    'message' => '入力内容に誤りがあります。エラーメッセージを確認してください。'
                ]
            ]
        ]
    ],
    'plan' => [
        'growth_simulation' => [
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '生産シミュレーションを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '生産シミュレーションの削除に失敗しました。'
                ]
            ]
        ],
        'planned_cultivation_status_work' => [
            'index' => [
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ表示が可能です。'
                ]
            ],
            'sum' => [
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ表示が可能です。'
                ]
            ],
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '各階栽培株数を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '各階栽培株数の保存に失敗しました。'
                ],
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ表示が可能です。'
                ]
            ]
        ],
        'planned_arrangement_status_work' => [
            'index' => [
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ表示が可能です。'
                ]
            ],
            'detail' => [
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ表示が可能です。'
                ]
            ],
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '栽培パネル配置図を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '栽培パネル配置図の保存に失敗しました。'
                ],
                'out_of_range' => [
                    'class'   => 'warning',
                    'message' => '%s から %s の間のみ保存が可能です。'
                ]
            ]
        ],
        'bed_states' => [
            'delete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => 'ベッド状況データを削除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'ベッド状況データの削除に失敗しました。'
                ]
            ],
            'arrangement_states' => [
                'index' => [
                    'out_of_range' => [
                        'class'   => 'warning',
                        'message' => '%s から %s の間のみ表示が可能です。'
                    ]
                ],
                'detail' => [
                    'out_of_range' => [
                        'class'   => 'warning',
                        'message' => '%s から %s の間のみ表示が可能です。'
                    ]
                ]
            ]
        ],
        'growth_sale_management' => [
            'export' => [
                'not_exist' => [
                    'class'   => 'warning',
                    'message' => '工場取扱商品が未設定です。'
                ]
            ],
            'import' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '生産・販売管理表を取り込みました。'
                ],
                'not_matched_file' => [
                    'class'   => 'danger',
                    'message' => 'アップロードされたファイルが正しくありません。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '生産・販売管理表の取込に失敗しました。'
                ],
                'import_data_not_exsit' => [
                    'class'   => 'info',
                    'message' => '更新対象データがありませんでした。'
                ]
            ]
        ],
        'facility_status_list' => [
            'export' => [
                'not_found_factory_species' => [
                    'class'   => 'warning',
                    'message' => '工場品種が未登録の工場です。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '施設利用状況一覧の出力に失敗しました。'
                ]
            ]
        ]
    ],
    'shipment' => [
        'productized_results' => [
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '製品化実績を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '製品化実績の保存に失敗しました。'
                ],
                'reserved' => [
                    'class'   => 'warning',
                    'message' => '引当済の製品があります。数量を変更する場合、もういちど引当をしなおしてください。'
                ],
                'moved' => [
                    'class'   => 'warning',
                    'message' => '在庫移動済の製品があります。数量を変更する場合、もとの保管在庫に移動し直してください。'
                ],
                'multiple' => [
                    'class'   => 'warning',
                    'message' => '複数倉庫で保管されています。数量を変更する場合、単一の倉庫にまとめてください。'
                ],
                'disposed' => [
                    'class'   => 'warning',
                    'message' => '廃棄登録されています。数量を変更する場合、廃棄実績を解除してください。'
                ]
            ]
        ],
        'product_allocations' => [
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫引当を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫引当の保存に失敗しました。'
                ],
                'over_allocated' => [
                    'class'   => 'warning',
                    'message' => '在庫数が不足している製品があります。もういちど引当をしなおしてください。'
                ]
            ]
        ],
        'collection_request' => [
            'export' => [
                'fail' => [
                    'class'   => 'danger',
                    'message' => '集荷依頼書の出力に失敗しました。'
                ],
                'not_found' => [
                    'class'   => 'warning',
                    'message' => 'テンプレートが作成されていません。システム部までお問い合わせください。'
                ]
            ],
            'save' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '注文情報を更新しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '注文情報の更新に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ]
        ],
        'shipment_data_export' => [
            'export' => [
                'fail' => [
                    'class'   => 'danger',
                    'message' => '出荷データの出力に失敗しました。'
                ]
            ]
        ],
        'shipment_fix' => [
            'fix' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '選択された注文を出荷確定しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '選択された注文の出荷確定に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ]
        ],
        'invoices' => [
            'fix' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '請求書の締め処理を確定しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '請求書の締め処理に失敗しました。'
                ]
            ],
            'cancel' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '請求書の確定を解除しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '請求書の確定の解除に失敗しました。'
                ]
            ],
            'export' => [
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'PDFのダウンロードに失敗しました。'
                ],
                'order_not_exist' => [
                    'class'   => 'warning',
                    'message' => '該当する注文が見つかりませんでした。'
                ],
                'tax_error' => [
                    'class'   => 'warning',
                    'message' => '適用可能な税率がマスタに登録されていません。税マスタを見直してください。'
                ],
                'not_found' => [
                    'class'   => 'warning',
                    'message' => '選択された工場用の帳票のテンプレートが作成されていません。システム部までお問い合わせください。'
                ]
            ]
        ],
        'form_output' => [
            'download' => [
                'fail' => [
                    'class'   => 'danger',
                    'message' => 'PDFのダウンロードに失敗しました。'
                ],
                'tax_error' => [
                    'class'   => 'warning',
                    'message' => '適用可能な税率がマスタに登録されていません。税マスタを見直してください。'
                ],
                'not_found' => [
                    'class'   => 'warning',
                    'message' => '選択された工場用の帳票のテンプレートが作成されていません。システム部までお問い合わせください。'
                ]
            ]
        ]
    ],
    'factory_production_work' => [
        'activity_results' => [
            'update' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '活動実績を更新しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '活動実績の更新に失敗しました。'
                ]
            ]
        ]
    ],
    'stock' => [
        'stocks' => [
            'move' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫移動情報を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫移動情報の保存に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'adjust' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫調整情報を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫調整情報の保存に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ]
            ],
            'dispose' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '廃棄情報を保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '廃棄情報の保存に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'ほかの担当者が修正しています。'
                ],
                'export' => [
                    'not_found' => [
                        'class'   => 'warning',
                        'message' => '廃棄された在庫が存在しないため、出力ができません。'
                    ]
                ]
            ],
        ],
        'stocktaking' => [
            'search' => [
                'moving' => [
                    'class'   => 'warning',
                    'message' => '倉庫間移動中の在庫があります。移動完了後に棚卸を実行してください。'
                ]
            ],
            'start' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫棚卸を開始しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫棚卸の開始に失敗しました。'
                ],
                'interuptted' => [
                    'class'   => 'warning',
                    'message' => 'すでに在庫棚卸が開始されています。'
                ]
            ],
            'refresh' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫棚卸のやり直しに成功しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫棚卸のやり直しに失敗しました。'
                ]
            ],
            'keep' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫棚卸データを一時保存しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫棚卸のデータの一時保存に失敗しました。'
                ]
            ],
            'complete' => [
                'success' => [
                    'class'   => 'info',
                    'message' => '在庫棚卸が完了しました。'
                ],
                'fail' => [
                    'class'   => 'danger',
                    'message' => '在庫棚卸の完了に失敗しました。'
                ]
            ],
            'save' => [
                'block' => [
                    'class'   => 'warning',
                    'message' => '在庫棚卸中は画面を操作できません。'
                ]
            ],
            'export' => [
                'transition' => [
                    'fail' => [
                        'class'   => 'warning',
                        'message' => '月末日に棚卸されたので、遷移はありません。'
                    ]
                ]
            ]
        ]
    ]
];
