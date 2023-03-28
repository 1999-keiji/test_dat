<template>
  <div class="col-md-6 col-sm-8 col-xs-10 col-md-offset-1 col-sm-offset-1">
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
              v-on:change="getWarehousesWithFactoryCode">
              <option value=""></option>
              <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
            </select>
          </td>
          <th>保管倉庫<span class="required-mark">*</span></th>
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
          <th>棚卸年月<span class="required-mark">*</span></th>
          <td>
            <year-monthpicker-ja name="stocktaking_month" :value="stocktakingMonth" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
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
    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || '',
      warehouses: [],
      warehouseCode: this.oldParams.warehouse_code || this.searchParams.warehouse_code || '',
      stocktakingMonth: this.oldParams.stocktaking_month || this.searchParams.stocktaking_month || ''
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getWarehousesWithFactoryCode()
      this.warehouseCode = this.oldParams.warehouse_code || this.searchParams.warehouse_code
    }
  },
  computed: {
    disabledToSelectWarehouse: function () {
      return this.warehouses.length === 0
    }
  },
  methods: {
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
    }
  }
}
</script>
