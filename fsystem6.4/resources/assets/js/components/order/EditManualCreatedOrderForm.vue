<style scoped>
  input.has-suffix {
    width: 70%;
    display: inline-block;
  }
  input[name="base_plus_order_number"] {
    width: 65%;
  }
  input[name="base_plus_order_chapter_number"] {
    width: 30%;
  }
  textarea.remark-text {
    min-height: 100px;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-sm btn-info" @click="showModal = true">修正</button>
    <modal title="注文データ修正" effect="fade" large v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">工場</label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">
                  {{ factory.factory_abbreviation }}
                  <input name="factory_code" type="hidden" :value="searchParams.factory_code">
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">注文日</label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">
                  {{ searchParams.received_date }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">得意先</label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">
                  {{ customer.customer_abbreviation }}
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">エンドユーザ</label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">
                  {{ searchParams.end_user_name }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="end_user_code" class="col-md-5 col-sm-5 col-xs-5 control-label">納入先</label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">
                  {{ searchParams.delivery_destination_name }}
                  <input name="delivery_destination_code" type="hidden" :value="searchParams.delivery_destination_code">
                </span>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="delivery_date" class="col-md-5 col-sm-5 col-xs-5 control-label required">
                納入日
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <datepicker-ja
                  attr-name="delivery_date"
                  :date="deliveryDate"
                  :allow-empty="true"
                  @update-date="updateDeliveryDate">
                </datepicker-ja>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="factory_product_sequence_number" class="col-md-5 col-sm-5 col-xs-5 control-label required">
                商品
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <select class="form-control" v-model="factoryProduct" required @change="getFactoryProductPrice">
                  <option v-for="fp in factoryProducts" :key="fp.factory_product_sequence_number" :value="fp">{{ fp.product_name }}</option>
                </select>
                <input name="factory_product_sequence_number" type="hidden" :value="factoryProduct.factory_product_sequence_number || ''">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="currency_code" class="col-md-5 col-sm-5 col-xs-5 control-label required">
                通貨
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-5 col-sm-5 col-xs-6">
                <select class="form-control" v-model="currency" required @change="getFactoryProductPrice">
                  <option v-for="c in currencies" :key="c.currency_code" :value="c">{{ c.currency_code }}</option>
                </select>
                <input name="currency_code" type="hidden" :value="currency.currency_code">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="order_quantity" class="col-md-5 col-sm-5 col-xs-5 control-label required">
                数量
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <input class="form-control text-right ime-inactive has-suffix" type="text" name="order_quantity" v-model="orderQuantity" required>&nbsp;{{ this.factoryProduct.unit }}
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="order_unit" class="col-md-5 col-sm-5 col-xs-5 control-label required">
                単価
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <input-number-with-formatter
                  attrName="order_unit"
                  :decimals="currency.order_unit_decimals"
                  :value="orderUnit"
                  v-on:update-value="updateOrderUnit"/>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">
                合価
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">{{ orderAmount }}</span>
                <input name="order_amount" type="hidden" :value="orderAmount">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="recived_order_unit" class="col-md-5 col-sm-5 col-xs-5 control-label">
                受注単価
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <input-number-with-formatter
                  attrName="received_order_unit"
                  :decimals="currency.order_unit_decimals"
                  :value="receivedOrderUnit"
                  v-on:update-value="updateReceivedOrderUnit"/>
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 col-xs-5 control-label">
                得意先受注合価
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7">
                <span class="shown_label">{{ customerReceivedOrderUnit }}</span>
                <input name="customer_received_order_unit" type="hidden" :value="customerReceivedOrderUnit">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="recived_order_unit" class="col-md-5 col-sm-5 col-xs-5 control-label">
                BASE+注文番号
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7 form-inline">
                <input class="form-control text-left ime-inactive" type="text" maxlength="10" name="base_plus_order_number" :value="basePlusOrderNumber">
                <input class="form-control text-left ime-inactive" type="text" maxlength="3" name="base_plus_order_chapter_number" :value="basePlusOrderChapterNumber">
              </div>
            </div>
          </div>
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="end_user_order_number" class="col-md-5 col-sm-5 col-xs-5 control-label" style="padding: 0 5px 0 15px">
                エンドユーザ注文番号
              </label>
              <div class="col-md-5 col-sm-5 col-xs-6">
                <input class="form-control text-left ime-inactive" type="text" maxlength="17" name="end_user_order_number" :value="endUserOrderNumber">
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="order_message" class="col-md-5 col-sm-5 col-xs-5 control-label">
                備考
              </label>
              <div class="col-md-7 col-sm-7 col-xs-7" style="padding-right: 0">
                <textarea class="form-control text-left ime-active remark-text" cols="25" rows="4" maxlength="50" name="order_message" v-model="orderMessage"></textarea>
              </div>
            </div>
          </div>
        </div>

        <input name="order_number" type="hidden" :value="order.order_number">
        <input name="updated_at" type="hidden" :value="order.updated_at">
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="PATCH">
      </form>
      <div slot="modal-footer" class="modal-footer">
        <button v-if="canSaveOrder" class="btn btn-default btn-lg" type="button" :disabled="disabledToSubmit" @click="submitForm">
          <i class="fa fa-save"></i> 保存
        </button>
        <button class="btn btn-default btn-lg" type="button" @click="showModal = false">
          キャンセル
        </button>
        <input v-if="! canSaveOrder" id="can-save-data" type="hidden" value="0">
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

const number_format = require('locutus/php/strings/number_format')

export default {
  components: {Modal: VueStrap.modal},
  props: {
    routeAction: {
      type: String,
      required: true
    },
    order: {
      type: Object,
      required: true
    },
    factories: {
      type: Array,
      required: true
    },
    customers: {
      type: Array,
      required: true
    },
    currencies: {
      type: Array,
      required: true
    },
    searchParams: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    canSaveOrder: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      factory: null,
      customer: null,
      deliveryDate: this.oldParams.delivery_date || this.order.delivery_date,
      factoryProducts: [],
      factoryProduct: {},
      currency: null,
      orderQuantity: this.oldParams.order_quantity || this.order.order_quantity,
      orderUnit: this.oldParams.order_unit || this.order.order_unit,
      receivedOrderUnit: this.oldParams.received_order_unit || this.order.recived_order_unit || '',
      basePlusOrderNumber: this.oldParams.base_plus_order_number || this.order.base_plus_order_number,
      basePlusOrderChapterNumber: this.oldParams.base_plus_order_chapter_number || this.order.base_plus_order_chapter_number,
      endUserOrderNumber: this.oldParams.end_user_order_number || this.order.end_user_order_number,
      orderMessage: this.oldParams.order_message || this.order.order_message,
      disabledToSubmit: false,
      showModal: false
    }
  },
  created: function () {
    this.factory = this.factories.filter(f => f.factory_code === this.searchParams.factory_code)[0]
    this.customer = this.customers.filter(c => c.customer_code === this.searchParams.customer_code)[0]
    this.currency = this.currencies
      .filter(c => c.currency_code === this.oldParams.currency_code || this.order.currency_code)[0]
    this.getFactoryProducts()
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
    getFactoryProducts: function () {
      this.factoryProducts = []
      this.disabledToSubmit = true

      axios.get('/api/get-delivery-factory-products', {
        params: {
          factory_code: this.order.factory_code,
          delivery_destination_code: this.order.delivery_destination_code
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToSelectProduct) {
            alert('指定された納入先に紐づけされた商品が存在していません。')
          }

          const factoryProductSequenceNumber = this.oldParams.factory_product_sequence_number ||
            this.order.factory_product_sequence_number
          if (factoryProductSequenceNumber) {
            for (const fp of this.factoryProducts) {
              if (factoryProductSequenceNumber == fp.factory_product_sequence_number) {
                this.factoryProduct = fp
              }
            }
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
        .finally(() => {
          this.disabledToSubmit = false
        })
    },
    getFactoryProductPrice: function () {
      if (! this.factoryProduct || ! this.deliveryDate) {
        return
      }
      if (this.orderUnit) {
        return
      }

      this.disabledToSubmit = true
      const params = {
        delivery_destination_code: this.order.delivery_destination_code,
        factory_code: this.order.factory_code,
        factory_product_sequence_number: this.factoryProduct.factory_product_sequence_number,
        currency_code: this.currency.currency_code,
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
                  this.orderUnit = number_format(factoryProductPrice.unit_price, this.currencies.order_unit_decimals)
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
        .finally(() => {
          this.disabledToSubmit = false
        })
    },
    updateDeliveryDate: function (deliveryDate) {
      this.deliveryDate = deliveryDate
    },
    updateOrderUnit: function (orderUnit) {
      this.orderUnit = orderUnit
    },
    updateReceivedOrderUnit: function (receivedOrderUnit) {
      this.receivedOrderUnit = receivedOrderUnit
    },
    submitForm: function () {
      if (confirm('データを登録しますか?')) {
        this.disabledToSubmit = true

        $('.alert').remove()
        this.$refs.form.submit()
      }
    }
  }
}
</script>
