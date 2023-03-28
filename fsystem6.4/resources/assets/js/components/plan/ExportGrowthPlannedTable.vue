<style scoped>
  select[name="date_range"] {
    width: 80px;
  }
</style>

<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th class="col-md-2 col-sm-2 col-xs-2">
          工場<span class="required-mark">*</span>
        </th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select class="form-control" name="factory_code" v-model="factoryCode" @change="getFactorySpecies" required>
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th class="col-md-2 col-sm-2 col-xs-2">工場品種</th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select class="form-control" name="factory_species_code" v-model="factorySpeciesCode" :disabled="disabledToSelectFactorySpecies">
            <option value=""></option>
            <option v-for="fs in factorySpeciesList" :key="fs.factory_species_code" :value="fs.factory_species_code">{{ fs.factory_species_name }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="col-md-2 col-sm-2 col-xs-2">
          日付<span class="required-mark">*</span>
        </th>
        <td colspan="3" class="text-left">
          <div class="form-inline">
            <datepicker-ja attr-name="date_from" :date="dateFrom"></datepicker-ja>
            から
            <select class="form-control" name="date_range" v-model="dateRange" required>
              <option value="4">4週間</option>
              <option value="8">8週間</option>
              <option value="12">12週間</option>
            </select>
          </div>
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
    oldParams: {
      type: Object,
      required: true
    }
  },
  data: function () {
    const moment = require('moment')
    return {
      factoryCode: this.oldParams.factory_code || null,
      factorySpeciesCode: this.oldParams.factory_species_code || null,
      factorySpeciesList: [],
      dateFrom: this.oldParams.date_from || moment().startOf('isoWeek').format('YYYY/MM/DD'),
      dateRange: this.oldParams.date_range || 4
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getFactorySpecies()
      this.factorySpeciesCode = this.oldParams.factory_species_code
    }
  },
  computed: {
    disabledToSelectFactorySpecies: function () {
      return this.factorySpeciesList.length === 0
    }
  },
  methods: {
    getFactorySpecies: function () {
      this.factorySpeciesCode = null
      this.factorySpeciesList = []
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-factory-species', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.factorySpeciesList = response.data
          if (this.disabledToSelectFactorySpecies) {
            alert('工場品種が未登録の工場です。')
          }
        })
        .catch(() => {
          alert('工場品種の取得に失敗しました。')
        })
    }
  }
}
</script>
