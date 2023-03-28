<template>
  <table class="table table-color-bordered table-layout-fixed">
    <tbody>
      <tr>
        <th>工場<span class="required-mark">*</span></th>
        <td>
          <select class="form-control" name="factory_code" v-model="factoryCode" @change="getFactorySpecies">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th>工場品種</th>
        <td>
          <select class="form-control ac-factory-species" name="factory_species_code" v-model="factorySpeciesCode" :disabled="disabledToSelectFactorySpecies">
            <option value=""></option>
            <option v-for="fs in factorySpeciesList" :key="fs.factory_species_code" :value="fs.factory_species_code">{{ fs.factory_species_name }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>作業日<span class="required-mark">*</span></th>
        <td>
          <datepicker-ja attr-name="working_date" :date="workingDate"></datepicker-ja>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ['factories', 'workingDate', 'selectedFactoryCode', 'selectedFactorySpeciesCode'],
  data: function () {
    return {
      factoryCode: this.selectedFactoryCode,
      factorySpeciesCode: this.selectedFactorySpeciesCode,
      factorySpeciesList: []
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getFactorySpecies()
      this.factorySpeciesCode = this.selectedFactorySpeciesCode
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
      axios.get('/api/get-factory-species', { params: { factory_code: this.factoryCode } })
        .then(response => {
          this.factorySpeciesList = response.data
          if (this.disabledToSelectFactorySpecies) {
            alert('工場品種が未登録の工場です。')
          }
        })
        .catch(() => { alert('通信エラーが発生しました。しばらくお待ちください。') })
    }
  }
}
</script>
