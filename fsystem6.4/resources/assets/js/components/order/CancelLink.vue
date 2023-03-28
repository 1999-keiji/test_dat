<style scoped>
  button.btn.btn-cancel-link {
    width: 50%;
  }
  button.btn.btn-cancel-link>i.fa-lock {
    color: tomato;
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
  .fixed-orders {
    margin-top: 2%;
  }
</style>

<template>
  <div>
    <button class="btn btn-lg btn-default btn-cancel-link" @click="showModal = true">
      <i class="fa fa-lock"></i>
    </button>

    <modal title="注文確定データ紐付解除" class="text-left" effect="fade" large v-model="showModal">
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 temporary-order">
          紐付け解除する仮注文データ
          <table class="table table-color-bordered table-more-condensed">
            <thead>
              <tr>
                <th>処理区分</th>
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
            </thead>
            <tbody>
              <tr>
                <td>{{ temporaryOrder.process_class.label }}</td>
                <td>{{ temporaryOrder.order_number }}</td>
                <td>{{ temporaryOrder.delivery_date | formatDate }}</td>
                <td class="text-left">{{ temporaryOrder.end_user_abbreviation }}</td>
                <td class="text-left">{{ temporaryOrder.delivery_destination_abbreviation }}</td>
                <td class="text-left">{{ temporaryOrder.product_name }}</td>
                <td class="text-right">{{ temporaryOrder.order_quantity }}&nbsp;{{ temporaryOrder.place_order_unit_code }}</td>
                <td class="text-right">{{ temporaryOrder.formatted_order_unit }}</td>
                <td class="text-right">{{ temporaryOrder.formatted_order_amount }}</td>
                <td>{{ temporaryOrder.currency_code }}</td>
                <td>{{ getBasePlusOrderNumber }}</td>
                <td class="text-left">{{ temporaryOrder.end_user_order_number }}</td>
                <td class="text-left">{{ temporaryOrder.order_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 fixed-orders">
          紐付け解除する確定注文データ
          <table class="table table-color-bordered table-more-condensed">
            <thead>
              <tr>
                <th>処理区分</th>
                <th>注文番号</th>
                <th>納入日</th>
                <th>エンドユーザ</th>
                <th>納入先</th>
                <th>商品</th>
                <th>数量(単位)</th>
                <th>単価</th>
                <th>合価</th>
                <th>通貨</th>
                <th>Base+注文番号</th>
                <th>エンドユーザ<br>注文番号</th>
                <th>備考</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="o in fixedOrders" :key="o.order_number">
                <td>{{ o.process_class.label }}</td>
                <td>{{ o.order_number }}</td>
                <td>{{ o.delivery_date | formatDate }}</td>
                <td class="text-left">{{ o.end_user_abbreviation }}</td>
                <td class="text-left">{{ o.delivery_destination_abbreviation }}</td>
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
        <button class="btn btn-danger" type="button" @click="cancelLink">解除</button>
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
    temporaryOrder: {
      type: Object,
      required: true
    },
    fixedOrders: {
      type: Array,
      required: true
    },
    routeAction: {
      type: String,
      required: true
    }
  },
  data: function () {
    return {
      showModal: false
    }
  },
  computed: {
    getBasePlusOrderNumber: function () {
      if (! this.temporaryOrder.base_plus_order_number || ! this.temporaryOrder.base_plus_order_chapter_number) {
        return ''
      }

      return [this.temporaryOrder.base_plus_order_number, this.temporaryOrder.base_plus_order_chapter_number].join('-')
    }
  },
  filters: {
    formatDate: function (date) {
      const moment = require('moment')
      return moment(date).format('YYYY/MM/DD')
    }
  },
  methods: {
    cancelLink: function () {
      if (! confirm('注文確定データ紐付解除を行います。よろしいですか？')) {
        return
      }

      axios.delete(this.routeAction)
        .then(() => {
          alert('注文確定データの紐付解除が完了しました。')
          location.reload()
        })
        .catch(error => {
          if (error.response.status === 423) {
            alert('ほかの担当者が修正しています。')
          }
          if (error.response.status === 500) {
            alert('通信エラーが発生しました。しばらくお待ちください。')
          }
        })
    }
  }
}
</script>
