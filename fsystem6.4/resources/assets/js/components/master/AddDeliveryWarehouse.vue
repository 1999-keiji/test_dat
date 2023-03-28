<template>
  <div>
    <button type="button" class="btn btn-lg btn-default pull-right" @click="initModal">
      <i class="fa fa-plus"></i> 追加
    </button>
    <modal title="納入倉庫設定" effect="fade" large v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div v-if="! deliveryDestinationCode" class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">納入先名検索</label>
              <div class="col-md-4 col-sm-4">
                <input class="form-control text-left ime-active" type="text" v-model="searchDeliveryDestinationName">
              </div>
              <div class="col-md-2 col-sm-2">
                <button class="btn btn-default" type="button" @click="searchDeliveryDestination()">絞り込み</button>
              </div>
              <div class="col-md-2 col-sm-2">
                <button class="btn btn-default" type="button" @click="searchDeliveryDestination(true)">全件</button>
              </div>
            </div>
            <div v-if="! deliveryDestinationCode" class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
                納入先
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-4 col-sm-4">
                <select class="form-control"
                  name="delivery_destination_code"
                  v-model="selectedDeliveryDestinationCode"
                  :disabled="disabledToSelectDeliveryDestination"
                  required>
                  <option value=""></option>
                  <option v-for="dd in deliveryDestinations"
                    :key="dd.code"
                    :value="dd.code">
                    {{ dd.name }}
                  </option>
                </select>
              </div>
            </div>
            <div v-if="! warehouseCode" class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
                倉庫
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-3 col-sm-4">
                <select class="form-control" name="warehouse_code" v-model="selectedWarehouseCode" required>
                  <option value=""></option>
                  <option v-for="w in warehouses" :key="w.warehouse_code" :value="w.warehouse_code">{{ w.warehouse_abbreviation }}</option>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
                配送LT
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2">
                <input
                  class="form-control text-right ime-inactive"
                  name="delivery_lead_time"
                  :maxlength="deliveryLeadTime.max_length"
                  :title="deliveryLeadTime.help_text"
                  type="text"
                  :value="deliveryLeadTimeOld"
                  required>
              </div>
              <div class="col-md-1 col-sm-1">日</div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
                出荷LT
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2">
                <select class="form-control" name="shipment_lead_time" required>
                  <option value=""></option>
                  <option v-for="(value, label) in shipmentLeadTimeList" :key="value" :value="value">{{ label }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <input v-if="deliveryDestinationCode" name="delivery_destination_code" type="hidden" :value="deliveryDestinationCode">
        <input v-if="warehouseCode" name="warehouse_code" type="hidden" :value="warehouseCode">
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
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

export default {
  components: {Modal: VueStrap.modal},
  props: {
    routeAction: {
      type: String,
      required: true
    },
    deliveryDestinationCode: {
      type: String,
      default: null
    },
    warehouseCode: {
      type: String,
      default: null
    },
    warehouses: {
      type: Array,
      default: function () {
        return []
      }
    },
    deliveryLeadTime: {
      type: Object,
      required: true
    },
    shipmentLeadTimeList: {
      type: Object,
      required: true
    },
    deliveryLeadTimeOld: {
      type: String,
      default: null
    }
  },
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      showModal: false,
      selectedDeliveryDestinationCode: null,
      searchDeliveryDestinationName: null,
      selectedWarehouseCode: null,
      deliveryDestinations: []
    }
  },
  computed: {
    disabledToSelectDeliveryDestination: function () {
      return this.deliveryDestinations.length === 0
    }
  },
  methods: {
    initModal: function () {
      this.showModal = true
    },
    searchDeliveryDestination: function (searchAll = false) {
      if (! this.searchDeliveryDestinationName && ! searchAll) {
        alert('絞り込み条件を入力してください。')
        return
      }

      this.deliveryDestinations = []
      axios.get('/api/search-delivery-destinations', {
        params: {
          master_name: searchAll ? null : this.searchDeliveryDestinationName,
          master_abbreviation: searchAll ? null : this.searchDeliveryDestinationName
        }
      })
        .then(response => {
          this.deliveryDestinations = response.data
          if (this.disabledToSelectDeliveryDestination) {
            alert('条件に一致する納入先が見つかりませんでした。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    submitForm: function (event) {
      if (confirm('データを登録しますか？')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
