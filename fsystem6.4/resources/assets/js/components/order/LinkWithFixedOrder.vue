<style scoped>
  button.btn.btn-link-with-order {
    width: 50%;
  }
  button.btn.btn-link-with-order>i.fa-unlock {
    color: steelblue;
  }

  .temporary-order,
  .fixed-orders {
    margin-left: 1.6%;
    overflow: auto;
    width: 96.8%;
  }
  .temporary-order>table,
  .fixed-orders>table {
    margin: 0 0 0px -15px;
  }
  .search-fixed-orders, .search-condition {
    margin-top: 2%;
    margin-bottom: -10px;
  }
  .search-condition {
    margin-left: 5%;
  }
</style>

<template>
  <div>
    <button class="btn btn-lg btn-default btn-link-with-order" @click="showModal = true">
      <i class="fa fa-unlock"></i>
    </button>

    <modal title="注文確定データ紐付設定" class="text-left" effect="fade" large v-model="showModal">
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 temporary-order">
          <table class="table table-color-bordered table-more-condensed">
            <tbody>
              <tr>
                <th>注文日</th>
                <th>注文番号</th>
                <th>納入日</th>
                <th>エンドユーザ</th>
                <th>納入先</th>
                <th>商品</th>
                <th>数量(単位)</th>
                <th>単価</th>
                <th>合価</th>
                <th>通貨</th>
                <th>BASE+注文番号</th>
                <th>エンドユーザ<br>注文番号</th>
                <th>備考</th>
              </tr>
              <tr>
                <td>{{ order.received_date }}</td>
                <td>{{ order.order_number }}</td>
                <td>{{ order.delivery_date }}</td>
                <td class="text-left">{{ order.end_user_abbreviation }}</td>
                <td class="text-left">{{ order.delivery_destination_abbreviation }}</td>
                <td class="text-left">{{ order.product_name }}</td>
                <td class="text-right">{{ order.order_quantity }}&nbsp;{{ order.place_order_unit_code }}</td>
                <td class="text-right">{{ order.formatted_order_unit }}</td>
                <td class="text-right">{{ order.formatted_order_amount }}</td>
                <td>{{ order.currency_code }}</td>
                <td>{{ getBasePlusOrderNumber }}</td>
                <td class="text-left">{{ order.end_user_order_number }}</td>
                <td class="text-left">{{ order.order_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-md-2 col-sm-2 col-xs-2 col-md-offset-9 col-sm-offset-9 col-xs-offset-10 search-fixed-orders">
          <button class="btn btn-lg btn-default pull-right" type="button" @click="searchFixedOrders">
            <i class="fa fa-search"></i>&nbsp;検索
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-md-10 col-sm-10 col-xs-12 search-condition">
          <table class="table table-color-bordered searched-orders">
            <tbody>
              <tr>
                <th>工場</th>
                <td class="text-left">{{ factory.factory_abbreviation }}</td>
                <th>得意先</th>
                <td class="text-left">{{ customer.customer_abbreviation }}</td>
              </tr>
              <tr>
                <th>エンドユーザ</th>
                <td class="text-left">{{ order.end_user_abbreviation }}</td>
                <th>納入先</th>
                <td class="text-left">{{ order.delivery_destination_abbreviation }}</td>
              </tr>
              <tr>
                <th>注文日</th>
                <td>
                  <datepicker-ja
                    attr-name="received_date_from"
                    :date="receivedDateFrom"
                    :allow-empty="true"
                    @update-date="updateReceivedDateFrom">
                  </datepicker-ja>&nbsp;～
                  <datepicker-ja
                    attr-name="received_date_to"
                    :date="receivedDateTo"
                    :allow-empty="true"
                    @update-date="updateReceivedDateTo"></datepicker-ja>
                </td>
                <th>納入日</th>
                <td>
                  <datepicker-ja
                    attr-name="delivery_date_from"
                    :date="deliveryDateFrom"
                    :allow-empty="true"
                    @update-date="updateDeliveryDateFrom">
                  </datepicker-ja>&nbsp;～
                  <datepicker-ja
                    attr-name="delivery_date_to"
                    :date="deliveryDateTo"
                    :allow-empty="true"
                    @update-date="updateDeliveryDateTo">
                  </datepicker-ja>
                </td>
              </tr>
              <tr>
                <th>注文番号</th>
                <td>
                  <input class="form-control ime-active" maxlength="14" type="text" v-model="orderNumber">
                </td>
                <th>BASE+注文番号</th>
                <td>
                  <input class="form-control ime-active base_plus_num" maxlength="10" type="text" v-model="basePlusOrderNumber">
                  <input class="form-control ime-active base_plus_chap" maxlength="3" type="text" v-model="basePlusOrderChapterNumber">
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div v-if="fixedOrders.length !== 0" class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 fixed-orders">
          <p>最大20件まで表示されます。</p>
          <table class="table table-color-bordered table-more-condensed">
            <tbody>
              <tr>
                <th>紐付<br>設定</th>
                <th>注文日</th>
                <th>注文番号</th>
                <th>納入日</th>
                <th>商品</th>
                <th>数量(単位)</th>
                <th>単価</th>
                <th>合価</th>
                <th>通貨</th>
                <th>BASE+注文番号</th>
                <th>エンドユーザ<br>注文番号</th>
                <th>備考</th>
              </tr>
              <tr v-for="o in fixedOrders" :key="o.order_number">
                <td><input type="checkbox" :value="o.order_number" v-model="orderNumberList"></td>
                <td>{{ o.received_date }}</td>
                <td>{{ o.order_number }}</td>
                <td>{{ o.delivery_date }}</td>
                <td class="text-left">{{ o.product_name }}</td>
                <td class="text-right">{{ o.order_quantity }}&nbsp;{{ o.place_order_unit_code }}</td>
                <td class="text-right">{{ o.formatted_order_unit }}</td>
                <td class="text-right">{{ o.formatted_order_amount }}</td>
                <td>{{ o.currency_code }}</td>
                <td>{{ o.base_plus_order_number }}-{{ o.base_plus_order_chapter_number }}</td>
                <td class="text-left">{{ o.end_user_order_number }}</td>
                <td class="text-left">{{ o.order_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-success" type="button" @click="linkOrder">保存</button>
        <button class="btn btn-default" type="button" @click="showModal = false">キャンセル</button>
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {
    Modal: VueStrap.modal,
  },
  props: {
    factory: {
      type: Object,
      required: true
    },
    customer: {
      type: Object,
      required: true
    },
    order: {
      type: Object,
      required: true
    },
    routeAction: {
      type: String,
      required: true
    },
    errorMessages: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      showModal: false,
      receivedDateFrom: '',
      receivedDateTo: '',
      deliveryDateFrom: '',
      deliveryDateTo: '',
      orderNumber: '',
      basePlusOrderNumber: '',
      basePlusOrderChapterNumber: '',
      fixedOrders: [],
      orderNumberList: []
    }
  },
  computed: {
    getBasePlusOrderNumber: function () {
      if (! this.order.base_plus_order_number || ! this.order.base_plus_order_chapter_number) {
        return ''
      }

      return [this.order.base_plus_order_number, this.order.base_plus_order_chapter_number].join('-')
    }
  },
  methods: {
    searchFixedOrders: function () {
      axios.get('/order/order_list/search-fixed-orders', {
        params: {
          factory_code: this.factory.factory_code,
          customer_code: this.customer.customer_code,
          end_user_code: this.order.end_user_code,
          delivery_destination_code: this.order.delivery_destination_code,
          factory_product_sequence_number: this.order.factory_product_sequence_number,
          received_date_from: this.receivedDateFrom,
          received_date_to: this.receivedDateTo,
          delivery_date_from: this.deliveryDateFrom,
          delivery_date_to: this.deliveryDateTo,
          order_number: this.orderNumber,
          base_plus_order_number: this.basePlusOrderNumber,
          base_plus_order_chapter_number: this.basePlusOrderChapterNumber
        }
      })
        .then(response => {
          this.fixedOrders = response.data
          if (this.fixedOrders.length === 0) {
            alert('条件に合致する注文データがありませんでした。')
          }
        })
        .catch(() => alert('通信エラーが発生しました。しばらくお待ちください。'))
    },
    linkOrder: function () {
      if (this.orderNumberList.length === 0) {
        alert('確定注文データが選択されていません。')
        return
      }

      let message = '注文確定データ紐付設定をします。よろしいですか？'
      if (this.order.had_been_shipped) {
        message = '出荷済の仮注文を紐づける場合、紐づけの解除はできません。\n' + message
      }

      const totalOrderQuantity = this.fixedOrders
        .filter(o => this.orderNumberList.includes(o.order_number))
        .reduce((totalOrderQuantity, o) => {
          return totalOrderQuantity + o.order_quantity
        }, 0)

      if (this.order.order_quantity !== totalOrderQuantity) {
        message = '仮注文の数量と、確定注文の数量合計が一致していません。\n' + message
      }

      if (! confirm(message)) {
        return
      }

      axios.post(this.routeAction, {order_number_list: this.orderNumberList})
        .then(() => {
          alert('注文確定データの紐付設定が完了しました。')
          location.reload()
        })
        .catch(error => {
          if (error.response.status === 423) {
            alert('ほかの担当者が修正しています。')
            location.reload()
          }
          if (error.response.status === 422) {
            alert(this.errorMessages[error.response.data]['message'])
          }
          if (error.response.status === 500) {
            alert('通信エラーが発生しました。しばらくお待ちください。')
          }
        })
    },
    updateReceivedDateFrom: function (receivedDateFrom) {
      this.receivedDateFrom = receivedDateFrom
    },
    updateReceivedDateTo: function (receivedDateTo) {
      this.receivedDateTo = receivedDateTo
    },
    updateDeliveryDateFrom: function (deliveryDateFrom) {
      this.deliveryDateFrom = deliveryDateFrom
    },
    updateDeliveryDateTo: function (deliveryDateTo) {
      this.deliveryDateTo = deliveryDateTo
    }
  }
}
</script>
