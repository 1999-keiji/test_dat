<style scoped>
  table.lacked-table {
    border-left: none;
    border-bottom: none;
  }
  table.lacked-table>tbody>tr>td.lacked-cell {
    border: none;
  }
  table>tbody>tr>th.harvesting-quantity,
  table>tbody>tr>th.forecasted-adjusted-quantity,
  table>tbody>tr>th.adjusted-quantity,
  table>tbody>tr>th.weight-per-head {
    width: 15%;
  }
  table>tbody>tr>th.forecasted-quantity,
  table>tbody>tr>th.actual-quantity {
    width: 20%;
  }
  table>tbody>tr>td>input.form-control {
    width: 80%;
  }

  table.productized-result-details-table>tbody>tr {
    font-size: 16px;
    height: 50px;
    line-height: 1.3333333;
  }
  table.productized-result-details-table>tbody>tr.divider {
    border-top: double;
  }
  table.productized-result-details-table>tbody>tr>td>input.form-control,
  table.productized-result-details-table>tbody>tr>td>span>input.form-control {
    font-size: 16px;
    height: 35px;
  }
</style>

<template>
  <form ref="form" class="form-horizontal basic-form save-data-form" :action="currentUrl" method="POST">
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-5">
        <a class="btn btn-default btn-lg back-button" :href="hrefToIndex">
          <i class="fa fa-arrow-left"></i> 戻る
        </a>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-7">
        <button v-if="canSaveProductizedResult" type="button" class="btn btn-default btn-lg pull-right" @click="saveProductizedResult($event)">
          <i class="fa fa-save"></i> 保存
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed">
          <tbody>
            <tr>
              <th>工場</th>
              <td class="text-left">{{ factory.factory_abbreviation }}</td>
              <th>品種</th>
              <td class="text-left">{{ species.species_name }}</td>
              <th>収穫日</th>
              <td class="text-center">{{ harvestingDate.date }}&nbsp;({{ harvestingDate.day_of_the_week_ja }})</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed lacked-table ">
          <tbody>
            <tr>
              <th class="harvesting-quantity">収穫予定<br>株数</th>
              <th class="forecasted-quantity">調整予定株数<br>(収穫廃棄)<br>(調整その他)</th>
              <th class="forecasted-adjusted-quantity">調整後株数<br>(予定)</th>
              <th class="actual-quantity">調整実績株数<br>(収穫廃棄)<br>(調整その他)</th>
              <th class="adjusted-quantity">調整後株数<br>(実績)</th>
            </tr>
            <tr>
              <td class="text-right" rowspan="2">{{ numberFormat(productizedResult.harvesting_quantity || 0) }}&nbsp;株</td>
              <td class="text-right">{{ numberFormat(productizedResult.forecasted_crop_failure || 0) }}&nbsp;株</td>
              <td class="text-right" rowspan="2">{{ numberFormat(forecastedAdjustedQuantity) }}&nbsp;株</td>
              <td class="text-right form-inline">
                <input
                    class="form-control text-right ime-inactive"
                    :class="{'has-error': 'productized_result.crop_failure' in errors}"
                    type="text"
                    name="productized_result[crop_failure]"
                    v-model.lazy.number="cropFailure"
                    maxlength="9">&nbsp;株
              </td>
              <td class="text-right" rowspan="2">{{ numberFormat(adjustedQuantity) }}&nbsp;株</td>
            </tr>
            <tr>
              <td class="text-right">{{ numberFormat(productizedResult.forecasted_advanced_harvest) }}&nbsp;株</td>
              <td class="text-right form-inline">
                <input
                    class="form-control text-right ime-inactive"
                    :class="{'has-error': 'productized_result.advanced_harvest' in errors}"
                    type="text"
                    name="productized_result[advanced_harvest]"
                    v-model.lazy.number="advancedHarvest"
                    maxlength="9">&nbsp;株
              </td>
            </tr>
            <tr>
              <td class="lacked-cell"></td>
              <th>製品化率<br>(予想)</th>
              <th>製品化重量<br>(予想)</th>
              <th>製品化率<br>(実績)</th>
              <th>製品化重量<br>(実績)</th>
              <th class="weight-per-head">製品化重量<br>(1株)</th>
            </tr>
            <tr>
              <td class="lacked-cell"></td>
              <td class="text-right">{{ numberFormat(productizedResult.product_rate, rateDecimal) }}%</td>
              <td class="text-right">{{ numberFormat(forecastedProductWeight, kgDecimal) }}&nbsp;kg</td>
              <td class="text-right">{{ numberFormat(productRate, rateDecimal) }}&nbsp;%</td>
              <td class="text-right">{{ numberFormat(productWeight, kgDecimal) }}&nbsp;kg</td>
              <td class="text-right">{{ numberFormat(productWeightPerHead) }}&nbsp;g</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered table-more-condensed lacked-table">
          <thead>
            <tr>
              <th class="col-sm-2">合計株数</th>
              <th class="col-sm-2">トリミング</th>
              <th class="col-sm-2">不具合品</th>
              <th class="col-sm-2">パッキング</th>
              <th class="col-sm-2">検査サンプル</th>
              <th class="col-sm-2">パスボックス通過品</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-right">{{ numberFormat(sumOfStock) }}&nbsp;株</td>
              <td class="text-right form-inline">
                <input
                  class="form-control text-right ime-inactive"
                  :class="{'has-error': 'productized_result.triming' in errors}"
                  type="text"
                  name="productized_result[triming]"
                  v-model.lazy.number="triming"
                  maxlength="9">&nbsp;株
              </td>
              <td class="text-right form-inline">
                <input
                  class="form-control text-right ime-inactive"
                  :class="{'has-error': 'productized_result.product_failure' in errors}"
                  type="text"
                  name="productized_result[product_failure]"
                  v-model.lazy.number="productFailure"
                  maxlength="9">&nbsp;株
              </td>
              <td class="text-right form-inline">
                <input
                  class="form-control text-right ime-inactive"
                  :class="{'has-error': 'productized_result.packing' in errors}"
                  type="text"
                  name="productized_result[packing]"
                  v-model.lazy.number="packing"
                  maxlength="9">&nbsp;株
              </td>
              <td class="text-right form-inline">
                <input
                  class="form-control text-right ime-inactive"
                  :class="{'has-error': 'productized_result.sample' in errors}"
                  type="text"
                  name="productized_result[sample]"
                  v-model.lazy.number="sample"
                  maxlength="9">&nbsp;株
              </td>
              <td class="text-right">{{ this.numberFormat(passBoxThroughed) }}&nbsp;株</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered lacked-table productized-result-details-table">
          <thead>
            <tr>
              <th class="col-sm-2" rowspan="2">品種</th>
              <th class="col-sm-2" rowspan="2">商品規格</th>
              <th class="col-sm-2 info">作業指示</th>
              <th class="danger" colspan="2">実績項目</th>
            </tr>
            <tr>
              <th class="info">数量</th>
              <th class="col-sm-2 danger">数量</th>
              <th class="col-sm-2 danger">重量換算</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(prd, idx) in updatedProductizedResultDetails" :key="idx">
              <td v-if="idx === 0" :rowspan="updatedProductizedResultDetails.length">{{ species.species_abbreviation }}</td>
              <td class="text-left">
                {{ numberFormat(prd.weightPerNumberOfHeads) }}g
                {{ inputGroupList[prd.inputGroup] }}
                <input type="hidden" :name="'productized_result_details['+idx+'][number_of_heads]'" :value="prd.numberOfHeads">
                <input type="hidden" :name="'productized_result_details['+idx+'][weight_per_number_of_heads]'" :value="prd.weightPerNumberOfHeads">
                <input type="hidden" :name="'productized_result_details['+idx+'][input_group]'" :value="prd.inputGroup">
              </td>
              <td class="text-right">{{ numberFormat(prd.cropQuantity) }}</td>
              <td>
                <span>
                  <input
                    class="form-control product-quantity text-right ime-inactive"
                    :class="{'has-error': 'productized_result_details.' + idx + '.product_quantity' in errors}"
                    type="text"
                    :name="'productized_result_details['+idx+'][product_quantity]'"
                    maxlength="9"
                    v-model.lazy.number="prd.productQuantity">
                </span>
              </td>
              <td class="text-right">{{ numberFormat(weightPerPackagingStyle(idx), kgDecimal) }}kg</td>
            </tr>
            <tr class="divider">
              <td class="lacked-cell"></td>
              <td><b>合計</b></td>
              <td class="text-right">{{ numberFormat(sumOfCropQuantity) }}</td>
              <td class="text-right">{{ numberFormat(passBoxThroughed) }}</td>
              <td class="text-right">{{ numberFormat(sumOfWeight, kgDecimal) }}kg</td>
            </tr>
            <tr>
              <td class="lacked-cell"></td>
              <td><b>廃棄率</b></td>
              <td class="text-right">{{ numberFormat(rateOfDiscarded, rateDecimal) }}&nbsp;%</td>
              <td><b>廃棄重量</b></td>
              <td class="text-right form-inline">
                <input
                  class="form-control weight-of-discarded text-right ime-inactive"
                  :class="{'has-error': 'productized_result.weight_of_discarded' in errors}"
                  type="text"
                  name='productized_result[weight_of_discarded]'
                  maxlength="6"
                  v-model.lazy.number="weightOfDiscarded">&nbsp;Kg
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <input name="_token" type="hidden" :value="csrf">
    <input name="_method" type="hidden" value="POST">
    <input v-if="! canSaveProductizedResult" id="can-save-data" type="hidden" value="0">
  </form>
</template>

<script>
const number_format = require('locutus/php/strings/number_format')

export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    species: {
      type: Object,
      required: true
    },
    harvestingDate: {
      type: Object,
      required: true
    },
    productizedResult: {
      type: Object,
      required: true
    },
    productizedResultDetails: {
      type: Array,
      required: true
    },
    speciesAverageWeight: {
      type: Number,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    hrefToIndex: {
      type: String,
      required: true
    },
    canSaveProductizedResult: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    let productFailure = this.productizedResult.product_failure || 0,
      triming = this.productizedResult.triming || 0,
      packing = this.productizedResult.packing || 0,
      cropFailure = this.productizedResult.crop_failure || 0,
      sample = this.productizedResult.sample || 0,
      advancedHarvest = this.productizedResult.advanced_harvest || 0,
      weightOfDiscarded = this.productizedResult.weight_of_discarded || 0
    if (weightOfDiscarded !== 0) {
      weightOfDiscarded = weightOfDiscarded / 1000
    }

    if (this.oldParams.productized_result) {
      cropFailure = this.oldParams.productized_result.crop_failure
      triming = this.oldParams.productized_result.triming
      productFailure = this.oldParams.productized_result.product_failure
      packing = this.oldParams.productized_result.packing
      sample = this.oldParams.productized_result.sample
      advancedHarvest = this.oldParams.productized_result.advanced_harvest
      weightOfDiscarded = this.oldParams.productized_result.weight_of_discarded
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      currentUrl: location.href.split('?')[0],
      cropFailure: cropFailure,
      productFailure: productFailure,
      triming: triming,
      packing: packing,
      sample: sample,
      advancedHarvest: advancedHarvest,
      weightOfDiscarded: weightOfDiscarded,
      updatedProductizedResultDetails: [],
      kgDecimal: 1,
      rateDecimal: 2,
    }
  },
  created: function () {
    for (const [idx, prd] of this.productizedResultDetails.entries()) {
      let productQuantity = prd.product_quantity
      if (this.oldParams.productized_result_details) {
        productQuantity = this.oldParams.productized_result_details[idx].product_quantity || ''
      }

      this.updatedProductizedResultDetails.push({
        numberOfHeads: parseFloat(prd.number_of_heads),
        weightPerNumberOfHeads: prd.weight_per_number_of_heads,
        inputGroup: prd.input_group,
        cropQuantity: prd.crop_number,
        productQuantity: productQuantity
      })
    }
  },
  computed: {
    forecastedAdjustedQuantity: function () {
      return parseInt(this.productizedResult.harvesting_quantity) +
        parseInt(this.productizedResult.forecasted_crop_failure || 0) +
        parseInt(this.productizedResult.forecasted_advanced_harvest || 0)
    },
    forecastedProductWeight: function () {
      return this.forecastedAdjustedQuantity *
        this.speciesAverageWeight *
        ((this.productizedResult.product_rate || 0) / 100) / 1000
    },
    adjustedQuantity: function () {
      return parseInt(this.productizedResult.harvesting_quantity) +
        this.parseInputtedNumber(this.cropFailure) +
        this.parseInputtedNumber(this.advancedHarvest)
    },
    productRate: function () {
      if (this.adjustedQuantity === 0) {
        return 0
      }

      return this.passBoxThroughed / this.adjustedQuantity * 100
    },
    productWeight: function () {
      return this.passBoxThroughed * this.speciesAverageWeight / 1000
    },
    productWeightPerHead: function () {
      return Math.floor(this.speciesAverageWeight * (this.productRate / 100))
    },
    sumOfStock: function () {
      return this.passBoxThroughed +
        (this.parseInputtedNumber(this.cropFailure) * -1) +
        this.parseInputtedNumber(this.advancedHarvest) +
        this.parseInputtedNumber(this.triming) +
        this.parseInputtedNumber(this.productFailure) +
        this.parseInputtedNumber(this.packing) +
        this.parseInputtedNumber(this.sample)
    },
    passBoxThroughed: function () {
      return this.updatedProductizedResultDetails.reduce((sum, prd) => {
        return sum += Math.floor(this.parseInputtedNumber(prd.productQuantity) * prd.numberOfHeads)
      }, 0)
    },
    rateOfDiscarded: function () {
      const weightOfDiscarded = parseFloat(this.weightOfDiscarded)
      if (this.sumOfWeight === 0 && weightOfDiscarded === 0) {
        return 0
      }

      return weightOfDiscarded /
        (weightOfDiscarded + this.sumOfWeight) *
        100
    },
    sumOfCropQuantity: function () {
      return this.updatedProductizedResultDetails.reduce((sum, prd) => {
        return sum += Math.floor(this.parseInputtedNumber(prd.cropQuantity) * prd.numberOfHeads)
      }, 0)
    },
    sumOfWeight: function () {
      let sumOfWeight = 0
      for (const idx of this.updatedProductizedResultDetails.keys()) {
        sumOfWeight += this.weightPerPackagingStyle(idx)
      }

      return sumOfWeight
    },
  },
  methods: {
    weightPerPackagingStyle: function (idx) {
      const productizedResultDetail = this.updatedProductizedResultDetails[idx]
      return productizedResultDetail.numberOfHeads *
        this.parseInputtedNumber(productizedResultDetail.productQuantity) *
        this.speciesAverageWeight /
        1000
    },
    numberFormat: function (value, decimals = 0) {
      return number_format(
        String(value).replace(/[Ａ-Ｚａ-ｚ０-９]/g, (value) => {return String.fromCharCode(value.charCodeAt(0)-0xFEE0)}),
        decimals
      )
    },
    parseInputtedNumber: function (value) {
      const num = String(value).replace(/[Ａ-Ｚａ-ｚ０-９]/g, (value) => {return String.fromCharCode(value.charCodeAt(0)-0xFEE0)})
        .replace(/,/g, '')

      return isNaN(parseInt(num)) ? 0 : parseInt(num)
    },
    saveProductizedResult: function (event) {
      if (confirm('データを保存しますか？')) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
