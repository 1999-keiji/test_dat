<style scoped>
  .adjust-form {
    margin-top: 3em;
  }
  .box-with-caption {
    border: thin solid;
  }
  .before-adjusting {
    margin-left: 3em;
  }
  .after-adjusting {
    margin-right: 3em;
  }
  label.caption {
    background-color: #D7E4BD;
    border: solid 1px #4F6228;
    font-size: 1.3em;
    margin-bottom: 1em;
    padding: 3px 30px;
  }
  i.fa.fa-arrow-right {
    font-size: 5em;
    margin-top: 0.5em;
    margin-left: 0.2em;
  }
  .form-horizontal .radio-inline {
    padding-top: 0;
  }
</style>

<template>
  <div class="row adjust-form">
    <div class="col-md-5 col-sm-5">
      <div class="row box-with-caption before-adjusting">
        <label class="caption">調整前</label>
        <div class="row form-group packaging-style">
          <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">商品規格</label>
          <div class="col-md-8 col-sm-8">
            <span class="shown_label">
              {{ stock.number_of_heads }}株
              {{ stock.weight_per_number_of_heads }}g
              {{ inputGroupList[stock.input_group] }}
            </span>
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">数量</label>
          <div class="col-md-8 col-sm-8">
            <span class="shown_label">{{ stock.stock_quantity - stock.disposal_quantity | formatNumber }}</span>
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">状態</label>
          <div class="col-md-8 col-sm-8">
            <span class="shown_label">
              {{ stockStatusList[stock.stock_status] }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-1 col-sm-1">
      <i class="fa fa-arrow-right"></i>
    </div>
    <div class="col-md-6 col-sm-6">
      <div class="row box-with-caption after-adjusting">
        <label class="caption">調整後</label>
        <div class="row form-group">
          <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
            調整方法<span class="required-mark">*</span>
          </label>
          <div class="col-md-9 col-sm-9">
            <label class="radio-inline">
              <input type="radio" name="adjusting_type" value="replace" v-model="adjustingType">商品規格の置き換え
            </label>
            <label class="radio-inline">
              <input type="radio" name="adjusting_type" value="separate" v-model="adjustingType">在庫を分離
            </label>
            <label class="radio-inline">
              <input type="radio" name="adjusting_type" value="change" v-model="adjustingType">商品状態の変更
            </label>
          </div>
        </div>
        <div v-show="adjustingType === 'replace'" class="row form-group">
          <label for="packaging_style" class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
            商品規格<span class="required-mark">*</span>
          </label>
          <div class="col-md-5 col-sm-6">
            <select
              id="packaging_style"
              class="form-control"
              v-model="packagingStyle"
              :disabled="disabledToSelectPackagingStyle"
              @change="stockQuantity = adjustedStockQuantity">
              <option v-for="(ps, index) in selectablePackagingStyles" :key="index" :value="ps">
                {{ ps.number_of_heads }}株
                {{ ps.weight_per_number_of_heads }}g
                {{ inputGroupList[ps.input_group] }}
              </option>
            </select>
            <input type="hidden" name="number_of_heads" :value="packagingStyle.number_of_heads">
            <input type="hidden" name="weight_per_number_of_heads" :value="packagingStyle.weight_per_number_of_heads">
            <input type="hidden" name="input_group" :value="packagingStyle.input_group">
          </div>
        </div>
        <div v-show="adjustingType !== 'change'" class="row form-group">
          <label for="stock_quantity" class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
            数量<span class="required-mark">*</span>
          </label>
          <div class="col-md-3 col-sm-4">
            <input-number-with-formatter
              :value="stockQuantity | formatNumber"
              attr-name="stock_quantity"
              :has-error="'stock_quantity' in errors"
              :max-length="9"
              @update-value="updateStockQuantity">
            </input-number-with-formatter>
          </div>
        </div>
        <div v-show="adjustingType === 'change' " class="row form-group">
          <label class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-1 control-label required">
            状態<span class="required-mark">*</span>
          </label>
          <div class="col-md-4 col-sm-5">
            <label v-for="(label, value) in stockStatusList" :key="value" class="radio-inline">
              <input type="radio" name="stock_status" :value="value" v-model="stockStatus" disabled>{{ label }}
            </label>
            <input type="hidden" name="stock_status" :value="stockStatus">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stock: {
      type: Object,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    stockStatusList: {
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
    let adjustingType = 'replace'
    if (this.oldParams.adjusting_type) {
      adjustingType = this.oldParams.adjusting_type
    }

    const stockStatus = !!this.stock.stock_status
    return {
      adjustingType: adjustingType,
      packagingStyles:[],
      packagingStyle: {
        number_of_heads: this.oldParams.number_of_heads || '',
        weight_per_number_of_heads: this.oldParams.weight_per_number_of_heads || '',
        input_group: this.oldParams.input_group || ''
      },
      stockQuantity: this.oldParams.stock_quantity || '',
      stockStatus: Number(!stockStatus)
    }
  },
  computed: {
    selectablePackagingStyles: function () {
      return this.packagingStyles.filter((ps) => {
        return ! (this.stock.number_of_heads === ps.number_of_heads &&
          this.stock.weight_per_number_of_heads === ps.weight_per_number_of_heads &&
          this.stock.input_group === ps.input_group)
      })
    },
    disabledToSelectPackagingStyle: function () {
      return this.selectablePackagingStyles.length === 0
    },
    stockWeight: function () {
      return this.stock.weight_per_number_of_heads * (this.stock.stock_quantity - this.stock.disposal_quantity)
    },
    adjustedStockQuantity: function () {
      if (! this.packagingStyle.weight_per_number_of_heads) {
        return ''
      }

      const stockQuantity = this.stockWeight / this.packagingStyle.weight_per_number_of_heads
      return Math.floor(stockQuantity)
    }
  },
  filters: {
    formatNumber: function (number) {
      const number_format = require('locutus/php/strings/number_format')
      return number_format(number)
    }
  },
  created: function () {
    axios.get('/api/get-packaging-styles-with-factory-code-and-species-code', {
      params: {
        factory_code: this.stock.factory_code,
        species_code : this.stock.species_code
      }
    })
      .then(response => {
        this.packagingStyles = response.data
        if (this.disabledToSelectPackagingStyle) {
          alert('調整可能な商品規格がありません。')
        }

        if (this.adjustingType === 'replace' && ! this.packagingStyle.number_of_heads) {
          this.packagingStyle = this.selectablePackagingStyles[0]
          this.stockQuantity = this.adjustedStockQuantity
        }
      })
      .catch(() => {
        alert('商品規格の取得に失敗しました。しばらくお待ちください。')
      })
  },
  watch: {
    adjustingType: function (value) {
      if (value === 'replace') {
        this.packagingStyle = this.selectablePackagingStyles[0]
        this.stockQuantity = this.adjustedStockQuantity
      }

      if (value === 'separate') {
        this.stockQuantity = ''
      }
    }
  },
  methods: {
    updateStockQuantity: function (stockQuantity) {
      this.stockQuantity = stockQuantity
      if (this.adjustingType !== 'replace') {
        return
      }

      if (this.stockWeight < stockQuantity * this.packagingStyle.weight_per_number_of_heads) {
        alert('調整後の重量が、調整前の重量を上回っています。')
      }
    }
  }
}
</script>
