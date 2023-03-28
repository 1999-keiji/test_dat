<template>
  <table class="table table-color-bordered growth-simulation-search">
    <tbody class="simulation-edit">
      <tr>
        <th>工場</th>
        <td class="text-left">{{ factory.factory_abbreviation }}</td>
        <th>工場品種</th>
        <td class="text-left">{{ factorySpecies.factory_species_name }}</td>
      </tr>
      <tr>
        <th>表示期間<span class="required-mark">*</span></th>
        <td>
          <div class="radio-inline">
            <label><input type="radio" name="display_term" value="date" v-model="displayTerm">日単位</label>
          </div>
          <div class="radio-inline">
            <label><input type="radio" name="display_term" value="month" v-model="displayTerm">月単位</label>
          </div>
          <div v-if="displayTerm == 'date'" class="day-month-select">
            <datepicker-ja attr-name="display_from_date" :date="displayFromDate" :disabled-days-of-week="[0, 2, 3, 4, 5, 6]">
            </datepicker-ja>
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
        <th>表示切替</th>
        <td>
          <label v-for="(value, label) in displayKubunList" class="radio-inline" :key="value">
            <input
              type="radio"
              name="display_kubun"
              v-model="displayKubun"
              :value="value"
              :disabled="growthSimulation.has_fixed">
              {{ label }}
          </label>
          <input v-if="growthSimulation.has_fixed" name="display_kubun" type="hidden" :value="displayKubunList['確定表示']">
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    factorySpecies: {
      type: Object,
      required: true
    },
    growthSimulation: {
      type: Object,
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
    },
    displayKubunList: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      displayTerm: this.oldParams.display_term || this.searchParams.display_term || 'date',
      displayFromDate: this.oldParams.display_from_date || this.searchParams.display_from_date || this.defaultHarvestingDate,
      displayFromMonth: this.oldParams.display_from_month || this.searchParams.display_from_month || '',
      weekTerm: this.oldParams.week_term || this.searchParams.week_term || 1,
      displayKubun: this.oldParams.display_kubun || this.searchParams.display_kubun || this.displayKubunList['確定表示']
    }
  }
}
</script>
