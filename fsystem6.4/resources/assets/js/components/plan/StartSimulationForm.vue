<style scoped>
  .basic-form {
    margin-top: 1em;
  }

  .simulation-add-buttons {
    padding: 0;
    width: 30%;
    bottom: 5px;
  }
</style>

<template>
  <div class="row row-pattern">
    <div class="form-horizontal basic-form">
      <div class="row">
        <div class="col-md-4 col-sm-5 col-xs-6 col-md-offset-1">
          <div class="row form-group">
            <label for="simulation_name" class="col-md-5 col-sm-5 control-label">
              シミュレーション名<span class="required-mark">*</span>
            </label>
            <div class="col-md-6 col-sm-7">
              <input id="simulation_name" class="form-control" name="simulation_name" type="text" required v-model="simulationName">
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label for="detail_number" class="col-md-6 col-sm-5 control-label">
              入力パターン数<span class="required-mark">*</span>
            </label>
            <div class="col-md-5 col-sm-7">
              <select id="detail_number" class="form-control" required>
                <option :value="0"></option>
                <option v-for="i in rangeOfPattern" :key="i" :value="i" >{{ i }}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="col-md-3 simulation-add-buttons">
          <button class="btn btn-lg btn-default change-pattern" type="button" @click="changeInputPattern">パターン数変更</button>
          <button v-show="detailNumber !== 0" class="btn btn-lg btn-default pull-right" type="button" @click="startSimulation">
            シミュレーション開始
          </button>
        </div>
      </div>
    </div>

    <input-simulation-pattern
      v-for="detailId in rangeOfDetailNumber"
      :key="detailId"
      :factory="factory"
      :detail-id="detailId"
      :factory-species="factorySpecies"
      :factory-growing-stages="factoryGrowingStages"
      :input-change-list="inputChangeList"
      :growing-stage-list="growingStageList"
      :disabled-days-of-week-on-harvesting="disabledDaysOfWeekOnHarvesting"
      :disabled-days-of-week-on-seeding="disabledDaysOfWeekOnSeeding"
      @get-growth-simulation-items="getGrowthSimulationItems">
    </input-simulation-pattern>
  </div>
</template>

<script>
const _ = require('lodash')

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
    factoryGrowingStages: {
      type: Array,
      required: true
    },
    maxPattern: {
      type: Number,
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
    },
    actionOfCreatingSimulation: {
      type: String,
      required: true
    },
    hrefToIndexOfGrowthSimulations: {
      type: String,
      required: true
    }
  },
  data: function () {
    return {
      simulationName: '',
      detailNumber: 0,
      growthSimulationItemsList: {}
    }
  },
  computed: {
    rangeOfPattern: function () {
      return _.range(1, this.maxPattern + 1)
    },
    rangeOfDetailNumber: function () {
      return _.range(1, this.detailNumber + 1)
    }
  },
  methods: {
    changeInputPattern: function () {
      const detailNumber = parseInt(document.getElementById('detail_number').value)
      if (! detailNumber) {
        alert('パターン数を選択してください。')
        return
      }

      this.detailNumber = 0
      this.detailNumber = detailNumber

      this.growthSimulationItemsList = {}
    },
    getGrowthSimulationItems:  function (detailId, growthSimulationItems) {
      this.growthSimulationItemsList[detailId] = growthSimulationItems
    },
    startSimulation: function () {
      if (this.simulationName === '') {
        alert('シミュレーション名を入力してください。')
        return
      }

      const calculated = Object.values(this.growthSimulationItemsList)
        .filter((growthSimulationItems) => growthSimulationItems.length !== 0)
        .length

      if (this.detailNumber !== calculated) {
        alert('再計算されていない入力パターンがあります。')
        return
      }

      const moment = require('moment'),
        firstHarvestingDate = Object.values(this.growthSimulationItemsList)
          .map(growthSimulationItems => {
            const last = growthSimulationItems.length - 1
            return moment(growthSimulationItems[last].date)
          })
          .sort(harvestingDate => harvestingDate.format('x'))
          .reverse()[0]

      if (moment().isAfter(firstHarvestingDate, 'day')) {
        alert('収穫開始日が過去の日付です。収穫開始日を今日以降の日付にしてください。')
        return
      }

      if (! confirm('シミュレーションを開始してもよろしいですか？')) {
        return
      }

      axios.post(this.actionOfCreatingSimulation, {
        factory_code: this.factory.factory_code,
        factory_species_code: this.factorySpecies.factory_species_code,
        detail_number: this.detailNumber,
        simulation_name: this.simulationName,
        growth_simulation_items_list: this.growthSimulationItemsList
      })

      alert('シミュレーション開始の準備をしています。')
      location.href = this.hrefToIndexOfGrowthSimulations
    }
  }
}
</script>
