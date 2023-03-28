<style scoped>
button.btn.btn-lg,
a.btn.btn-lg {
  margin-top: 5px;
  text-align: left;
  width: 11em;
}

.radio-inline {
  padding-top: 0;
}
</style>

<template>
  <div class="row">
    <div class="col-md-9 col-sm-9 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <form id="search-disposed-stocks-form" class="form-horizontal basic-form" method="POST">
        <table class="table table-color-bordered search-stocks-form">
          <tbody>
            <tr>
              <th>工場<span class="required-mark">*</span></th>
              <td>
                <select
                  id="factory_code"
                  class="form-control"
                  :class="{'has-error': 'factory_code' in errors}"
                  name="factory_code"
                  v-model="factoryCode"
                  v-on:change="getSpeciesWithFactoryCode() + getWarehousesWithFactoryCode()">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
                </select>
              </td>
              <th>保管倉庫<span class="required-mark">*</span></th>
              <td>
                <select id="warehouse_code" class="form-control" :class="{'has-error': 'warehouse_code' in errors}" name="warehouse_code" v-model="warehouseCode" :disabled="disabledToSelectWarehouse">
                  <option value=""></option>
                  <option v-for="w in warehouses" :key="w.warehouse_code" :value="w.warehouse_code">{{ w.warehouse_abbreviation }}</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>収穫年月<span class="required-mark">*</span></th>
              <td>
                <year-monthpicker-ja name="harvesting_month" :value="harvestingMonth" />
              </td>
              <th>状態</th>
              <td>
                <label class="radio-inline">
                  <input type="radio" name="stock_status" :value="null" v-model="stockStatus">すべて
                </label>
                <label v-for="(value, label) in stockStatusList" :key="value" class="radio-inline">
                  <input type="radio" name="stock_status" :value="value" v-model="stockStatus">{{ label }}
                </label>
              </td>
            </tr>
            <tr>
              <th>品種</th>
              <td>
                <select
                  id="species_code"
                  class="form-control"
                  :class="{'has-error': 'species_code' in errors}"
                  name="species_code"
                  v-model="speciesCode"
                  :disabled="disabledToSelectSpecies"
                  v-on:change="getPackagingStylesWithFactoryCodeAndSpeciesCode">
                  <option value=""></option>
                  <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
                </select>
              </td>
              <th>商品規格</th>
              <td>
                <select id="packagingStyles" class="form-control" :disabled="disabledToSelectPackagingStyle" v-model="packagingStyle">
                  <option value=""></option>
                  <option v-for="(ps, index) in packagingStyles" :key="index" :value="ps">
                    {{ ps.number_of_heads }}株
                    {{ ps.weight_per_number_of_heads }}g
                    {{ inputGroupList[ps.input_group] }}
                  </option>
                </select>
                <input type="hidden" name="number_of_heads" :value="packagingStyle.number_of_heads">
                <input type="hidden" name="weight_per_number_of_heads" :value="packagingStyle.weight_per_number_of_heads">
                <input type="hidden" name="input_group" :value="packagingStyle.input_group">
              </td>
            </tr>
            <tr>
              <th>廃棄状態</th>
              <td>
                <label class="radio-inline">
                  <input type="radio" name="disposal_status" :value="null" v-model="disposalStatus">すべて
                </label>
                <label v-for="(value, label) in disposalStatusList" :key="value" class="radio-inline">
                  <input type="radio" name="disposal_status" :value="value" v-model="disposalStatus">{{ label }}
                </label>
              </td>
            </tr>
          </tbody>
        </table>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="POST">
      </form>
    </div>
    <div class="col-md-2 col-sm-2 col-xs-2">
      <button class="btn btn-lg btn-default pull-left" type="button" @click="searchDisposedStocks">
        <i class="fa fa-search"></i>&nbsp;検索
      </button>
      <a class="btn btn-lg btn-default pull-left" @click="exportDisposedStocks">
        <i class="fa fa-download"></i>&nbsp;Excelダウンロード
      </a>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    searchDisposedStocksAction: {
      type: String,
      required: true
    },
    exportDisposedStocksAction: {
      type: String,
      required: true
    },
    factories: {
      type: Array,
      required: true
    },
    stockStatusList: {
      type: Object,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    disposalStatusList: {
      type: Object,
      required: true
    },
    searchParams: {
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
    let stockStatus = null
    if (typeof this.searchParams.stock_status !== 'undefined') {
      stockStatus = this.searchParams.stock_status
    }
    if (typeof  this.oldParams.stock_status !== 'undefined') {
      stockStatus = this.oldParams.stock_status
    }

    let disposalStatus = null
    if (typeof this.searchParams.disposal_status !== 'undefined') {
      disposalStatus = this.searchParams.disposal_status
    }
    if (typeof this.oldParams.disposal_status !== 'undefined') {
      disposalStatus = this.oldParams.disposal_status
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || '',
      warehouses: [],
      warehouseCode: this.oldParams.warehouseCode || this.searchParams.warehouseCode || '',
      harvestingMonth: this.oldParams.harvesting_month || this.searchParams.harvesting_month || '',
      stockStatus: stockStatus,
      speciesList: [],
      speciesCode: this.oldParams.species_code || this.searchParams.species_code || '',
      packagingStyles:[],
      packagingStyle: {
        number_of_heads: this.oldParams.number_of_heads || this.searchParams.number_of_heads || '',
        weight_per_number_of_heads: this.oldParams.weight_per_number_of_heads || this.searchParams.weight_per_number_of_heads || '',
        input_group: this.oldParams.input_group || this.searchParams.input_group || ''
      },
      disposalStatus: disposalStatus
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getWarehousesWithFactoryCode()
      this.warehouseCode = this.oldParams.warehouse_code || this.searchParams.warehouse_code
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code || this.searchParams.species_code
    }
    if (this.factoryCode && this.speciesCode) {
      this.getPackagingStylesWithFactoryCodeAndSpeciesCode()
      this.packagingStyle = {
        number_of_heads: this.oldParams.number_of_heads || this.searchParams.number_of_heads || '',
        weight_per_number_of_heads: this.oldParams.weight_per_number_of_heads || this.searchParams.weight_per_number_of_heads || '',
        input_group: this.oldParams.input_group || this.searchParams.input_group || ''
      }
    }
  },
  computed: {
    disabledToSelectSpecies: function () {
      return this.speciesList.length === 0
    },
    disabledToSelectWarehouse: function () {
      return this.warehouses.length === 0
    },
    disabledToSelectPackagingStyle: function () {
      return this.packagingStyles.length === 0
    }
  },
  methods: {
    searchDisposedStocks: function () {
      document.getElementById('search-disposed-stocks-form').action = this.searchDisposedStocksAction
      document.getElementById('search-disposed-stocks-form').submit()
    },
    exportDisposedStocks: function () {
      document.getElementById('search-disposed-stocks-form').action = this.exportDisposedStocksAction
      document.getElementById('search-disposed-stocks-form').submit()
    },
    getWarehousesWithFactoryCode: function () {
      this.warehouseCode = ''
      this.warehouses = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-warehouses-with-factory-code', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.warehouses = response.data
          if (this.disabledToSelectWarehouse) {
            alert('保管倉庫が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('保管倉庫の取得に失敗しました。しばらくお待ちください。')
        })
    },
    getSpeciesWithFactoryCode: function () {
      this.speciesCode = ''
      this.speciesList = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-species-with-factory-code', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.speciesList = response.data
          if (this.disabledToSelectSpecies) {
            alert('工場取扱品種が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('品種の取得に失敗しました。しばらくお待ちください。')
        })
    },
    getPackagingStylesWithFactoryCodeAndSpeciesCode: function () {
      this.packagingStyles = []
      this.packagingStyle = {}
      if (! this.factoryCode && ! this.speciesCode) {
        return
      }

      axios.get('/api/get-packaging-styles-with-factory-code-and-species-code', {
        params: {
          factory_code: this.factoryCode,
          species_code : this.speciesCode
        }
      })
        .then(response => {
          this.packagingStyles = response.data
          if (this.disabledToSelectPackagingStyle) {
            alert('商品規格が未登録の品種です。')
          }
        })
        .catch(() => {
          alert('商品規格の取得に失敗しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
