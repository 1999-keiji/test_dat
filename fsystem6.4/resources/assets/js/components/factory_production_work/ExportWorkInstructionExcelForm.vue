<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th class="col-md-2 col-sm-2 col-xs-2">
          工場
          <span class="required-mark">*</span>
        </th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select id="factory_code" class="form-control" name="factory_code" v-model="factoryCode" @change="getSpeciesWithFactoryCode">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th class="col-md-2 col-sm-2 col-xs-2">
          品種
          <span class="required-mark">*</span>
        </th>
        <td class="col-md-4 col-sm-4 col-xs-4">
          <select id="species_code" class="form-control" name="species_code" v-model="speciesCode" :disabled="disabledToSelectSpecies">
            <option value=""></option>
            <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">{{ s.species_name }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>
          作業日
          <span class="required-mark">*</span>
        </th>
        <td colspan="2">
          <datepicker-ja attr-name="working_date_from" :date="oldParams.working_date_from"></datepicker-ja>
          <span>&nbsp;&nbsp;～&nbsp;&nbsp;</span>
          <datepicker-ja attr-name="working_date_to" :date="oldParams.working_date_to"></datepicker-ja>
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
  data: function ()  {
    return {
      factoryCode: this.oldParams.factory_code || '',
      speciesCode: this.oldParams.species_code || '',
      speciesList: [],
    }
  },
  computed: {
    disabledToSelectSpecies: function () {
      return this.speciesList.length === 0
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code
    }
  },
  methods: {
    getSpeciesWithFactoryCode: function () {
      this.speciesCode = null
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
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
