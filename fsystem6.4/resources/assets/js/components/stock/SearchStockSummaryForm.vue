<template>
  <table class="table table-color-bordered">
    <colgroup>
      <col class="col-md-2 col-sm-2 col-xs-2">
      <col class="col-md-4 col-sm-4 col-xs-4">
      <col class="col-md-2 col-sm-2 col-xs-2">
      <col class="col-md-4 col-sm-4 col-xs-4">
    </colgroup>
    <tbody>
      <tr>
        <th>工場<span class="required-mark">*</span></th>
        <td>
          <select id="factory_code" class="form-control" :class="{'has-error': 'factory_code' in errors}" name="factory_code" v-model="factoryCode" v-on:change="getSpeciesWithFactoryCode() + getWarehousesWithFactoryCode()">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th>保管倉庫</th>
        <td>
          <select id="warehouse_code" class="form-control" :class="{'has-error': 'warehouse_code' in errors}" name="warehouse_code" v-model="warehouseCode" :disabled="disabledToSelectWarehouse">
            <option value=""></option>
            <option v-for="w in warehouses" :key="w.warehouse_code" :value="w.warehouse_code">{{ w.warehouse_abbreviation }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>品種</th>
        <td>
          <select id="species_code" class="form-control" :class="{'has-error': 'species_code' in errors}" name="species_code" v-model="speciesCode" :disabled="disabledToSelectSpecies">
            <option value=""></option>
            <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
          </select>
        </td>
        <th>引当状態</th>
        <td>
          <label class="radio-inline">
            <input type="radio" name="allocation_status" :value="null"  v-model="allocationStatus">すべて
          </label>
          <label v-for="(value, label) in allocationStatusList" :key="value" class="radio-inline">
            <input type="radio" name="allocation_status" :value="value" v-model="allocationStatus">{{ label }}
          </label>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    allocationStatusList: {
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
    let allocationStatus = null
    if (typeof this.searchParams.allocation_status !== 'undefined') {
      allocationStatus = this.searchParams.allocation_status
    }
    if (typeof this.oldParams.allocation_status !== 'undefined') {
      allocationStatus = this.oldParams.allocation_status
    }

    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || '',
      warehouses: [],
      warehouseCode: this.oldParams.warehouse_code || this.searchParams.warehouse_code || '',
      speciesList: [],
      speciesCode: this.oldParams.species_code || this.searchParams.species_code || '',
      allocationStatus: allocationStatus
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getWarehousesWithFactoryCode()
      this.warehouseCode = this.oldParams.warehouse_code || this.searchParams.warehouse_code
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code || this.searchParams.species_code
    }
  },
  computed: {
    disabledToSelectWarehouse: function () {
      return this.warehouses.length === 0
    },
    disabledToSelectSpecies: function () {
      return this.speciesList.length === 0
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
          if (this.disabledToSelectWarehouses) {
            alert('保管倉庫が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('保管倉庫の取得に失敗しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
