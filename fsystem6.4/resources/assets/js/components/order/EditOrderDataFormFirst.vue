<style scoped>
  .not-value-multi-shown-label-first {
    margin-right: 3px;
  }
</style>

<template>
  <div>
    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>基本情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="customer_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            Fsystem注文番号
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.order_number }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            処理区分
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.process_class.label }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="base_plus_order_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            BASE+発注番号
          </label>
          <div class="col-md-7 col-sm-7">
            <span
              v-if="order.base_plus_order_number == null || order.base_plus_order_chapter_number == null"
              class="shown_label not-value-multi-shown-label-first">
              {{ order.base_plus_order_number }}
            </span>
            <span v-else class="shown_label">{{ order.base_plus_order_number }}</span>
            <span class="shown_label">{{ order.base_plus_order_chapter_number }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="base_plus_recived_order_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            BASE+受注番号
          </label>
          <div class="col-md-7 col-sm-7">
            <span
              v-if="order.base_plus_recived_order_number == null || order.base_plus_recived_order_chapter_number == null"
              class="shown_label not-value-multi-shown-label-first">
              {{ order.base_plus_recived_order_number }}
            </span>
            <span v-else class="shown_label">{{ order.base_plus_recived_order_number }}</span>
            <span class="shown_label">{{ order.base_plus_recived_order_chapter_number }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="end_user_order_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            エンドユーザ注文番号
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <input
              class="form-control ime-inactive"
              :class="{ 'has-error': 'end_user_order_number' in errors }"
              maxlength="50"
              name="end_user_order_number"
              type="text"
              :value="oldParams.end_user_order_number || order.end_user_order_number">
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ order.end_user_order_number }}</span>
            <input type="hidden" name="end_user_order_number" :value="order.end_user_order_number">
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="received_date" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            注文日
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <datepicker-ja attr-name="received_date" :date="receivedDate"></datepicker-ja>
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ receivedDate }}</span>
            <input type="hidden" name="received_date" :value="order.received_date | formatDate">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="delivery_date" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            納入日
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <datepicker-ja
              attr-name="delivery_date"
              :date="deliveryDate"
              @update-date="updateDeliveryDate">
            </datepicker-ja>
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ deliveryDate }}</span>
            <input type="hidden" name="delivery_date" :value="order.delivery_date | formatDate">
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="shipping_date" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            出荷日
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateShippingDate">
            <datepicker-ja attr-name="shipping_date" :date="shippingDate"></datepicker-ja>
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ shippingDate }}</span>
            <input type="hidden" name="shipping_date" :value="order.shipping_date | formatDate">
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>納入先情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="factory_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">工場</label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ factory.factory_abbreviation }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="customer_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">得意先</label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ customer.customer_abbreviation }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="end_user_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            エンドユーザ
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <search-master
              target="end_user"
              :code="endUserCode"
              :name="endUserName"
              :factory-code="factory.factory_code"
              :customer-code="customer.customer_code" />
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="delivery_destination_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            納入先
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <search-master
              target="delivery_destination"
              :code="deliveryDestinationCode"
              :name="deliveryDestinationName"
              :factory-code="factory.factory_code"
              :customer-code="customer.customer_code"
              @get-selected-delivery-destination="getSelectedDeliveryDestination" />
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>商品情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="product_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            商品
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <select class="form-control" v-model="factoryProduct" required @change="getFactoryProductPrice">
              <option value=""></option>
              <option v-for="fp in factoryProducts" :key="fp.factory_product_sequence_number" :value="fp">{{ fp.product_name }}</option>
            </select>
            <input name="product_code" type="hidden" :value="factoryProduct.product_code || ''">
            <input name="factory_product_sequence_number" type="hidden" :value="factoryProduct.factory_product_sequence_number || ''">
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ order.product_code }}</span>
            <input name="product_code" type="hidden" :value="factoryProduct.product_code">
            <input name="factory_product_sequence_number" type="hidden" :value="factoryProduct.factory_product_sequence_number">
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="product_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            品名
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <input class="form-control ime-active" :class="{ 'has-error': 'product_name' in errors }" type="text" name="product_name" v-model="productName">
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ order.product_name }}</span>
            <input type="hidden" name="product_name" :value="order.product_name">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="supplier_product_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            仕入先品名
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <input
              class="form-control ime-active"
              :class="{ 'has-error': 'supplier_product_name' in errors }"
              type="text"
              name="supplier_product_name"
              :value="oldParams.supplier_product_name || order.supplier_product_name">
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ order.supplier_product_name }}</span>
            <input type="hidden" name="supplier_product_name" :value="order.supplier_product_name">
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="customer_product_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            得意先品名
          </label>
          <div class="col-md-7 col-sm-7" v-if="canUpdateBase">
            <input
              class="form-control ime-active"
              :class="{ 'has-error': 'customer_product_name' in errors }"
              type="text"
              name="customer_product_name"
              :value="oldParams.customer_product_name || order.customer_product_name">
          </div>
          <div class="col-md-7 col-sm-7" v-else>
            <span class="shown_label">{{ order.customer_product_name }}</span>
            <input type="hidden" name="customer_product_name" :value="order.customer_product_name">
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>商品明細情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="order_quantity" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            注文数
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-3 col-sm-3">
            <input class="form-control ime-inactive text-right" :class="{ 'has-error': 'order_quantity' in errors }" type="text" name="order_quantity" v-model="orderQuantity" required>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="place_order_unit_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            単位
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-3 col-sm-3">
            <input
              class="form-control ime-inactive"
              :class="{ 'has-error': 'place_order_unit_code' in errors }"
              type="text"
              name="place_order_unit_code"
              :value="placeOrderUnitCode">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="order_unit" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            単価
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-4 col-sm-4">
            <input-number-with-formatter
              attrName="order_unit"
              :decimals="currency.order_unit_decimals"
              :value="orderUnit"
              v-on:update-value="updateOrderUnit"/>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="order_amount" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            合価
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ orderAmount }}</span>
            <input type="hidden" name="order_amount" :value="orderAmount">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="currency_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            通貨
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-3 col-sm-3">
            <select class="form-control" :class="{ 'has-error': 'currency_code' in errors }" v-model="currency" required @change="getFactoryProductPrice">
              <option v-for="c in currencies" :key="c.currency_code" :value="c">{{ c.currency_code }}</option>
            </select>
            <input name="currency_code" type="hidden" :value="currency.currency_code">
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>帳票情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="statement_delivery_price_display_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            納品書価格表示区分
            <span class="required-mark">*</span>
          </label>
          <div class="col-md-6 col-sm-6">
            <select class="form-control" :class="{ 'has-error': 'statement_delivery_price_display_class' in errors }" name="statement_delivery_price_display_class" v-model="statementDeliveryPriceDisplayClass" required>
              <option value=""></option>
              <option v-for="(value, key) in statementDeliveryPriceDisplayClassList" :key="value" :value="value">{{ key }}</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="basis_for_recording_sales_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            売上計上基準区分
          </label>
          <div class="col-md-4 col-sm-4">
            <select class="form-control" :class="{ 'has-error': 'basis_for_recording_sales_class' in errors }" name="basis_for_recording_sales_class" v-model="basisForRecordingSalesClass" required>
              <option value=""></option>
              <option v-for="(value, key) in basisForRecordingSalesClassList" :key="value" :value="value">{{ key }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="recived_order_unit" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            受注単価
          </label>
          <div class="col-md-4 col-sm-4">
            <input-number-with-formatter
              attrName="received_order_unit"
              :decimals="currency.order_unit_decimals"
              :value="receivedOrderUnit"
              v-on:update-value="updateReceivedOrderUnit"/>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="customer_received_order_unit" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            得意先受注合価
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ customerReceivedOrderUnit }}</span>
            <input type="hidden" name="customer_received_order_unit" :value="customerReceivedOrderUnit">
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="small_peace_of_peper_type_class" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            発注伝票種別区分
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.small_peace_of_peper_type_class.label }}</span>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="small_peace_of_peper_type_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            発注伝票種別コード
          </label>
          <div class="col-md-4 col-sm-4">
            <select class="form-control" :class="{ 'has-error': 'small_peace_of_peper_type_code' in errors }" name="small_peace_of_peper_type_code" v-model="smallPeaceOfPeperTypeCode" required>
              <option v-for="(value, key) in smallPeaceOfPeperTypeCodeList" :key="value" :value="value">{{ key }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const number_format = require('locutus/php/strings/number_format'),
  moment = require('moment')

export default {
  props: {
    order: {
      type: Object,
      required: true
    },
    endUser: {
      type: Object,
      required: true
    },
    deliveryDestination: {
      type: Object,
      required: true
    },
    canUpdateBase: {
      type: Boolean,
      required: true
    },
    canUpdateShippingDate: {
      type: Boolean,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    },
    factory: {
      type: Object,
      required: true
    },
    customer: {
      type: Object,
      required: true
    },
    currencies: {
      type: Array,
      required: true
    },
    statementDeliveryPriceDisplayClassList: {
      type: Object,
      required: true
    },
    basisForRecordingSalesClassList: {
      type: Object,
      required: true
    },
    smallPeaceOfPeperTypeCodeList: {
      type: Object,
      required: true
    }
  },
  data: function () {
    let receivedDate = this.oldParams.received_date || this.order.received_date
    if (! ('received_date' in this.errors) && moment(receivedDate, 'YYYY-MM-DD', true).isValid()) {
      receivedDate = moment(receivedDate).format('YYYY/MM/DD')
    }
    let deliveryDate = this.oldParams.delivery_date || this.order.delivery_date
    if (! ('delivery_date' in this.errors) && moment(deliveryDate, 'YYYY-MM-DD', true).isValid()) {
      deliveryDate = moment(deliveryDate).format('YYYY/MM/DD')
    }
    let shippingDate = this.oldParams.shipping_date || this.order.shipping_date
    if (! ('shipping_date' in this.errors) && moment(shippingDate, 'YYYY-MM-DD', true).isValid()) {
      shippingDate = moment(shippingDate).format('YYYY/MM/DD')
    }

    const currency = this.currencies
      .filter(c => c.currency_code === this.oldParams.currency_code || this.order.currency_code)[0]

    let basisForRecordingSalesClass = this.oldParams.basis_for_recording_sales_class
    if (! basisForRecordingSalesClass) {
      basisForRecordingSalesClass = this.order.basis_for_recording_sales_class ?
        this.order.basis_for_recording_sales_class.value :
        ''
    }

    let statementDeliveryPriceDisplayClass = this.oldParams.statement_delivery_price_display_class
    if (! statementDeliveryPriceDisplayClass) {
      statementDeliveryPriceDisplayClass = this.order.statement_delivery_price_display_class ?
        this.order.statement_delivery_price_display_class.value :
        ''
    }

    return {
      receivedDate,
      deliveryDate,
      shippingDate,
      endUserCode: this.oldParams.end_user_code || this.order.end_user_code,
      endUserName: this.oldParams.end_user_name || this.endUser.end_user_abbreviation,
      deliveryDestinationCode: this.oldParams.delivery_destination_code || this.order.delivery_destination_code,
      deliveryDestinationName: this.oldParams.delivery_destination_name || this.deliveryDestination.delivery_destination_abbreviation,
      factoryProducts: [],
      factoryProduct: {},
      productName: this.oldParams.product_name || this.order.product_name,
      orderQuantity: this.oldParams.order_quantity || this.order.order_quantity,
      orderUnit: this.oldParams.order_unit || this.order.order_unit,
      placeOrderUnitCode: this.oldParams.place_order_unit_code || this.order.place_order_unit_code,
      currency,
      receivedOrderUnit: this.oldParams.received_order_unit || this.order.recived_order_unit,
      statementDeliveryPriceDisplayClass: statementDeliveryPriceDisplayClass,
      basisForRecordingSalesClass: basisForRecordingSalesClass,
      smallPeaceOfPeperTypeCode: this.oldParams.small_peace_of_peper_type_code || this.order.small_peace_of_peper_type_code.value
    }
  },
  created: function () {
    this.getFactoryProducts()
  },
  filters: {
    formatDate: function (date) {
      return moment(date).format('YYYY/MM/DD')
    }
  },
  computed: {
    disabledToSelectProduct: function () {
      return this.factoryProducts.length === 0
    },
    orderAmount: function () {
      if (! (this.orderQuantity && this.orderUnit)) {
        return ''
      }

      return number_format(
        this.orderQuantity * this.orderUnit.replace(/,/g, ''),
        this.currency.order_amount_decimals
      )
    },
    customerReceivedOrderUnit: function () {
      if (! (this.orderQuantity && this.receivedOrderUnit)) {
        return ''
      }

      return number_format(
        this.orderQuantity * this.receivedOrderUnit.replace(/,/g, ''),
        this.currency.order_amount_decimals
      )
    }
  },
  methods: {
    updateDeliveryDate: function(date) {
      this.deliveryDate = date
      this.getShippingDate()
    },
    getSelectedDeliveryDestination: function (deliveryDestination) {
      this.deliveryDestinationCode = deliveryDestination.code
      this.getFactoryProducts()
      this.getShippingDate()
    },
    getFactoryProducts: function () {
      this.factoryProducts = []
      if (! this.deliveryDestinationCode) {
        return
      }

      axios.get('/api/get-delivery-factory-products', {
        params: {
          factory_code: this.factory.factory_code,
          delivery_destination_code: this.deliveryDestinationCode
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToSelectProduct) {
            alert('指定された納入先に紐づけされた商品が存在していません。')
          }

          const productCode = this.oldParams.product_code || this.order.product_code
          if (productCode) {
            for (const fp of this.factoryProducts) {
              if (productCode == fp.product_code) {
                this.factoryProduct = fp
              }
            }
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    getFactoryProductPrice: function () {
      this.productName = this.factoryProduct.product_name
      this.placeOrderUnitCode = this.factoryProduct.unit

      if (! this.factoryProduct || ! this.deliveryDate) {
        return
      }
      if (this.orderUnit) {
        return
      }

      const params = {
        delivery_destination_code: this.deliveryDestinationCode,
        factory_code: this.factory.factory_code,
        factory_product_sequence_number: this.factoryProduct.factory_product_sequence_number,
        currency_code: this.currencyCode,
        date: this.deliveryDate
      }

      axios.get('/api/get-applied-factory-product-special-price', {params: params})
        .then(response => {
          const factorySpecialProductPrice = response.data
          if (factorySpecialProductPrice) {
            this.orderUnit = number_format(factorySpecialProductPrice.unit_price, this.currency.order_unit_decimals)
          }

          if (! factorySpecialProductPrice) {
            axios.get('/api/get-applied-factory-product-price', {params: params})
              .then(response => {
                const factoryProductPrice = response.data
                if (! factoryProductPrice) {
                  alert('適用可能な商品価格が設定されていません。')
                }

                if (factoryProductPrice) {
                  this.orderUnit = number_format(factoryProductPrice.unit_price, this.currency.order_unit_decimals)
                }
              })
              .catch(() => {
                alert('通信エラーが発生しました。しばらくお待ちください。')
              })
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    getShippingDate: function () {
      if (! this.deliveryDestinationCode || ! this.deliveryDate) {
        return
      }

      axios.get('/api/get-shipping-date', {
        params: {
          delivery_destination_code: this.deliveryDestinationCode,
          factory_code: this.factory.factory_code,
          delivery_date: this.deliveryDate
        }
      })
        .then(response => {
          this.shippingDate = response.data
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    updateOrderUnit: function (orderUnit) {
      this.orderUnit = orderUnit
    },
    updateReceivedOrderUnit: function (receivedOrderUnit) {
      this.receivedOrderUnit = receivedOrderUnit
    },
  }
}
</script>
