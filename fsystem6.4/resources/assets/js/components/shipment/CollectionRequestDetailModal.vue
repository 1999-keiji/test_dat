<template>
  <div>
    <a class="btn btn-sm btn-info" @click="initModal">詳細</a>
    <form ref="form" :action="routeAction" class="form-horizontal basic-form save-data-form" method="POST">
      <modal large effect="fade" v-model="showModal">
        <div slot="modal-header" class="modal-header">
          <h4 class="modal-title">
            運送会社:&nbsp;[{{ groupedOrder.transport_company_abbreviation }}]&nbsp;&nbsp;&nbsp;
            集荷時間:&nbsp;[{{ groupedOrder.collection_time }}]
          </h4>
        </div>
        <table class="table table-color-bordered">
          <thead>
            <tr>
              <th>出荷日</th>
              <th>納入日</th>
              <th>運送会社</th>
              <th>集荷時間</th>
              <th>注文番号</th>
              <th>エンドユーザ</th>
              <th>納入先</th>
              <th>商品</th>
              <th>数量</th>
              <th>重量(g)</th>
            </tr>
          </thead>
          <tbody>
            <update-shipment-data-form
              v-for="(order, idx) in groupedOrder.orders"
              :key="idx"
              :order="order"
              :index="idx"
              :transport-companies="transportCompanies"
              :collection-times="collectionTimes">
            </update-shipment-data-form>
          </tbody>
        </table>
        <div slot="modal-footer" class="modal-footer">
          <button v-if="! hadBeenShippedAllOrders" class="btn btn-success" type="button" @click="submitForm($event)">保存</button>
          <button class="btn btn-default" type="button" @click="showModal = false">キャンセル</button>
        </div>
      </modal>
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="POST">
    </form>
  </div>
</template>

<script>
import VueStrap from 'vue-strap'

export default {
  components: {Modal: VueStrap.modal},
  props: {
    routeAction: {
      type: String,
      required: true
    },
    groupedOrder: {
      type: Object,
      required: true,
    },
    transportCompanies: {
      type: Array,
      required: true
    }
  },
  data: function () {
    return {
      showModal: false,
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      collectionTimes: []
    }
  },
  computed: {
    hadBeenShippedAllOrders: function () {
      return this.groupedOrder.orders.filter((o) => {
        return ! o.had_been_shipped
      })
        .length === 0
    }
  },
  methods: {
    initModal: function () {
      axios.get('/api/get-collection-times-by-trasport-company', {
        params: {
          transport_company_code: this.groupedOrder.transport_company_code
        }
      })
        .then(response => {
          this.collectionTimes = response.data
          this.showModal = true
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    submitForm: function (event) {
      if (confirm('注文を更新しますか？')) {
        $('.alert').remove()

        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
