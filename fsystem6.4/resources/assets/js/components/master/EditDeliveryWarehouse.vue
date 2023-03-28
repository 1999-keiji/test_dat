<style scoped>
  span.shown_label {
    float: left
  }
  #date-suffix {
    padding: 0;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-sm btn-info" @click="initModal">修正</button>
    <modal title="納入倉庫設定" effect="fade" v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">納入先</label>
              <div class="col-md-6 col-sm-8">
                <span class="shown_label">{{ deliveryWarehouse.delivery_destination_abbreviation }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label">倉庫</label>
              <div class="col-md-6 col-sm-8">
                <span class="shown_label">{{ deliveryWarehouse.warehouse_abbreviation }}</span>
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
                  :value="deliveryWarehouse.delivery_lead_time"
                  required>
              </div>
              <div id="date-suffix" class="col-md-1 col-sm-1 text-left">日</div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
                出荷LT
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2">
                <select class="form-control" name="shipment_lead_time" required>
                  <option value=""></option>
                  <option v-for="(value, label) in shipmentLeadTimeList" :key="value" :value="value" :selected="value == deliveryWarehouse.shipment_lead_time">{{ label }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="PATCH">
      </form>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default btn-lg" type="button" @click="submitForm($event)">
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
    deliveryWarehouse: {
      type: Object,
      required: true
    },
    deliveryLeadTime: {
      type: Object,
      required: true
    },
    shipmentLeadTimeList: {
      type: Object,
      required: true
    },
  },
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      showModal: false
    }
  },
  methods: {
    initModal: function () {
      this.showModal = true
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
