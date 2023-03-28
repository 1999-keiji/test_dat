<?php

declare(strict_types=1);

use Cake\Chronos\Chronos;

if (! function_exists('is_not_null')) {
    /**
     * NULLでないか判定する
     *
     * @param  mixed $value
     * @return bool
     */
    function is_not_null($value): bool
    {
        return ! is_null($value);
    }
}

if (! function_exists('subtract_category_from_path')) {
    /**
     * パス(URL)からカテゴリを抽出する
     *
     * @param  string $path
     * @return string
     */
    function subtract_category_from_path(string $path): string
    {
        return starts_with($path, '/') ? explode('/', $path)[2] : explode('/', $path)[1];
    }
}

if (! function_exists('route_relatively')) {
    /**
     * ルート名から相対パス(URL)を生成
     *
     * @param  string $route_name
     * @param  array  $parameters
     * @return string
     */
    function route_relatively(string $route_name, $parameters = []): string
    {
        return route($route_name, $parameters, false);
    }
}

if (! function_exists('has_error')) {
    /**
     * 入力項目にエラーがあった場合、HTMLのclass属性を付与する
     *
     * @param  string $key
     * @return string
     */
    function has_error(string $key): string
    {
        if (! session()->has('errors')) {
            return '';
        }

        return session('errors')->has($key) ? 'has-error' : '';
    }
}

if (! function_exists('is_selected')) {
    /**
     * セレクトボックスに選択済属性を付与する
     *
     * @param  mixed $value
     * @param  mixed $selected
     * @return string
     */
    function is_selected($value, $selected): string
    {
        if (is_string($selected)) {
            $value = (string)$value;
        }

        return $value === $selected ? 'selected' : '';
    }
}

if (! function_exists('is_checked')) {
    /**
     * チェックボックス、ラジオボタンに選択済属性を付与する
     *
     * @param  mixed $value
     * @param  mixed $checked
     * @return string
     */
    function is_checked($value, $checked): string
    {
        if (is_string($checked)) {
            $value = (string)$value;
        }
        if (is_bool($checked)) {
            $value = (bool)$value;
        }

        return $value === $checked ? 'checked' : '';
    }
}

if (! function_exists('replace_el')) {
    /**
     * 改行コードを、改行用のHTMLタグに置換
     *
     * @param  string $text
     * @return string
     */
    function replace_el(string $text): string
    {
        return str_replace(["\r\n", "\n"], '<br>', $text);
    }
}

if (! function_exists('generate_file_name')) {
    /**
     * ファイル名の生成
     *
     * @param  string $base_file_name
     * @param  array $params
     * @return string
     */
    function generate_file_name(string $base_file_name, ?array $params = null): string
    {
        $elements = [$base_file_name];
        if (! is_null($params)) {
            $elements[] = implode('-', $params);
        }

        $elements[] = Chronos::now()->format('Ymd_Hi');
        $file_name = implode('_', $elements);

        return str_replace(
            ["\r\n", "\r", "\n", "\\", '/', ':', ',', ';', '*', '?', '<', '>', '|'],
            ['', '', '', '￥', '／', '：', '，', '；', '＊', '？', '＜', '＞', '｜'],
            $file_name
        );
    }
}

if (! function_exists('convert_to_kilogram')) {
    /**
     * g->kgへの変換
     *
     * @param  $weight
     * @return float
     */
    function convert_to_kilogram($weight): float
    {
        return floor((int)$weight / 100) / 10;
    }
}

if (! function_exists('number_format_of_product')) {
    /**
     * 商品にまつわる数値のフォーマット
     *
     * @param  $number
     * @param  string $unit
     * @return string
     */
    function number_format_of_product($number, string $unit = 'weight'): string
    {
        return $unit === 'weight' ? number_format((float)$number, 1) : number_format((float)$number);
    }
}

if (! function_exists('decimal_floor')) {
    /**
     * 指定小数での切り捨て
     *
     * @param  float $value
     * @param  number $digit
     * @return float
     */
    function decimal_floor(float $value, $digit = 0): float
    {
        return floor($value * pow(10, $digit)) / pow(10, $digit);
    }
}

if (! function_exists('str_limit_ja')) {
    /**
     * 日本語文字列の切りつめ
     *
     * @param  string $value
     * @param  ?int $limit
     * @param  ?string $end
     */
    function str_limit_ja(string $value, $limit = 100, $end = '...'): string
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }
}

if (! function_exists('get_excel_column_str')) {
    /**
     * 列番号からExcel列のローマ字文字列を生成
     *
     * @param integer $col_number
     * @return string
     */
    function get_excel_column_str($col_number)
    {
        $column_str = [
            '',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z'
        ];

        $buf_num = $col_number;
        $buf_str = '';
        while ($buf_num > (count($column_str) - 1)) {
            $str_num = $buf_num;
            $buf_num = floor(($str_num - 1) / (count($column_str) - 1));
            $str_num = $str_num - ($buf_num * (count($column_str) - 1));
            $buf_str = $column_str[$str_num].$buf_str;
        }

        return $column_str[$buf_num].$buf_str;
    }
}

if (! function_exists('response_to_download_zip')) {
    /**
     * ZIPファイルをダウンロードさせるためのHTTPレスポンスを設定
     *
     * @param  string $path
     * @param  string $file_name
     * @return void
     */
    function response_to_download_zip(string $path, string $file_name)
    {
        header('Content-Type: application/zip; name="'.$file_name.'"');
        header('Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode($file_name));
        header('Content-Length: '.filesize($path.$file_name));
        echo file_get_contents($path.$file_name);
    }
}
