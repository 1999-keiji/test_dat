<style scoped>
  table.table-in-modal {
    margin-top: 1em;
  }
  table.table-in-modal>thead>tr>th {
    min-width: 100px;
  }
</style>

<template>
  <div>
    <button class="btn btn-sm btn-info" type="button" @click="showModal = true">詳細</button>
    <modal title="伝票詳細" class="text-left" effect="fade" large v-model="showModal">
      <div class="row">
        <div class="col-md-12 col-sm-10">
          <table class="table table-color-bordered table-more-condensed table-in-modal">
            <thead>
              <tr>
                <th>注文番号</th>
                <th>BASE+注文番号</th>
                <th>エンドユーザ<br>注文番号</th>
                <th>ステータス</th>
                <th>商品</th>
                <th>数量(単位)</th>
                <th>金額</th>
                <th>通貨</th>
                <th>備考</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="o in orders" :key="o.order_number">
                <td>{{ o.order_number }}</td>
                <td>{{ getBasePlusOrderNumber(o) }}</td>
                <td class="text-left">{{ o.end_user_order_number }}</td>
                <td>{{ o.slip_status_type.label }}</td>
                <td class="text-left">{{ o.product_name }}</td>
                <td class="text-right">{{ o.order_quantity }}&nbsp;{{ o.unit }}</td>
                <td class="text-right">{{ o.formatted_order_amount }}</td>
                <td>{{ o.currency_code }}</td>
                <td class="text-left">{{ o.order_message }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default" type="button" @click="showModal = false">閉じる</button>
      </div>
    </modal>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: { Modal: VueStrap.modal },
  props: {
    orders: {
      type: Array,
      required: true
    }
  },
  data: () =>  {
    return {
      showModal: false
    }
  },
  methods: {
    getBasePlusOrderNumber: function (order) {
      if (! order.base_plus_order_number || ! order.base_plus_order_chapter_number) {
        return ''
      }

      return [order.base_plus_order_number, order.base_plus_order_chapter_number].join('-')
    }
  }
}
</script>
