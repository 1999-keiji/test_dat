<style scoped>
  span.shown_label {
    float: left;
  }
  textarea.remark-text {
    min-height: 100px;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-warning" @click="initModal">返品</button>
    <modal title="返品情報入力" effect="fade" large v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                注文番号
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.order_number }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                注文日
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.received_date }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                納入日
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.delivery_date }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                エンドユーザ
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.end_user_abbreviation }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                納入先
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.delivery_destination_abbreviation }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                商品
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.product_name }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                単価
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.order_unit }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                通貨
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.currency_code }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                注文数
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.order_quantity }}</span>{{ '' }}
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                返品日
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <datepicker-ja attr-name="returned_on" :date="returnedOn"></datepicker-ja>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                返品商品
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-3 col-sm-2 col-xs-4">
                <select
                  id="factory_product_sequence_number"
                  class="form-control"
                  name="factory_product_sequence_number"
                  v-model="factoryProductSequenceNumber"
                  :disabled="disabledToFactoryProduct">
                  <option value=""></option>
                  <option v-for="fp in factoryProducts" :key="fp.sequence_number" :value="fp.sequence_number">
                    {{ fp.factory_product_name }}
                  </option>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                返品単価
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-2">
                <input-number-with-formatter
                  attrName="unit_price"
                  :decimals="order.currency.order_unit_decimals"
                  :value="unitPrice"
                  v-on:update-value="updateUnitPrice"/>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                返品数
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-2">
                <input
                  class="form-control ime-inactive text-right"
                  name="quantity"
                  maxlength="9"
                  type="text"
                  v-model="quantity"
                  required />
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                返品金額
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ returnedAmount }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                金額
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ amountExceptReturned }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                BASE+注文番号
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.base_plus_order_number || '&nbsp;' }}</span>
              </div>
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                エンドユーザ注文番号
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <span class="shown_label">{{ order.end_user_order_number || '&nbsp;' }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                備考
              </label>
              <div class="col-md-3 col-sm-3 col-xs-6">
                <textarea
                  class="form-control text-left ime-active remark-text"
                  cols="25"
                  rows="4"
                  maxlength="50"
                  name="remark"
                  :value="oldParams.remark || order.returned_remark"/>
              </div>
            </div>
          </div>
        </div>
        <input name="order_number" type="hidden" :value="order.order_number">
        <input name="updated_at" type="hidden" :value="order.returned_updated_at">
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" :value="method">
      </form>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default btn-lg" type="button" @click="submitForm">
          <i class="fa fa-save"></i> 保存
        </button>
        <button class="btn btn-default btn-lg" type="button" @click="showModal = false">
          キャンセル
        </button>
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
    oldParams: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      method: ! this.order.returned_order_number ? 'POST' : 'PATCH',
      showModal: false,
      returnedOn: this.oldParams.returned_on || this.order.returned_on,
      factoryProductSequenceNumber: this.oldParams.factory_product_sequence_number ||
        this.order.returned_factory_product_sequence_number ||
        this.order.factory_product_sequence_number,
      factoryProducts: [],
      unitPrice: this.oldParams.unit_price || this.order.returned_unit_price || this.order.order_unit,
      quantity: this.oldParams.quantity || this.order.returned_quantity,
    }
  },
  computed: {
    disabledToFactoryProduct: function () {
      return this.factoryProducts.length === 0
    },
    returnedAmount: function () {
      if (! (this.unitPrice && this.quantity)) {
        return number_format(0, this.order.currency.order_amount_decimals)
      }

      return number_format(this.unitPrice * this.quantity, this.order.currency.order_amount_decimals)
    },
    amountExceptReturned: function () {
      if (! this.returnedAmount) {
        return number_format(this.order.order_amount, this.order.currency.order_amount_decimals)
      }

      return number_format(
        this.order.order_amount - this.returnedAmount.replace(/,/g, ''),
        this.order.currency.order_amount_decimals
      )
    }
  },
  methods: {
    initModal: function () {
      axios.get('/api/get-factory-products', {
        params: {
          factory_code: this.order.factory_code,
          species_code: this.order.species_code
        }
      })
        .then(response => {
          this.factoryProducts = response.data
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })

      this.showModal = true
    },
    updateUnitPrice: function (unitPrice) {
      this.unitPrice = unitPrice
    },
    submitForm: function () {
      if (this.amountExceptReturned.replace(/,/g, '') < 0) {
        alert('返品金額がもともとの注文金額を上回っています。')
        return
      }

      if (! confirm('データを登録しますか?')) {
        return
      }

      this.$refs.form.submit()
    }
  }
}
</script>
