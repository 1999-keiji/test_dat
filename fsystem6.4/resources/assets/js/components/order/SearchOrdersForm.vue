<style scoped>
button.btn.btn-lg,
a.btn.btn-lg {
  margin-top: 5px;
  text-align: left;
  width: 9em;
}
</style>

<template>
  <div class="row">
    <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-1 col-sm-offset-1">
      <form id="search-orders-form" class="form-horizontal basic-form" method="POST">
        <table class="table table-color-bordered search-orders-form">
          <tbody>
            <tr>
              <th>工場<span class="required-mark">*</span></th>
              <td>
                <select class="form-control" name="factory_code" v-model="factoryCode">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
                </select>
              </td>
              <th>得意先<span class="required-mark">*</span></th>
              <td>
                <select class="form-control" name="customer_code" v-model="customerCode">
                  <option value=""></option>
                  <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>ステータス<span class="required-mark">*</span></th>
              <td colspan="3" class="text-left">
                <div class="radio-inline">
                  <label><input type="radio" name="order_status" value="all" v-model="orderStatus">すべて</label>
                </div>
                <div class="radio-inline">
                  <label><input type="radio" name="order_status" value="temporary" v-model="orderStatus">仮注文</label>
                </div>
                <div class="radio-inline">
                  <label><input type="radio" name="order_status" value="fixed" v-model="orderStatus">確定済</label>
                </div>
                <div class="radio-inline">
                  <label><input type="radio" name="order_status" value="cancel" v-model="orderStatus">キャンセル</label>
                </div>
                <div class="radio-inline">
                  <label><input type="radio" name="order_status" value="slip" v-model="orderStatus">赤伝黒伝</label>
                </div>
              </td>
            </tr>
            <tr>
              <th>エンドユーザ</th>
              <td v-if="! customerCode">得意先を選択してください。</td>
              <td v-else class="text-left">
                <search-master
                  target="end_user"
                  :code="endUserCode"
                  :name="endUserName"
                  :factory-code="factoryCode"
                  :customer-code="customerCode"/>
              </td>
              <th>納入先</th>
              <td v-if="! customerCode">得意先を選択してください。</td>
              <td v-else class="text-left">
                <search-master
                  target="delivery_destination"
                  :code="deliveryDestinationCode"
                  :name="deliveryDestinationName"
                  :factory-code="factoryCode"
                  @get-selected-delivery-destination="getSelectedDeliveryDestination"/>
              </td>
            </tr>
            <tr>
              <th>注文日</th>
              <td>
                <datepicker-ja attr-name="received_date_from" :date="receivedDateFrom" :allow-empty="true"></datepicker-ja> ～
                <datepicker-ja attr-name="received_date_to" :date="receivedDateTo" :allow-empty="true"></datepicker-ja>
              </td>
              <th>納入日</th>
              <td>
                <datepicker-ja attr-name="delivery_date_from" :date="deliveryDateFrom" :allow-empty="true"></datepicker-ja> ～
                <datepicker-ja attr-name="delivery_date_to" :date="deliveryDateTo" :allow-empty="true"></datepicker-ja>
              </td>
            </tr>
            <tr>
              <th>注文番号</th>
              <td>
                <input id="order_number" name="order_number" class="form-control ime-active" :value="orderNumber" maxlength="14" type="text">
              </td>
              <th>BASE+注文番号</th>
              <td>
                <input id="base_plus_order_number" name="base_plus_order_number" class="form-control ime-active base_plus_num" :value="basePlusOrderNumber" maxlength="10" type="text">
                <input id="base_plus_order_chapter_number" name="base_plus_order_chapter_number" class="form-control ime-active base_plus_chap" :value="basePlusOrderChapterNumber" maxlength="3" type="text">
              </td>
            </tr>
            <tr>
              <th>引当状態</th>
              <td>
                <select class="form-control" name="allocation_status" v-model="allocationStatus">
                  <option value=""></option>
                  <option v-for="(value, label) in allocationStatusList" :key="value" :value="value">{{ label }}</option>
                </select>
              </td>
              <th>出荷状態</th>
              <td>
                <select class="form-control" name="shipment_status" v-model="shipmentStatus">
                  <option value=""></option>
                  <option v-for="(value, label) in shipmentStatusList" :key="value" :value="value">{{ label }}</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </form>
    </div>

    <div class="col-md-2 col-sm-2 col-xs-3">
      <button class="btn btn-lg btn-default pull-left" type="button" @click="initModal">
        赤伝黒伝追加
      </button>
      <a class="btn btn-lg btn-default pull-left" @click="exportOrders">
        <i class="fa fa-download"></i> Excel出力
      </a>
      <button class="btn btn-lg btn-default pull-left" type="button" @click="matchOrders">
        マッチング実行
      </button>
      <button class="btn btn-lg btn-default pull-left" type="button" @click="searchOrders">
        <i class="fa fa-search"></i> 検索
      </button>
    </div>

    <form id="match-orders-form" :action="matchOrdersAction" method="POST">
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="POST">
    </form>

    <modal title="赤伝黒伝データ追加" effect="fade" v-model="showModal">
      <div class="form-horizontal basic-form">
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="delivery_date" class="col-md-3 col-sm-3 control-label">
                納入日<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7">
                <datepicker-ja ref="delivery_date" attr-name="delivery_date" :date="''" :allow-empty="true"></datepicker-ja>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="delivery_destination_code" class="col-md-3 col-sm-3 control-label">
                納入先<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7">
                <select id="delivery_destination_code" class="form-control" v-model="deliveryDestination">
                  <option value=""></option>
                  <option v-for="dd in deliveryDestinations" :key="dd.code" :value="dd">{{ dd.name }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label class="col-md-3 col-sm-3 control-label">エンドユーザ</label>
              <div class="col-md-7 col-sm-7">
                <span v-if="deliveryDestination.end_user" class="shown_label">
                  {{ deliveryDestination.end_user.name }}
                </span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="product_name" class="col-md-3 col-sm-3 control-label">
                商品<span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7">
                <input id="product_name" class="form-control ime-active" type="text">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="order_quantity" class="col-md-3 col-sm-3 control-label">
                数量<span class="required-mark">*</span>
              </label>
              <div class="col-md-5 col-sm-6">
                <input-number-with-formatter
                  attrName="order_quantity"
                  :decimals="0"
                  :value="orderQuantity"
                  v-on:update-value="updateOrderQuantity"/>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="currency_code" class="col-md-3 col-sm-3 control-label">
                通貨<span class="required-mark">*</span>
              </label>
              <div class="col-md-4 col-sm-5">
                <select id="currency_code" class="form-control" v-model="currency">
                  <option v-for="c in currencies" :key="c.currency_code" :value="c">{{ c.currency_code }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="order_unit" class="col-md-3 col-sm-3 control-label">
                単価<span class="required-mark">*</span>
              </label>
              <div class="col-md-5 col-sm-6">
                <input-number-with-formatter
                  attrName="order_unit"
                  :decimals="currency.order_unit_decimals"
                  :value="orderUnit"
                  v-on:update-value="updateOrderUnit"/>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label class="col-md-3 col-sm-3 control-label">合価</label>
              <div class="col-md-7 col-sm-7">
                <span class="shown_label text-right">{{ orderAmount }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-sm-8 col-xs-12 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group simulation_info-form">
              <label for="order_message" class="col-md-3 col-sm-3 control-label">備考</label>
              <div class="col-md-7 col-sm-7">
                <input id="order_message" class="form-control ime-inactive" type="text">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-success" type="button" @click="saveSlip">保存</button>
        <button class="btn btn-default" type="button" @click="showModal = false">キャンセル</button>
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    searchOrdersAction: {
      type: String,
      required: true
    },
    exportOrdersAction: {
      type: String,
      required: true
    },
    matchOrdersAction: {
      type: String,
      required: true
    },
    saveSlipAction: {
      type: String,
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
    allocationStatusList: {
      type: Object,
      required: true
    },
    shipmentStatusList: {
      type: Object,
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
    },
    currencies: {
      type: Array,
      required: true
    },
    defaultCurrencyCode: {
      type: String,
      required: true
    }
  },
  data: function () {
    let shipmentStatus = this.oldParams.shipment_status || this.searchParams.shipment_status
    if (shipmentStatus === undefined && Object.keys(this.searchParams).length === 0) {
      shipmentStatus = this.shipmentStatusList['未出荷']
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code,
      customerCode: this.oldParams.customer_code || this.searchParams.customer_code || '',
      endUserCode: this.oldParams.end_user_code || this.searchParams.end_user_code,
      endUserName: this.oldParams.end_user_name || this.searchParams.end_user_name,
      deliveryDestinationCode: this.oldParams.delivery_destination_code || this.searchParams.delivery_destination_code,
      deliveryDestinationName: this.oldParams.delivery_destination_name || this.searchParams.delivery_destination_name,
      orderStatus: this.oldParams.order_status || this.searchParams.order_status || 'all',
      receivedDateFrom: this.oldParams.received_date_from || this.searchParams.received_date_from || '',
      receivedDateTo: this.oldParams.received_date_to || this.searchParams.received_date_to || '',
      deliveryDateFrom: this.oldParams.delivery_date_from || this.searchParams.delivery_date_from || '',
      deliveryDateTo: this.oldParams.delivery_date_to || this.searchParams.delivery_date_to || '',
      orderNumber: this.oldParams.order_number || this.searchParams.order_number,
      basePlusOrderNumber: this.oldParams.base_plus_order_number || this.searchParams.base_plus_order_number,
      basePlusOrderChapterNumber: this.oldParams.base_plus_order_chapter_number || this.searchParams.base_plus_order_chapter_number,
      allocationStatus: this.oldParams.allocation_status || this.searchParams.allocation_status || '',
      shipmentStatus,
      showModal: false,
      deliveryDestination: {},
      deliveryDestinations: [],
      orderQuantity: '',
      currency: this.currencies.filter(c => c.currency_code === this.defaultCurrencyCode)[0],
      orderUnit: ''
    }
  },
  computed: {
    orderAmount: function () {
      if (! (this.orderQuantity && this.orderUnit)) {
        return ''
      }

      const number_format = require('locutus/php/strings/number_format')
      return number_format(this.orderQuantity * this.orderUnit, this.currency.order_amount_decimals)
    }
  },
  methods: {
    searchOrders: function () {
      document.getElementById('search-orders-form').action = this.searchOrdersAction
      document.getElementById('search-orders-form').submit()
    },
    exportOrders: function () {
      document.getElementById('search-orders-form').action = this.exportOrdersAction
      document.getElementById('search-orders-form').submit()
    },
    matchOrders: function () {
      if (confirm('マッチング処理を実行してよろしいですか？')) {
        document.getElementById('match-orders-form').submit()
      }
    },
    getSelectedDeliveryDestination: function (deliveryDestination) {
      this.deliveryDestinationCode = deliveryDestination.code
      this.deliveryDestinationName = deliveryDestination.name
      this.endUserCode = deliveryDestination.end_user.code
      this.endUserName = deliveryDestination.end_user.name
    },
    initModal: function () {
      if (! this.factoryCode) {
        alert('工場を選択してください。')
        return
      }
      if (! this.customerCode) {
        alert('得意先を選択してください。')
        return
      }

      axios.get('/api/search-delivery-destinations', {params: {
        factory_code: this.factoryCode,
        customer_code: this.customerCode,
        limited: 0
      }})
        .then(response => {
          this.deliveryDestinations = response.data
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
          return
        })

      this.showModal = true
    },
    updateOrderQuantity: function (orderQuantity) {
      this.orderQuantity = parseInt(orderQuantity)
    },
    updateOrderUnit: function (orderUnit) {
      this.orderUnit = parseInt(orderUnit)
    },
    saveSlip: function () {
      if (! confirm('赤伝黒伝データを追加してよろしいですか？')) {
        return
      }

      axios.post(this.saveSlipAction, {
        factory_code: this.factoryCode,
        customer_code: this.customerCode,
        delivery_date: this.$refs.delivery_date.value,
        end_user_code: this.deliveryDestination.end_user ? this.deliveryDestination.end_user.code : null,
        delivery_destination_code: this.deliveryDestination.code,
        product_name: document.getElementById('product_name').value,
        order_quantity: this.orderQuantity,
        order_unit: this.orderUnit,
        currency_code: this.currency.currency_code,
        order_amount: this.orderAmount,
        order_message: document.getElementById('order_message').value
      })
        .then(() => {
          alert('赤伝黒伝データの追加が完了しました。')

          this.showModal = false
          location.reload()
        })
        .catch(error => {
          if (error.response.status === 422) {
            const errors = Object.values(error.response.data.errors)
              .map(errors => errors[0])
              .join('\n')

            alert(errors)
          }
          if (error.response.status === 500) {
            alert('通信エラーが発生しました。しばらくお待ちください。')
          }
        })
    }
  }
}
</script>
