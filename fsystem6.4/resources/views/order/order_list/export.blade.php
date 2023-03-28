<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  @php
    $order_status = [
      'all' => 'すべて',
      'temporary' => '仮注文',
      'fixed' => '確定済',
      'cancel' => 'キャンセル',
      'slip' => '赤伝黒伝'
    ];
  @endphp
</head>
<body>
  <table>
    <tr>
      <th>{{ $factory->factory_abbreviation }}－{{ $customer->customer_abbreviation }}－{{ $order_status[$params['order_status']] }}</th>
    </tr>
    <tr>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">処理区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">注文番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">出荷確定日時</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">引当</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">会社コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票種別区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票種別コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">仕入先コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">課税区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">購買担当者コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">購買担当者名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">通貨コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注業務担当者コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注業務担当名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注項番</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注年月日</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">製品区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">仕入先品名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先品名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">品番</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">品名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">特殊仕様</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">メーカーコード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">希望納期</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">要求元組織コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">組織名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">最終顧客コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">仕入先発注数</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注数</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注単位</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">仕入先発注単価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注合価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注単価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注コメント</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">仕入先指示</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">発注者備考</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納入先コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納入先略称</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">受注番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先注文番号</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票種別区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票種別コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">売上計上基準区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">納品書価格表示区分</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">販売担当コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">販売担当者名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先担当者名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">注文単価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">得意先注文合価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">商品重量</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">出荷日</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">登録種別</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票種別</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">伝票状態種別</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">紐付状態種別</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">工場キャンセルフラグ</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">返品日</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">返品商品名</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">返品商品単価</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">返品数</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">返品備考</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">運送会社コード</th>
      <th width="20" style="background-color: #c5d9f1; border: 1px solid #000000;">集荷時間</th>
    </tr>
    @foreach ($orders as $o)
    <tr>
      <td style="border: 1px solid #000000">{{ $o->process_class->value() }}:{{ $o->process_class->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->order_number }}</td>
      <td style="border: 1px solid #000000">
        @if ($o->hadBeenShipped())
        {{ $o->fixed_shipping_on }}
        @endif
      </td>
      <td style="border: 1px solid #000000">{{ $o->allocation_status->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->own_company_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->base_plus_order_number }}</td>
      <td style="border: 1px solid #000000">{{ $o->small_peace_of_peper_type_class->value() }}:{{ $o->small_peace_of_peper_type_class->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->small_peace_of_peper_type_code->value() }}:{{ $o->small_peace_of_peper_type_code->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->supplier_flag }}</td>
      <td style="border: 1px solid #000000">{{ $o->tax_class }}</td>
      <td style="border: 1px solid #000000">{{ $o->purchase_staff_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->purchase_staff_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->currency_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->place_order_work_staff_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->place_order_work_staff_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->base_plus_order_chapter_number }}</td>
      <td style="border: 1px solid #000000">{{ $o->received_date }}</td>
      <td style="border: 1px solid #000000">{{ $o->prodcut_class->value() }}:{{ $o->prodcut_class->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->supplier_product_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->customer_product_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->product_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->product_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->special_spec_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->maker_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->delivery_date ?: '' }}</td>
      <td style="border: 1px solid #000000">{{ $o->requestor_organization_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->organization_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->end_user_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->base_plus_end_user_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->order_quantity }}</td>
      <td style="border: 1px solid #000000">{{ $o->place_order_quantity }}</td>
      <td style="border: 1px solid #000000">{{ $o->place_order_unit_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->supplier_place_order_unit }}</td>
      <td style="border: 1px solid #000000">{{ $o->order_amount }}</td>
      <td style="border: 1px solid #000000">{{ $o->order_unit }}</td>
      <td style="border: 1px solid #000000">{{ $o->order_message }}</td>
      <td style="border: 1px solid #000000">{{ $o->supplier_instructions }}</td>
      <td style="border: 1px solid #000000">{{ $o->buyer_remark }}</td>
      <td style="border: 1px solid #000000">{{ $o->delivery_destination_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->delivery_destination_abbreviation }}</td>
      <td style="border: 1px solid #000000">{{ $o->base_plus_order_number }}-{{ $o->base_plus_order_chapter_number }}</td>
      <td style="border: 1px solid #000000">{{ $o->end_user_order_number }}</td>
      <td style="border: 1px solid #000000">
        @if ($o->pickup_type_class)
        {{ $o->pickup_type_class->value() }}:{{ $o->pickup_type_class->label() }}
        @endif
      </td>
      <td style="border: 1px solid #000000">
        @if ($o->pickup_type_code)
        {{ $o->pickup_type_code->value() }}:{{ $o->pickup_type_code->label() }}
        @endif
      </td>
      <td style="border: 1px solid #000000">
        @if ($o->basis_for_recording_sales_class)
        {{ $o->basis_for_recording_sales_class->value() }}:{{ $o->basis_for_recording_sales_class->label() }}
        @endif
      </td>
      <td style="border: 1px solid #000000">
        @if ($o->statement_delivery_price_display_class)
        {{ $o->statement_delivery_price_display_class->value() }}:{{ $o->statement_delivery_price_display_class->label() }}
        @endif
      </td>
      <td style="border: 1px solid #000000">{{ $o->seller_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->seller_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->customer_staff_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->recived_order_unit }}</td>
      <td style="border: 1px solid #000000">{{ $o->customer_recived_order_unit }}</td>
      <td style="border: 1px solid #000000">{{ $o->product_weight }}</td>
      <td style="border: 1px solid #000000">{{ $o->shipping_date }}</td>
      <td style="border: 1px solid #000000">{{ $o->creating_type->value() }}:{{ $o->creating_type->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->slip_type->value() }}:{{ $o->slip_type->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->slip_status_type->value() }}:{{ $o->slip_status_type->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->related_order_status_type->value() }}:{{ $o->related_order_status_type->label() }}</td>
      <td style="border: 1px solid #000000">{{ $o->factory_cancel_flag }}</td>
      <td style="border: 1px solid #000000">{{ $o->returned_on ?: '' }}</td>
      <td style="border: 1px solid #000000">{{ $o->returned_product_name }}</td>
      <td style="border: 1px solid #000000">{{ $o->returned_unit_price }}</td>
      <td style="border: 1px solid #000000">{{ $o->returned_quantity }}</td>
      <td style="border: 1px solid #000000">{{ $o->returned_remark }}</td>
      <td style="border: 1px solid #000000">{{ $o->transport_company_code }}</td>
      <td style="border: 1px solid #000000">{{ $o->collection_time }}</td>
    </tr>
    @endforeach
  </table>
</body>
</html>
