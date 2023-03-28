<style scoped>
  #add-row {
    margin-top: -2em;
    margin-bottom: 1em;
  }
  #button-space {
    padding-right: 30px;
  }
  .has-suffix {
    display: inline-block;
    width: 70%;
  }
</style>

<template>
  <div class="row row-pattern">
    <form ref="form" class="form-horizontal basic-form save-data-form" :action="actionOfUpdateFactoryProduct" method="POST">
      <div class="row">
        <div class="col-md-12 col-sm-12">
          <div class="row form-group">
            <div v-if="canSaveFactory" id="button-space" class="col-sm-3 col-xs-3 col-sm-offset-9 col-xs-offset-6">
              <delete-form
                v-if="isDeletable"
                :route-action="actionOfDeleteFactoryProduct"
                :is-large-button="true">
              </delete-form>
              <button class="btn btn-default pull-right btn-lg" type="button" @click="saveFactoryProduct($event)">
                <i class="fa fa-save"></i> 保存
              </button>
            </div>
          </div>
        </div>

        <div class="col-md-9 col-sm-9">
          <div class="row form-group">
            <label for="species_code" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              品種
            </label>
            <div class="col-md-4 col-sm-4">
              <span class="shown_label">{{ species.species_name }}</span>
            </div>
          </div>

          <div class="row form-group">
            <label for="product_code" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label">
              商品
            </label>
            <div class="col-md-4 col-sm-4">
              <span class="shown_label">{{ product.product_code }}:&nbsp;{{ product.product_name }}</span>
            </div>
          </div>

          <div class="row form-group">
            <label for="factory_product_name" class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              工場商品名
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-4 col-sm-4">
              <input
                id="factory_product_name"
                class="form-control text-left ime-active"
                :class="{'has-error': 'factory_product_name' in errors}"
                type="text"
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
                id="factory_product_abbreviation"
                class="form-control text-left ime-active"
                :class="{'has-error': 'factory_product_abbreviation' in errors}"
                type="text"
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
                <option v-for="ul in unitList" :key="ul" :value="ul" :selected="ul === unit">{{ ul }}</option>
              </select>
            </div>
          </div>

          <div class="row form-group">
            <label class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 control-label required">
              商品重量
              <span class="required-mark">*</span>
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
                :value="oldParams.remark || factoryProduct.remark"
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
                    <th v-if="canSaveFactory">削除</th>
                    <th>適用開始日</th>
                    <th>単価</th>
                    <th>原価</th>
                    <th>通貨コード</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(fpp, index) in factoryProductPrices" :key="index">
                    <td v-if="canSaveFactory">
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
              <button v-if="canSaveFactory" id="add-row" type="button" class="btn btn-default pull-right" :disabled="disabledToAdd" @click="addFactoryProductPrice">
                <i class="fa fa-plus"></i> 追加
              </button>
            </div>
          </div>
          <input name="_token" type="hidden" :value="csrf">
          <input name="_method" type="hidden" value="PATCH">
          <input v-if="! canSaveFactory" id="can-save-data" type="hidden" value="0">
        </div>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    actionOfUpdateFactoryProduct: {
      type: String,
      required: true
    },
    actionOfDeleteFactoryProduct: {
      type: String,
      required: true
    },
    factoryProduct: {
      type: Object,
      required: true
    },
    isDeletable: {
      type: Boolean,
      required: true
    },
    species: {
      type: Object,
      required: true
    },
    product: {
      type: Object,
      required: true
    },
    currentFactoryProductPrices: {
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
    },
    canSaveFactory: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    let canBeTransportedDouble = this.factoryProduct.can_be_transported_double
    if (this.errors.length !== 0) {
      canBeTransportedDouble = this.oldParams.can_be_transported_double || false
    }

    const _ = require('lodash')
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      factoryProductName: this.oldParams.factory_product_name || this.factoryProduct.factory_product_name,
      factoryProductAbbreviation: this.oldParams.factory_product_abbreviation || this.factoryProduct.factory_product_abbreviation,
      numberOfHeads: this.oldParams.number_of_heads || this.factoryProduct.number_of_heads,
      weightPerNumberOfHeads: this.oldParams.weight_per_number_of_heads || this.factoryProduct.weight_per_number_of_heads,
      inputGroup: this.oldParams.input_group || this.factoryProduct.input_group,
      numberOfCases: this.oldParams.number_of_cases || this.factoryProduct.number_of_cases,
      unit: this.oldParams.unit || this.factoryProduct.unit,
      canBeTransportedDouble,
      disabledToAdd: false,
      factoryProductPrices: _.cloneDeep(this.currentFactoryProductPrices)
    }
  },
  created: function () {
    if (this.errors.length !== 0) {
      this.factoryProductPrices = this.oldParams.factory_product_prices
    }
  },
  computed: {
    productWeight: function () {
      if (this.weightPerNumberOfHeads && this.numberOfCases) {
        const number_format = require('locutus/php/strings/number_format')
        return number_format(this.weightPerNumberOfHeads * this.numberOfCases)
      }
    }
  },
  methods: {
    addFactoryProductPrice: function () {
      this.factoryProductPrices.push({
        application_started_on: null,
        unit_price: null,
        cost: null,
        currency_code: null
      })
    },
    deleteFactoryProductPrice: function (index) {
      this.factoryProductPrices.splice(index, 1)
    },
    saveFactoryProduct: function () {
      if (confirm('データを登録しますか?')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
