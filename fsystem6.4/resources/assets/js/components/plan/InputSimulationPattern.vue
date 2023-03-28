<style scoped>
  label.pattern-num {
    font-size: 1.5em;
    color: dimgray;
  }

  .input-pattern {
    display: flex;
  }
  .input-pattern>table {
    width: 80%;
  }
  .input-pattern>table>tbody tr.stock td input.stock-num {
    width: 100%;
  }

  .row-standard {
    width: 20%;
    margin-left: 15px;
  }
  .row-standard>button {
    width: 50%;
  }
</style>

<template>
  <div class="row">
    <div class="col-md-11 col-sm-11 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <label class="pattern-num">入力{{ detailId }}</label>
    </div>
    <div class="col-md-10 col-sm-8 col-xs-9 col-md-offset-1 col-sm-offset-1 input-pattern">
      <table class="table table-color-bordered table-more-condensed table-layout-fixed">
        <thead>
          <tr>
            <th></th>
            <th v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number">
              {{ gsi.growing_stage_name }}
            </th>
          </tr>
        </thead>

        <tbody v-if="inputChange === inputChangeList['収穫基準']">
          <tr>
            <th>日付</th>
            <th v-for="(gsi, idx) in growthSimulationItems" :key="gsi.growing_stages_sequence_number">
              <div v-if="(idx + 1) !== growthSimulationItems.length">
                {{ gsi.date | formatDate }}<br>{{ gsi.date | getDayOfTheWeek }}
              </div>
              <div v-else>
                <datepicker-ja
                  :date="gsi.date"
                  :disabled-days-of-week="disabledDaysOfWeekOnHarvesting"
                  :allow-empty="true"
                  @update-date="updateHarvestingDate">
                </datepicker-ja>
              </div>
            </th>
          </tr>
          <tr>
            <th>ベッド数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.bed_number !== null}">
              {{ ((gsi.bed_number !== null) ? gsi.bed_number : '-') | formatNumber }}
            </td>
          </tr>
          <tr>
            <th>パネル数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.panel_number}">
              {{ (gsi.panel_number || '-') | formatNumber }}
            </td>
          </tr>
          <tr class="stock">
            <th>株数</th>
            <td v-for="(gsi, idx) in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.stock_number}">
              <div v-if="(idx + 1) !== growthSimulationItems.length">
                {{ (gsi.stock_number || '-') | formatNumber }}
              </div>
              <div v-else>
                <input class="text-right ime-inactive stock-num" type="number" maxlength="6" pattern="^[0-9]+$" required v-model.number="gsi.stock_number">
              </div>
            </td>
          </tr>
          <tr>
            <th>生育日数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number">
              {{ gsi.growth_days ? gsi.growth_days + '日' : '-' }}
            </td>
          </tr>
        </tbody>

        <tbody v-if="inputChange === inputChangeList['播種基準']">
          <tr>
            <th>日付</th>
            <th v-for="(gsi, idx) in growthSimulationItems" :key="gsi.growing_stages_sequence_number">
              <div v-if="idx !== 0">
                {{ gsi.date | formatDate }}<br>{{ gsi.date | getDayOfTheWeek }}
              </div>
              <div v-else>
                <datepicker-ja
                  :date="gsi.date"
                  :disabled-days-of-week="disabledDaysOfWeekOnSeeding"
                  :allow-empty="true"
                  @update-date="updateSeedingDate">
                </datepicker-ja>
              </div>
            </th>
          </tr>
          <tr>
            <th>ベッド数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.bed_number !== null}">
              {{ ((gsi.bed_number !== null) ? gsi.bed_number : '-') | formatNumber }}
            </td>
          </tr>
          <tr>
            <th>パネル数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.panel_number}">
              {{ (gsi.panel_number || '-') | formatNumber }}
            </td>
          </tr>
          <tr class="stock">
            <th>株数</th>
            <td v-for="(gsi, idx) in growthSimulationItems" :key="gsi.growing_stages_sequence_number" :class="{'text-right': gsi.stock_number}">
              <div v-if="idx !== 0">
                {{ (gsi.stock_number || '-') | formatNumber }}
              </div>
              <div v-else>
                <input class="text-right ime-inactive stock-num" type="number" maxlength="6" pattern="^[0-9]+$" required v-model.number="gsi.stock_number">
              </div>
            </td>
          </tr>
          <tr>
            <th>生育日数</th>
            <td v-for="gsi in growthSimulationItems" :key="gsi.growing_stages_sequence_number">
              {{ gsi.growth_days ? gsi.growth_days + '日' : '-' }}
            </td>
          </tr>
        </tbody>
      </table>
      <div class="row row-standard">
        <table class="table table-color-bordered table-more-condensed">
          <tbody>
            <tr>
              <th>入力切替</th>
              <td>
                <div v-for="(value, label) in inputChangeList" :key="value" class="radio-inline">
                  <label>
                    <input name="input_change" type="radio" :value="value" v-model="inputChange">{{ label }}
                  </label>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
        <button class="btn btn-default" type="button" @click="recalculate">
          再計算
        </button>
      </div>
    </div>
  </div>
</template>

<script>
const moment = require('moment')
moment.locale('ja')

export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    detailId: {
      type: Number,
      required: true
    },
    factorySpecies: {
      type: Object,
      required: true
    },
    factoryGrowingStages: {
      type: Array,
      required: true
    },
    inputChangeList: {
      type: Object,
      required: true
    },
    growingStageList: {
      type: Object,
      required: true
    },
    disabledDaysOfWeekOnHarvesting: {
      type: Array,
      required: true
    },
    disabledDaysOfWeekOnSeeding: {
      type: Array,
      required: true
    }
  },
  data: function () {
    return {
      growthSimulationItems: [],
      inputChange: this.inputChangeList['収穫基準']
    }
  },
  created: function() {
    for (const fgs of this.factoryGrowingStages) {
      this.growthSimulationItems.push({
        factory_code: this.factory.factory_code,
        factory_species_code: this.factorySpecies.factory_species_code,
        detail_id: this.detailId,
        growing_stages_sequence_number: fgs.sequence_number,
        growing_stage: fgs.growing_stage,
        input_change: this.inputChange,
        growing_stage_name: fgs.growing_stage_name,
        date: null,
        bed_number: null,
        panel_number: null,
        stock_number: null,
        growth_days: fgs.growing_term
      })
    }

    this.growthSimulationItems.push({
      factory_code: this.factory.factory_code,
      factory_species_code: this.factorySpecies.factory_species_code,
      detail_id: this.detailId,
      growing_stages_sequence_number: this.factoryGrowingStages.length + 1,
      growing_stage: this.growingStageList['収穫'],
      growing_stage_name: '収穫',
      input_change: this.inputChange,
      date: null,
      bed_number: null,
      panel_number: null,
      stock_number: null,
      growth_days: null
    })

    this.$emit('get-growth-simulation-items', this.detailId, [])
  },
  filters: {
    formatDate: function (date) {
      if (! date) {
        return ''
      }

      return moment(date).format('YYYY/MM/DD')
    },
    getDayOfTheWeek: function (date) {
      if (! date) {
        return ''
      }

      return moment(date).format('(ddd)')
    },
    formatNumber: function (number) {
      if (typeof number === 'string') {
        return number
      }

      const number_format = require('locutus/php/strings/number_format')
      return number_format(number)
    }
  },
  watch: {
    inputChange: function () {
      this.initializeGrowthSimulationItems()
    }
  },
  methods: {
    initializeGrowthSimulationItems: function () {
      this.growthSimulationItems = this.growthSimulationItems.map((gsi) => {
        gsi.date = null
        gsi.bed_number = null
        gsi.panel_number = null
        gsi.stock_number = null

        return gsi
      })

      this.$emit('get-growth-simulation-items', this.detailId, [])
    },
    updateHarvestingDate: function (harvestingDate) {
      this.initializeGrowthSimulationItems()

      const last = this.growthSimulationItems.length - 1
      this.growthSimulationItems[last].date = harvestingDate
    },
    updateSeedingDate: function (seedingDate) {
      this.initializeGrowthSimulationItems()
      this.growthSimulationItems[0].date = seedingDate
    },
    recalculate: function () {
      let date = null,
        stock_number = null
      if (this.inputChange === this.inputChangeList['収穫基準']) {
        const last = this.growthSimulationItems.length - 1
        date = this.growthSimulationItems[last].date
        stock_number = this.growthSimulationItems[last].stock_number
      }
      if (this.inputChange === this.inputChangeList['播種基準']) {
        date = this.growthSimulationItems[0].date
        stock_number = this.growthSimulationItems[0].stock_number
      }

      if (! date || ! stock_number) {
        alert('日付と株数を入力してください。')
        return
      }
      if (! /^\d{4}\/\d{2}\/\d{2}$/.test(date)) {
        alert('日付の形式が正しくありません。')
        return
      }

      if (this.inputChange === this.inputChangeList['収穫基準']
        && this.disabledDaysOfWeekOnHarvesting.includes(parseInt(moment(date, 'YYYY/MM/DD').format('e')))) {
        alert('選択できない曜日です。')
        return
      }
      if (this.inputChange === this.inputChangeList['播種基準']
        && this.disabledDaysOfWeekOnSeeding.includes(parseInt(moment(date, 'YYYY/MM/DD').format('e')))) {
        alert('選択できない曜日です。')
        return
      }

      axios.get('/api/simulate-growing', {params: {
        factory_code: this.factory.factory_code,
        factory_species_code: this.factorySpecies.factory_species_code,
        input_change: this.inputChange,
        date,
        stock_number
      }})
        .then(response => {
          for (const [idx, gsi] of response.data.entries()) {
            this.growthSimulationItems[idx].date = gsi.date
            this.growthSimulationItems[idx].bed_number = gsi.bed_number
            this.growthSimulationItems[idx].panel_number = gsi.panel_number
            this.growthSimulationItems[idx].stock_number = gsi.stock_number
          }

          this.$emit('get-growth-simulation-items', this.detailId, this.growthSimulationItems)
        })
        .catch(() => {
          alert('再計算に失敗しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
