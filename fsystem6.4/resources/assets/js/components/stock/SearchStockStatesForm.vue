<template>
  <div class="col-md-9 col-sm-9 col-xs-9 col-md-offset-1 col-sm-offset-1">
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
          <th>保管倉庫</th>
          <td>
            <select
              id="warehouse_code"
              class="form-control"
              :class="{'has-error': 'warehouse_code' in errors}"
              name="warehouse_code"
              v-model="warehouseCode"
              :disabled="disabledToSelectWarehouse">
              <option value=""></option>
              <option v-for="w in warehouses" :key="w.warehouse_code" :value="w.warehouse_code">{{ w.warehouse_abbreviation }}</option>
            </select>
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
          <th>履歴</th>
          <td>
            <label class="radio-inline">
              <input name="export_type" type="radio" value="current" v-model="exportType">&nbsp;最新状況
            </label>
            <label class="radio-inline">
              <input name="export_type" type="radio" value="past" v-model="exportType">&nbsp;過去状況
            </label>
          </td>
          <th v-show="exportType === 'past'">日付
            <span class="required-mark">*</span>
          </th>
          <td v-show="exportType === 'past'">
            <datepicker-ja attr-name="stock_date" :date="stockDate" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
const moment = require('moment')

export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    inputGroupList: {
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
      factoryCode: this.oldParams.factory_code || '',
      warehouses: [],
      warehouseCode: this.oldParams.warehouseCode || '',
      speciesList: [],
      speciesCode: this.oldParams.species_code || '',
      packagingStyles: [],
      packagingStyle: {
        number_of_heads: this.oldParams.number_of_heads || '',
        weight_per_number_of_heads: this.oldParams.weight_per_number_of_heads || '',
        input_group: this.oldParams.input_group || ''
      },
      exportType: this.oldParams.export_type || 'current',
      stockDate: this.oldParams.stock_date || moment().subtract(1, 'days').format('YYYY/MM/DD')
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getWarehousesWithFactoryCode()
      this.warehouseCode = this.oldParams.warehouse_code
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code
    }
    if (this.factoryCode && this.speciesCode) {
      this.getPackagingStylesWithFactoryCodeAndSpeciesCode()
      this.packagingStyle = {
        number_of_heads: this.oldParams.number_of_heads,
        weight_per_number_of_heads: this.oldParams.weight_per_number_of_heads,
        input_group: this.oldParams.input_group
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
          if (this.disabledToSelectPackagingStyles) {
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
