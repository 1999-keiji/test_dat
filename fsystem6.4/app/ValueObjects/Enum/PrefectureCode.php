<?php

declare(strict_types=1);

namespace App\ValueObjects\Enum;

final class PrefectureCode extends Enum
{
    public const HOKKAIDO = '01';
    public const AOMORI = '02';
    public const IWATE = '03';
    public const MIYAGI = '04';
    public const AKITA = '05';
    public const YAMAGATA = '06';
    public const FUKUSHIMA = '07';
    public const IBARAKI = '08';
    public const TOCHIGI = '09';
    public const GUNMA = '10';
    public const SAITAMA = '11';
    public const CHIBA = '12';
    public const TOKYO = '13';
    public const KANAGAWA = '14';
    public const NIIGATA = '15';
    public const TOYAMA = '16';
    public const ISHIKAWA = '17';
    public const FUKUI = '18';
    public const YAMANASHI = '19';
    public const NAGANO = '20';
    public const GIFU = '21';
    public const SHIZUOKA = '22';
    public const AICHI = '23';
    public const MIE = '24';
    public const SHIGA = '25';
    public const KYOTO = '26';
    public const OSAKA = '27';
    public const HYOGO = '28';
    public const NARA = '29';
    public const WAKAYAMA = '30';
    public const TOTTORI = '31';
    public const SHIMANE = '32';
    public const OKAYAMA = '33';
    public const HIROSHIMA = '34';
    public const YAMAGUCHI = '35';
    public const TOKUSHIMA = '36';
    public const KAGAWA = '37';
    public const EHIME = '38';
    public const KOCHI = '39';
    public const FUKUOKA = '40';
    public const SAGA = '41';
    public const NAGASAKI = '42';
    public const KUMAMOTO = '43';
    public const OITA = '44';
    public const MIYAZAKI = '45';
    public const KAGOSHIMA = '46';
    public const OKINAWA = '47';

    protected const ENUM = [
        '北海道' => self::HOKKAIDO,
        '青森県' => self::AOMORI,
        '岩手県' => self::IWATE,
        '宮城県' => self::MIYAGI,
        '秋田県' => self::AKITA,
        '山形県' => self::YAMAGATA,
        '福島県' => self::FUKUSHIMA,
        '茨城県' => self::IBARAKI,
        '栃木県' => self::TOCHIGI,
        '群馬県' => self::GUNMA,
        '埼玉県' => self::SAITAMA,
        '千葉県' => self::CHIBA,
        '東京都' => self::TOKYO,
        '神奈川県' => self::KANAGAWA,
        '新潟県' => self::NIIGATA,
        '富山県' => self::TOYAMA,
        '石川県' => self::ISHIKAWA,
        '福井県' => self::FUKUI,
        '山梨県' => self::YAMANASHI,
        '長野県' => self::NAGANO,
        '岐阜県' => self::GIFU,
        '静岡県' => self::SHIZUOKA,
        '愛知県' => self::AICHI,
        '三重県' => self::MIE,
        '滋賀県' => self::SHIGA,
        '京都府' => self::KYOTO,
        '大阪府' => self::OSAKA,
        '兵庫県' => self::HYOGO,
        '奈良県' => self::NARA,
        '和歌山県' => self::WAKAYAMA,
        '鳥取県' => self::TOTTORI,
        '島根県' => self::SHIMANE,
        '岡山県' => self::OKAYAMA,
        '広島県' => self::HIROSHIMA,
        '山口県' => self::YAMAGUCHI,
        '徳島県' => self::TOKUSHIMA,
        '香川県' => self::KAGAWA,
        '愛媛県' => self::EHIME,
        '高知県' => self::KOCHI,
        '福岡県' => self::FUKUOKA,
        '佐賀県' => self::SAGA,
        '長崎県' => self::NAGASAKI,
        '熊本県' => self::KUMAMOTO,
        '大分県' => self::OITA,
        '宮崎県' => self::MIYAZAKI,
        '鹿児島県' => self::KAGOSHIMA,
        '沖縄県' => self::OKINAWA
    ];

    /**
     * @var array
     */
    private const REQUIRE_PREFECTURE_CODE_LIST = ['JP'];

    /**
     * 都道府県コードがセットで必要になる国コードのリストを返却
     *
     * @return array
     */
    public function getRequirePrefectureCodeList(): array
    {
        return self::REQUIRE_PREFECTURE_CODE_LIST;
    }

    /**
     * 都道府県コードがセットで必要になる国コードのリストをカンマ区切りで返却
     *
     * @return string
     */
    public function getJoinedRequirePrefectureCodeList(): string
    {
        return implode(',', $this->getRequirePrefectureCodeList());
    }

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return sprintf("国コードが%sの場合、\n都道府県は必須選択です。", $this->getJoinedRequirePrefectureCodeList());
    }
}
