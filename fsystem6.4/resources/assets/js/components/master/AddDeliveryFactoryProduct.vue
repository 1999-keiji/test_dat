<style scoped>
  #add-row {
    margin-top: -2em;
  }
</style>

<template>
  <div>
    <button type="button" class="btn btn-lg btn-default pull-right" @click="initModal">
      <i class="fa fa-plus"></i> 追加
    </button>
    <modal title="納入商品設定" effect="fade" large v-model="showModal">
      <form ref="form" class="form-horizontal basic-form" :action="routeAction" method="POST">
        <div class="row">
          <div class="col-md-12 col-sm-12">
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
                工場
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-2 col-sm-2 col-xs-4">
                <select class="form-control" v-model="factory" @change="getFactoryProducts">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factor_code" :value="f">{{ f.factory_abbreviation }}</option>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
                工場取扱商品
                <span class="required-mark">*</span>
              </label>
              <div class="col-md-3 col-sm-3 col-xs-6">
                <select class="form-control" :disabled="disabledToSelectFactoryProduct" v-model="factoryProduct" @change="getProductSpecialPrices">
                  <option value=""></option>
                  <option v-for="fp in factoryProducts" :key="fp.factory_product_sequence_number" :value="fp">{{ fp.factory_product_abbreviation }}</option>
                </select>
              </div>
            </div>
            <div class="row form-group">
              <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
                特別価格
              </label>
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
                    <tr v-for="(psp, index) in productSpecialPrices" :key="index">
                      <td>
                        <button class="btn btn-danger" type="button" @click="deleteSpecialPrice(index)">削除</button>
                      </td>
                      <td>
                        <datepicker-ja attr-name="application_started_on[]" :date="psp.application_started_on"></datepicker-ja>
                      </td>
                      <td>
                        <datepicker-ja attr-name="application_ended_on[]" :date="psp.application_ended_on"></datepicker-ja>
                      </td>
                      <td>
                        <input-number-with-formatter
                          attr-name="unit_price[]"
                          :value="psp.unit_price"
                          :max-length="unitPrice.max_length"
                          :decimals="unitPrice.decimals"
                          :help-text="unitPrice.help_text">
                        </input-number-with-formatter>
                      </td>
                      <td>
                        <select class="form-control" name="currency_code[]">
                          <option value=""></option>
                          <option v-for="c in currencies" :key="c.currency_code" :value="c.currency_code" :selected="c.currency_code === psp.currency_code">{{ c.currency_code }}</option>
                        </select>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row">
              <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
                <button id="add-row" type="button" class="btn btn-default pull-right" :disabled="disabledToAdd" @click="addSpecialPrice">
                  <i class="fa fa-plus"></i> 追加
                </button>
              </div>
            </div>
          </div>
        </div>
        <input name="delivery_destination_code" type="hidden" :value="deliveryDestinationCode">
        <input ref="factory_code" name="factory_code" type="hidden">
        <input ref="factory_product_sequence_number" name="factory_product_sequence_number" type="hidden">
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
  props: ['routeAction', 'deliveryDestinationCode', 'factories', 'currencies', 'unitPrice'],
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      showModal: false,
      factory: null,
      factoryProduct: null,
      factoryProducts: [],
      productSpecialPrices: [],
      disabledToAdd: false
    }
  },
  computed: {
    disabledToSelectFactoryProduct: function () {
      return this.factoryProducts.length === 0
    }
  },
  methods: {
    initModal: function () {
      this.factory = null
      this.factoryProduct = null
      this.factoryProducts = []
      this.productSpecialPrices = []
      this.showModal = true
    },
    getFactoryProducts: function () {
      this.factoryProduct = null
      this.factoryProducts = this.productSpecialPrices = []
      if (! this.factory) {
        return
      }

      axios.get('/api/get-factory-products', {
        params: {
          factory_code: this.factory.factory_code
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToSelectFactoryProduct) {
            alert('工場取扱商品が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    getProductSpecialPrices: function () {
      this.productSpecialPrices = []
      if (! this.factoryProduct) {
        return
      }

      this.disabledToAdd = true
      axios.get('/api/get-product-special-prices', {
        params: {
          delivery_destination_code: this.deliveryDestinationCode,
          factory_code: this.factory.factory_code,
          product_code: this.factoryProduct.product_code
        }
      })
        .then(response => {
          if (response.data.length !== 0) {
            this.productSpecialPrices = response.data
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
        .finally(() => {
          this.disabledToAdd = false
        })
    },
    addSpecialPrice: function () {
      this.productSpecialPrices.push({
        application_started_on: '',
        application_ended_on: '',
        unit_price: '',
        currency_code: ''
      })
    },
    deleteSpecialPrice: function (index) {
      this.productSpecialPrices.splice(index, 1)
    },
    submitForm: function () {
      if (! this.factory || ! this.factoryProduct) {
        alert('工場もしくは工場取扱商品が未選択です。')
        return
      }

      if (confirm('データを登録しますか?')) {
        this.$refs.factory_code.value = this.factory.factory_code
        this.$refs.factory_product_sequence_number.value = this.factoryProduct.sequence_number

        this.$refs.form.submit()
      }
    }
  }
}
</script>
