<template>
  <div>
    <a @click="initModal" role="button">{{ date.day }}</a>
    <modal title="カレンダー設定" effect="fade" v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="currentUrl" method="POST">
        <div class="row">
          <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">日付</label>
              <div class="col-md-7 col-sm-7">
                <span class="shown_label">{{ date.formatted_date }}</span>
                <input name="date" type="hidden" :value="date.date">
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5 control-label">
                状態
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-7 col-sm-7">
                <label>工場休</label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="factory_is_rest" value="1" :disabled="! canSaveFactory" v-model="factoryIsRest">ON
                </label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="factory_is_rest" value="0" :disabled="! canSaveFactory" v-model="factoryIsRest">OFF
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5"></label>
              <div class="col-md-7 col-sm-7">
                <label>出荷休</label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="shipment_is_rest" value="1" :disabled="! canSaveFactory" v-model="shipmentIsRest">ON
                </label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="shipment_is_rest" value="0" :disabled="! canSaveFactory" v-model="shipmentIsRest">OFF
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label class="col-md-5 col-sm-5"></label>
              <div class="col-md-7 col-sm-7">
                <label>納入休</label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="delivery_is_rest" value="1" :disabled="! canSaveFactory" v-model="deliveryIsRest">ON
                </label>
                <label class="radio-inline rest-radio">
                  <input type="radio" name="delivery_is_rest" value="0" :disabled="! canSaveFactory" v-model="deliveryIsRest">OFF
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 col-sm-7 col-xs-8 col-md-offset-1 col-sm-offset-1">
            <div class="row form-group">
              <label for="remark" class="col-md-5 col-sm-5 control-label">備考</label>
              <div class="col-md-7 col-sm-7">
                <input id="remark" class="form-control" name="remark" maxlength="50" type="text" :value="factoryRest.remark || ''" :disabled="! canSaveFactory">
              </div>
            </div>
          </div>
        </div>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </form>

      <div slot="modal-footer" class="modal-footer">
        <button v-if="canSaveFactory" class="btn btn-success" type="button" @click="saveFactoryRest($event)">保存</button>
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
    date: {
      type: Object,
      required: true
    },
    factoryRest: {
      type: [Array, Object],
      required: true
    },
    canSaveFactory: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      showModal: false,
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      currentUrl: location.href.split('?')[0],
      factoryIsRest: this.factoryRest.factory_is_rest || 0,
      shipmentIsRest: this.factoryRest.shipment_is_rest || 0,
      deliveryIsRest: this.factoryRest.delivery_is_rest || 0,
    }
  },
  methods: {
    initModal: function () {
      this.showModal = true
    },
    saveFactoryRest: function (event) {
      if (confirm('データを登録しますか？')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
