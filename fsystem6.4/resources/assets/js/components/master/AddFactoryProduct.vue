<style scoped>
  #add-row {
    margin-top: -2em;
    margin-bottom: 1em;
  }
  #button-space {
    padding-right: 30px;
  }
  .has-suffix {
    width: 70%;
    display: inline-block;
  }
</style>

<template>
  <div class="row row-pattern">
    <form ref="form" class="form-horizontal basic-form save-data-form" :action="actionOfSaveFactoryProduct" method="POST">
      <input name="factory_code" type="hidden" :value="factory.factory_code">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="row form-group">
            <div class="col-md-9 col-sm-9 col-xs-9">
            </div>
            <div id="button-space" class="col-md-3 col-sm-3 col-xs-3">
              <button class="btn btn-default pull-right btn-lg" type="button" @click="saveFactoryProduct($event)">
                <i class="fa fa-save"></i> 保存
              </button>
            </div>
          </div>
        </div>

        <div class="col-md-9 col-sm-9">
          <div class="row form-group">
            <label for="species_code" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              品種
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-4 col-sm-4">
              <select
                id="species_code"
                class="form-control"
                name="species_code"
                v-model="speciesCode"
                required
                @change="getProducts">
                <option value=""></option>
                <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
              </select>
            </div>
          </div>

          <div class="row form-group">
            <label for="product_code" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              商品
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-4 col-sm-4">
              <select
                id="product_code"
                class="form-control"
                :class="{'has-error': 'product_code' in errors}"
                name="product_code"
                v-model="productCode"
                required
                :disabled="disabledToSelectProduct"
                @change="getProductPrices">
                <option value=""></option>
                <option v-for="p in products" :key="p.product_code" :value="p.product_code">{{ p.product_code }}:&nbsp;{{ p.product_name }}</option>
              </select>
            </div>
          </div>

          <div class="row form-group">
            <label for="factory_product_name" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              工場商品名
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-4 col-sm-4">
              <input
                class="form-control text-left ime-active"
                :class="{'has-error': 'factory_product_name' in errors}"
                type="text"
                maxlength="50"
                name="factory_product_name"
                v-model="factoryProductName">
            </div>
          </div>

          <div class="row form-group">
            <label for="factory_product_abbreviation" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              工場商品名略称
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-4 col-sm-4">
              <input
                class="form-control text-left ime-active"
                :class="{'has-error': 'factory_product_abbreviation' in errors}"
                type="text"
                maxlength="15"
                name="factory_product_abbreviation"
                v-model="factoryProductAbbreviation">
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              商品規格
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-2 col-sm-2">
              <input
                class="form-control text-right has-suffix ime-inactive"
                :class="{'has-error': 'number_of_heads' in errors}"
                type="text"
                name="number_of_heads"
                v-model="numberOfHeads">&nbsp;&nbsp;株
            </div>
            <div class="col-md-2 col-sm-2">
              <input
                class="form-control text-right has-suffix ime-inactive"
                :class="{'has-error': 'weight_per_number_of_heads' in errors}"
                type="text"
                name="weight_per_number_of_heads"
                v-model="weightPerNumberOfHeads">&nbsp;&nbsp;g
            </div>
            <div class="col-md-4 col-sm-4">
              <select
                class="form-control"
                :class="{'has-error': 'input_group' in errors}"
                name="input_group"
                v-model="inputGroup">
                <option value=""></option>
                <option v-for="(label, value) in inputGroupList" :key="value" :value="value">{{ label }}</option>
              </select>
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              ケース入数
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-2 col-sm-2">
              <input
                class="form-control text-right ime-inactive"
                :class="{'has-error': 'number_of_cases' in errors}"
                type="text"
                name="number_of_cases"
                v-model="numberOfCases">
            </div>
            <div class="col-md-2 col-sm-2">
              <select
                class="form-control"
                :class="{'has-error': 'unit' in errors}"
                name="unit"
                v-model="unit">
                <option value=""></option>
                <option v-for="u in unitList" :key="u" :value="u">{{ u }}</option>
              </select>
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              商品重量
            </label>
            <div class="col-md-7 col-sm-7">
              <span class="shown_label">{{ productWeight }}</span>&nbsp;&nbsp;g
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              ニコイチ配送可否
            </label>
            <div class="col-md-7 col-sm-7">
              <input name="can_be_transported_double" type="checkbox" value="1" v-model="canBeTransportedDouble">
            </div>
          </div>

          <div class="row form-group">
            <label for="remark" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              備考
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="remark"
                class="form-control ime-active"
                :class="{'has-error': 'remark' in errors}"
                maxlength="50"
                name="remark"
                :value="oldParams.remark || null"
                type="text">
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              工場商品価格
            </label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <table class="table table-color-bordered table-more-condensed">
                <thead>
                  <tr>
                    <th>削除</th>
                    <th>適用開始日</th>
                    <th>単価</th>
                    <th>原価</th>
                    <th>通貨コード</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(fpp, index) in factoryProductsPrices" :key="index">
                    <td>
                      <button class="btn btn-danger" type="button" @click="deleteFactoryProductPrice(index)">削除</button>
                    </td>
                    <td>
                      <datepicker-ja
                        :attr-name="'factory_product_prices['+ index + '][application_started_on]'"
                        :date="fpp.application_started_on">
                      </datepicker-ja>
                    </td>
                    <td>
                      <input-number-with-formatter
                        :attr-name="'factory_product_prices['+ index + '][unit_price]'"
                        :value="fpp.unit_price"
                        :max-length="unitPrice.max_length"
                        :decimals="unitPrice.decimals"
                        :help-text="unitPrice.help_text">
                      </input-number-with-formatter>
                    </td>
                    <td>
                      <input-number-with-formatter
                        :attr-name="'factory_product_prices['+ index + '][cost]'"
                        :value="fpp.cost"
                        :max-length="cost.max_length"
                        :decimals="cost.decimals"
                        :help-text="cost.help_text">
                      </input-number-with-formatter>
                    </td>
                    <td>
                      <select
                        class="form-control"
                        :name="'factory_product_prices['+ index + '][currency_code]'">
                        <option value=""></option>
                        <option v-for="c in currencies" :key="c.currency_code" :value="c.currency_code" :selected="c.currency_code === fpp.currency_code">{{ c.currency_code }}</option>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1">
              <button id="add-row" type="button" class="btn btn-default pull-right" :disabled="disabledToAdd" @click="addFactoryProductPrice">
                <i class="fa fa-plus"></i> 追加
              </button>
            </div>
          </div>
          <input name="_token" type="hidden" :value="csrf">
          <input name="_method" type="hidden" value="POST">
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    actionOfSaveFactoryProduct: {
      type: String,
      required: true
    },
    factory: {
      type: Object,
      required: true
    },
    speciesList: {
      type: Array,
      required: true
    },
    currencies: {
      type: Array,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    unitList: {
      type: Array,
      required: true
    },
    unitPrice: {
      type: Object,
      required: true
    },
    cost: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    }
  },
  data: function () {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      speciesCode: this.oldParams.species_code || null,
      products: [],
      productCode: this.oldParams.product_code || null,
      factoryProductName: this.oldParams.factory_product_name || null,
      factoryProductAbbreviation: this.oldParams.factory_product_abbreviation || null,
      numberOfHeads: this.oldParams.number_of_heads || null,
      weightPerNumberOfHeads: this.oldParams.weight_per_number_of_heads || null,
      inputGroup: this.oldParams.input_group || null,
      numberOfCases: this.oldParams.number_of_cases || null,
      unit: this.oldParams.unit || null,
      canBeTransportedDouble: this.oldParams.can_be_transported_double || 0,
      factoryProductsPrices: [],
      disabledToAdd: false
    }
  },
  created: function () {
    if (this.speciesCode) {
      this.getProducts()
      this.productCode = this.oldParams.product_code
    }

    if (this.errors.length !== 0) {
      this.factoryProductsPrices = this.oldParams.factory_product_prices || []
    }
  },
  computed: {
    disabledToSelectProduct: function () {
      return this.products.length === 0
    },
    productWeight: function () {
      if (this.weightPerNumberOfHeads && this.numberOfCases) {
        const number_format = require('locutus/php/strings/number_format')
        return number_format(this.weightPerNumberOfHeads * this.numberOfCases)
      }
    }
  },
  methods: {
    getProducts: function () {
      this.productCode = null
      this.products = this.factoryProductsPrices = []
      if (! this.speciesCode) {
        return
      }

      axios.get('/api/get-products', {
        params: {
          species_code: this.speciesCode
        }
      })
        .then(response => {
          this.products = response.data
          if (this.products.length === 0) {
            alert('商品が未登録の品種です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    getProductPrices: function () {
      this.factoryProductsPrices = []
      if (! this.productCode) {
        return
      }

      this.disabledToAdd = true
      axios.get('/api/get-product-prices', {
        params: {
          factory_code: this.factory.factory_code,
          product_code: this.productCode
        }
      })
        .then(response => {
          this.factoryProductsPrices = response.data
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
        .finally(() => {
          this.disabledToAdd = false
        })
    },
    addFactoryProductPrice: function () {
      this.factoryProductsPrices.push({
        application_started_on: null,
        unit_price: null,
        cost: null,
        currency_code: null
      })
    },
    deleteFactoryProductPrice: function (index) {
      this.factoryProductsPrices.splice(index, 1)
    },
    saveFactoryProduct: function (event) {
      if (confirm('データを登録しますか?')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
