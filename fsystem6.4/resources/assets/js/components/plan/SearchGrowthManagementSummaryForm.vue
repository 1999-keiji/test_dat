<style scoped>
  select.week-term {
    width: 80px;
  }
</style>

<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th>表示切替<span class='required-mark'>*</span></th>
        <td>
          <select class="form-control" name="display_type" v-model="displayType">
            <option value=""></option>
            <option v-if="! belongsToFactory" value="factories">全工場</option>
            <option value="factory_species">工場－品種単位</option>
            <option value="delivery_destination">納入先単位</option>
          </select>
        </td>
        <th>表示期間<span class='required-mark'>*</span></th>
        <td colspan="2">
          <label class="radio-inline">
            <input type="radio" name="display_term" value="date" v-model="displayTerm">
            日単位
          </label>
          <label class="radio-inline">
            <input type="radio" name="display_term" value="month" v-model="displayTerm">
            月単位
          </label>
          <div v-if="displayTerm == 'date'" class="form-inline">
            <datepicker-ja attr-name="display_from_date" :date="displayFromDate" :disabled-days-of-week="[0, 2, 3, 4, 5, 6]">
            </datepicker-ja>
            から
            <select class="form-control week-term" name="week_term" v-model="weekTerm">
              <option value="1">１週間</option>
              <option value="2">２週間</option>
              <option value="3">３週間</option>
            </select>
          </div>
          <div v-else class="day-month-select">
            <year-monthpicker-ja name="display_from_month" :value="displayFromMonth"></year-monthpicker-ja>
            から１年間
          </div>
        </td>
        <th>出荷表示<span class='required-mark'>*</span></th>
        <td>
          <label class="radio-inline">
            <input type="radio" name='display_unit' v-model="displayUnit" value="weight">
            重量
          </label>
          <label v-if="displayType !== 'factories'" class="radio-inline">
            <input type="radio" name='display_unit' v-model="displayUnit" value="quantity">
            個数
          </label>
        </td>
      </tr>
      <tr v-if="displayType == 'factories'">
        <th>品種<span class='required-mark'>*</span></th>
        <td>
          <select class="form-control" name="species_code" v-model="speciesCode">
            <option value=""></option>
            <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">
              {{ s.species_name }}
            </option>
          </select>
        </td>
      </tr>
      <tr v-if="displayType == 'factory_species'">
        <th>工場<span class='required-mark'>*</span></th>
        <td>
          <select class="form-control" name="factory_code" v-model="factoryCode" @change="getSpeciesWithFactoryCode">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">
              {{ f.factory_abbreviation }}
            </option>
          </select>
        </td>
        <th>品種<span class='required-mark'>*</span></th>
        <td>
          <select class="form-control" name="species_code" v-model="speciesCode" :disabled="disabledToSelectSpecies">
            <option value=""></option>
            <option v-for="s in speciesListOfTheFactory" :key="s.species_code" :value="s.species_code">
              {{ s.species_name }}
            </option>
          </select>
        </td>
      </tr>
      <tr v-if="displayType == 'delivery_destination'">
        <th>品種<span class='required-mark'>*</span></th>
        <td>
          <select class="form-control" name="species_code" v-model="speciesCode">
            <option value=""></option>
            <option v-for="s in speciesList" :key="s.species_code" :value="s.species_code">
              {{ s.species_name }}
            </option>
          </select>
        </td>
        <th>納入先<span class='required-mark'>*</span></th>
        <td class="text-left">
          <search-master target="delivery_destination" :code="deliveryDestinationCode" :name="deliveryDestinationName">
          </search-master>
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
    speciesList: {
      type: Array,
      required: true
    },
    oldParams: {
      type: Object,
      default: function () {
        return {}
      }
    },
    searchParams: {
      type: Object,
      required: true
    },
    belongsToFactory: {
      type: Boolean,
      required: true
    },
    defaultHarvestingDate: {
      type: String,
      default: function () {
        return ''
      }
    }
  },
  data: function () {
    return {
      displayType: this.oldParams.display_type || this.searchParams.display_type || null,
      displayTerm: this.oldParams.display_term || this.searchParams.display_term || 'date',
      displayFromDate: this.oldParams.display_from_date || this.searchParams.display_from_date || this.defaultHarvestingDate,
      displayFromMonth: this.oldParams.display_from_month || this.searchParams.display_from_month || '',
      weekTerm: this.oldParams.week_term || this.searchParams.week_term || 1,
      displayUnit: this.oldParams.display_unit || this.searchParams.display_unit || 'weight',
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code || null,
      speciesCode: this.oldParams.species_code || this.searchParams.species_code || null,
      speciesListOfTheFactory: [],
      deliveryDestinationCode: this.oldParams.delivery_destination_code || this.searchParams.delivery_destination_code || null,
      deliveryDestinationName: this.oldParams.delivery_destination_name || this.searchParams.delivery_destination_name || null
    }
  },
  computed: {
    disabledToSelectSpecies: function () {
      return this.speciesListOfTheFactory.length === 0
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code || this.searchParams.species_code
    }
  },
  watch: {
    displayType: function (displayType) {
      if (displayType === 'factories') {
        this.displayUnit = 'weight'
      }
    }
  },
  methods: {
    getSpeciesWithFactoryCode: function () {
      this.speciesListOfTheFactory = []
      this.speciesCode = null
      if (! this.factoryCode) {
        return
      }

      axios.get('/api/get-species-with-factory-code', {
        params: {
          factory_code: this.factoryCode
        }
      })
        .then(response => {
          this.speciesListOfTheFactory = response.data
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
