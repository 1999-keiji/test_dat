<style scoped>
  span.shown_label {
    float: left
  }
  #add-row {
    margin-top: -2em;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-info btn-sm" @click="initModal">修正</button>
    <modal title="納入商品設定" effect="fade" large v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">工場</label>
              <div class="col-md-4 col-sm-4 col-xs-6">
                <span class="shown_label">{{ deliveryFactoryProduct.factory_product_abbreviation }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">工場取扱商品</label>
              <div class="col-md-4 col-sm-4 col-xs-6">
                <span class="shown_label">{{ deliveryFactoryProduct.factory_product_abbreviation }}</span>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">特別価格</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <table class="table table-color-bordered table-more-condensed">
                  <thead>
                    <tr>
                      <th>削除</th>
                      <th>適用開始日</th>
                      <th>適用終了日</th>
                      <th>単価</th>
                      <th>通貨</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(fpsp, index) in factoryProductSpecielPrices" :key="index">
                      <td>
                        <button class="btn btn-danger" type="button" @click="deleteSpecialPrice(index)">削除</button>
                      </td>
                      <td>
                        <datepicker-ja attr-name="application_started_on[]" :date="fpsp.application_started_on"></datepicker-ja>
                      </td>
                      <td>
                        <datepicker-ja attr-name="application_ended_on[]" :date="fpsp.application_ended_on"></datepicker-ja>
                      </td>
                      <td>
                        <input-number-with-formatter
                          attr-name="unit_price[]"
                          :value="fpsp.unit_price"
                          :max-length="unitPrice.max_length"
                          :decimals="unitPrice.decimals"
                          :help-text="unitPrice.help_text">
                        </input-number-with-formatter>
                      </td>
                      <td>
                        <select class="form-control" name="currency_code[]">
                          <option value=""></option>
                          <option v-for="c in currencies" :key="c.currency_code" :value="c.currency_code" :selected="c.currency_code === fpsp.currency_code">{{ c.currency_code }}</option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
                <button id="add-row" type="button" class="btn btn-default pull-right" @click="addSpecialPrice">
                  <i class="fa fa-plus"></i> 追加
                </button>
              </div>
            </div>
          </div>
        </div>
        <input name="delivery_destination_code" type="hidden" :value="deliveryFactoryProduct.delivery_destination_code">
        <input ref="factory_code" name="factory_code" type="hidden" :value="deliveryFactoryProduct.factory_code">
        <input ref="factory_product_sequence_number" name="factory_product_sequence_number" type="hidden" :value="deliveryFactoryProduct.factory_product_sequence_number">
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="PATCH">
      </form>
      <div slot="modal-footer" class="modal-footer">
        <button class="btn btn-default btn-lg" type="button" @click="submitForm">
          <i class="fa fa-save"></i> 保存
        </button>
        <button class="btn btn-default btn-lg" type="button" @click="closeModal">
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
  props: ['routeAction', 'deliveryFactoryProduct', 'currentFactoryProductSpecielPrices', 'currencies', 'unitPrice'],
  data: function () {
    const _ = require('lodash')
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      showModal: false,
      factoryProductSpecielPrices: _.cloneDeep(this.currentFactoryProductSpecielPrices),
      lodash: _
    }
  },
  methods: {
    initModal: function () {
      this.showModal = true
    },
    closeModal: function () {
      this.showModal = false
      this.factoryProductSpecielPrices = this.lodash.cloneDeep(this.currentFactoryProductSpecielPrices)
    },
    addSpecialPrice: function () {
      this.factoryProductSpecielPrices.push({
        application_started_on: '',
        application_ended_on: '',
        unit_price: '',
        currency_code: ''
      })
    },
    deleteSpecialPrice: function (index) {
      this.factoryProductSpecielPrices.splice(index, 1)
    },
    submitForm: function () {
      if (confirm('データを登録しますか?')) {
        this.$refs.form.submit()
      }
    }
  }
}
</script>
