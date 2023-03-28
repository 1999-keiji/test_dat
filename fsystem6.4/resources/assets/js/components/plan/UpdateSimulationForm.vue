<template>
  <div class="row row-pattern">
    <form class="form-horizontal basic-form pattern-head" :action="actionOfChangingSimulationName" method="POST">
      <div class="row">
        <div class="col-md-4 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label class="col-md-5 col-sm-5 control-label">シミュレーション名</label>
            <div class="col-md-6 col-sm-7">
              <input id="simulation_name" class="form-control" name="simulation_name" type="text" required v-model="simulationName">
            </div>
          </div>
        </div>
        <div class="col-md-1 col-sm-2 col-xs-3 pattern-button">
          <button class="btn btn-lg btn-default" type="submit">変更</button>
        </div>
        <div class="col-md-2 col-sm-5 col-xs-6">
          <div class="row form-group">
            <label class="col-md-7 col-sm-5 control-label">入力パターン数</label>
            <div class="col-md-3 col-sm-7 detail_number">
              <label id="detail_number" class="pattern-num">{{ growthSimulation.detail_number }}</label>
            </div>
          </div>
        </div>
        <div v-if="! growthSimulation.has_fixed" class="col-md-4 col-sm-2 col-xs-3 pattern-button">
          <button type="button" class="btn btn-lg btn-default start-simulation" @click="updateSimulation">シミュレーション開始</button>
        </div>
      </div>
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="PATCH">
    </form>

    <update-simulation-pattern
      v-for="(growthSimulationItems, detailId) in growthSimulationItemsList"
      :growth-simulation="growthSimulation"
      :key="detailId"
      :detail-id="parseInt(detailId)"
      :growth-simulation-items="growthSimulationItems"
      :input-change-list="inputChangeList"
      :disabled-days-of-week-on-harvesting="disabledDaysOfWeekOnHarvesting"
      :disabled-days-of-week-on-seeding="disabledDaysOfWeekOnSeeding"
      @get-growth-simulation-items="getGrowthSimulationItems">
    </update-simulation-pattern>
  </div>
</template>

<script>
const _ = require('lodash')

export default {
  props: {
    growthSimulation: {
      type: Object,
      required: true
    },
    growthSimulationItemsList: {
      type: Object,
      required: true
    },
    inputChangeList: {
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
    actionOfUpdatingSimulation: {
      type: String,
      required: true
    },
    actionOfChangingSimulationName: {
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
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      simulationName: this.growthSimulation.simulation_name,
      updatedGrowthSimulationItemsList: _.clone(this.growthSimulationItemsList)
    }
  },
  methods: {
    getGrowthSimulationItems:  function (detailId, growthSimulationItems) {
      this.updatedGrowthSimulationItemsList[detailId] = growthSimulationItems
    },
    updateSimulation: function () {
      const calculated = Object.values(this.updatedGrowthSimulationItemsList)
        .filter((growthSimulationItems) => growthSimulationItems.length !== 0)
        .length

      if (this.growthSimulation.detail_number !== calculated) {
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

      if (! confirm('保存されているシミュレーションがクリアされて初めからシミュレーションが開始されます。実行してもよろしいですか？')) {
        return
      }

      axios.post(this.actionOfUpdatingSimulation, {
        growth_simulation_items_list: this.updatedGrowthSimulationItemsList
      })

      alert('シミュレーション開始の準備をしています。')
      location.href = this.hrefToIndexOfGrowthSimulations
    }
  }
}
</script>
