<template>
  <table class="table table-color-bordered growth-simulation-search">
    <tbody class="simulation-add">
      <tr>
        <th>工場<span class="required-mark">*</span></th>
        <td>
          <select class="form-control" name="factory_code" v-model="factoryCode" @change="getFactorySpecies" required>
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th>工場品種<span class="required-mark">*</span></th>
        <td>
          <select class="form-control" name="factory_species_code" v-model="factorySpeciesCode" required :disabled="disabledToSelectFactorySpecies">
            <option value=""></option>
            <option v-for="fs in factorySpeciesList" :key="fs.factory_species_code" :value="fs.factory_species_code">{{ fs.factory_species_name }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>表示期間<span class="required-mark">*</span></th>
        <td colspan="2">
          <div class="radio-inline">
            <label><input type="radio" name="display_term" value="date" v-model="displayTerm">日単位</label>
          </div>
          <div class="radio-inline">
            <label><input type="radio" name="display_term" value="month" v-model="displayTerm">月単位</label>
          </div>
          <div v-if="displayTerm == 'date'" class="day-month-select">
            <datepicker-ja attr-name="display_from_date" :date="displayFromDate" :disabled-days-of-week="[0, 2, 3, 4, 5, 6]"></datepicker-ja>
            から
            <select class="form-control" name="week_term" v-model="weekTerm" required>
              <option value="1">1週間</option>
              <option value="2">2週間</option>
              <option value="3">3週間</option>
            </select>
          </div>
          <div v-else class="day-month-select">
            <year-monthpicker-ja name="display_from_month" :value="displayFromMonth"></year-monthpicker-ja>
            <label>から1年間</label>
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
    },
    searchParams: {
      type: Object,
      required: true
    },
    defaultHarvestingDate: {
      type: String,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || null,
      factorySpeciesCode: this.oldParams.factory_species_code || this.searchParams.factory_species_code || null,
      factorySpeciesList: [],
      displayTerm: this.oldParams.display_term || this.searchParams.display_term || 'date',
      displayFromDate: this.oldParams.display_from_date || this.searchParams.display_from_date || this.defaultHarvestingDate,
      displayFromMonth: this.oldParams.display_from_month || this.searchParams.display_from_month || '',
      weekTerm: this.oldParams.week_term || this.searchParams.week_term || 1,
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getFactorySpecies()
      this.factorySpeciesCode = this.oldParams.factory_species_code || this.searchParams.factory_species_code
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
      axios.get('/api/get-factory-species', {
        params: {
          factory_code: this.factoryCode,
          can_select_on_simulation: true
        }
      })
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
