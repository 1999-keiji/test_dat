<style scoped>
  td.empty-bed,
  td.other-species,
  td.washing-bed {
    width: 40px;
  }
  td.washing-bed {
    background-color: #00FFFF;
  }

  table.panel-allocation>tbody>tr>th,
  table.panel-allocation>tbody>tr>td {
    width: 38px;
    height: 30px;
    padding: 0;
  }
  table.panel-allocation>tbody>tr>td.growing-stage-name {
    width: 55px;
  }

  table.panel-allocation>tbody>tr>th.circulation-border,
  table.panel-allocation>tbody>tr>td.circulation-border {
    background-color: #ffffff;
    border: none;
    width: 8px;
  }
  table.panel-allocation>tbody>tr>td.floor-border {
    background-color: #ffffff;
    border: none;
    width: 8px;
    height: 8px;
  }
  table.panel-allocation>tbody>tr>td.sum-of-panel {
    padding-right: 5px;
  }
  table.panel-allocation>tbody>tr>td.sum-of-panel-per-stage {
    font-weight: bold;
    padding-right: 5px;
  }
</style>

<template>
  <div>
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-5">
        <a class="btn btn-default btn-lg back-button can-transition" :href="hrefToPrevious" @click="confirmLeave($event)">
          <i class="fa fa-arrow-left"></i> 戻る
        </a>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-7">
        <a
          class="btn btn-default btn-lg"
          :href="hrefToExportData + '?display_kubun=' + displayKubun + '&label_of_bed=' + labelOfBed"
          @click="exportData($event)">
          <i class="fa fa-edit"></i> 帳票
        </a>
        <button ref="saveButton"
          v-if="! hasFixed && ! displayOnlyFixed"
          class="btn btn-default btn-lg pull-right"
          type="button"
          @click="savePanelAllocation($event)">
          <i class="fa fa-save"></i> 保存
        </button>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="form-inline">
          <table class="table table-color-bordered">
            <tbody>
              <tr>
                <th>画面切替</th>
                <td class="text-left" colspan="3">
                  <a :href="hrefToFloorCultivationStock" @click="confirmLeave($event)">各階栽培株数一覧</a>
                  <a :href="hrefToFloorCultivationStockSum" @click="confirmLeave($event)">各階栽培株数合計表</a>
                </td>
              </tr>
              <tr>
                <th>工場</th>
                <td class="text-left">{{ factory.factory_abbreviation }}</td>
                <th>工場取扱品種</th>
                <td class="text-left">{{ factorySpecies.factory_species_name }}</td>
                <th>シミュレーション</th>
                <td class="text-left">{{ growthSimulation.simulation_name }}</td>
              </tr>
              <tr>
                <th>
                  日付ジャンプ
                  <span class="required-mark">*</span>
                </th>
                <td>
                  <datepicker-ja
                    ref="targetDate"
                    attr-name="date"
                    :date="simulationDate.value"
                    :disabled-days-of-week="simulationDate.disabled_days_of_week">
                  </datepicker-ja>
                  <a class="btn btn-default" href="#" @click="switchDate">
                    <i class="fa fa-location-arrow"></i> 移動
                  </a>
                </td>
                <th>
                  表示切替
                  <span class="required-mark">*</span>
                </th>
                <td>
                  <label class="radio-inline" v-for="dk in displayKubunList" :key="dk.value">
                    <input
                      type="radio"
                      name="display_kubun"
                      v-model="displayKubun"
                      :value="dk.value"
                      :disabled="hasFixed">
                      {{ dk.label }}
                  </label>
                  <button class="btn btn-default" type="button" :disabled="hasFixed" @click="switchDisplayKubun">
                    <i class="fa fa-location-arrow"></i> 表示
                  </button>
                </td>
                <th>ベッド欄表示</th>
                <td>
                  <label class="radio-inline">
                    <input type="radio" value="pattern" v-model="labelOfBed">サイクルパターン
                  </label>
                  <label class="radio-inline">
                    <input type="radio" value="number_of_panels" v-model="labelOfBed">パネル
                  </label>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="row" v-if="! hasFixed && ! displayOnlyFixed">
      <div :class="bedLegendsGrid">
        <table class="table table-bordered table-more-condensed panel-allocation">
          <tbody>
            <tr>
              <th>フロア</th>
              <template v-for="stage in bedStatusOptions.slice().reverse()">
                <th :key="stage.growing_stage_sequence_number"></th>
                <th v-for="pattern in stage.factory_cycle_pattern_items" :key="[stage.growing_stage_sequence_number, pattern.pattern].join('-')">
                  {{ pattern.pattern }}
                </th>
              </template>
            </tr>
            <tr v-for="(floor, index) in factoryLayout.beds" :key="floor.floor">
              <th>{{ floor.floor }}階</th>
              <template v-for="stage in bedStatusOptions.slice().reverse()">
                <td v-if="index === 0" class="growing-stage-name" :key="[floor.floor, stage.growing_stage_sequence_number].join('-')">
                  {{ stage.growing_stage_name }}
                </td>
                <td v-else-if="index === 1" class="growing-stage-name" :key="[floor.floor, stage.growing_stage_sequence_number].join('-')">
                  ({{ stage.number_of_holes }}穴)
                </td>
                <td v-else class="growing-stage-name" :key="[floor.floor, stage.growing_stage_sequence_number].join('-')">
                  &nbsp;
                </td>
                <template v-for="pattern in stage.factory_cycle_pattern_items" >
                  <td
                    :key="[floor.floor, stage.growing_stage_sequence_number, pattern.pattern].join('-')"
                    class="number-of-beds"
                    :style="{backgroundColor: '#' + stage.label_color}">
                    {{ remainingBeds[stage.growing_stage_sequence_number][floor.floor][pattern.pattern] }}
                  </td>
                </template>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3 col-sm-3 col-md-offset-9 col-sm-offset-9">
        <table class="table table-more-condensed table-color-bordered">
          <tbody>
            <tr>
              <td class="empty-bed">&nbsp;</td>
              <td>空き</td>
              <td class="other-species">&nbsp;</td>
              <td>別品種</td>
              <td class="washing-bed">&nbsp;</td>
              <td>洗浄</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="row">
      <div class="col-md-10 col-sm-12 col-md-offset-1">
        <div class="row simulation-dates">
          <div class="col-md-4 col-sm-4 change-simulation-date">
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['prev_month'] === '#'}"
              :href="switchingDateUrlList['prev_month']"
              @click="confirmLeave($event)">
              <i class="fa fa-angle-left" aria-hidden="true"></i>
              <i class="fa fa-angle-double-left" aria-hidden="true"></i>
              1ヶ月前
            </a>
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['prev_week'] === '#'}"
              :href="switchingDateUrlList['prev_week']"
              @click="confirmLeave($event)">
              <i class="fa fa-angle-double-left" aria-hidden="true"></i> 1週間前
            </a>
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['prev_day'] === '#'}"
              :href="switchingDateUrlList['prev_day']"
              @click="confirmLeave($event)">
              <i class="fa fa-angle-left" aria-hidden="true"></i> 1日前
            </a>
          </div>
          <div class="col-md-4 col-sm-4 text-center">
            <h3>{{ simulationDate.ja }}</h3>
          </div>
          <div class="col-md-4 col-sm-4 text-right change-simulation-date">
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['next_day'] === '#'}"
              :href="switchingDateUrlList['next_day']"
              @click="confirmLeave($event)">
              1日後
              <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['next_week'] === '#'}"
              :href="switchingDateUrlList['next_week']"
              @click="confirmLeave($event)">
              1週間後
              <i class="fa fa-angle-double-right" aria-hidden="true"></i>
            </a>
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['next_month'] === '#'}"
              :href="switchingDateUrlList['next_month']"
              @click="confirmLeave($event)">
              1ヶ月後
              <i class="fa fa-angle-double-right" aria-hidden="true"></i>
              <i class="fa fa-angle-right" aria-hidden="true"></i>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-9 col-sm-9">
        <table class="table table-bordered table-more-condensed panel-allocation">
          <tbody>
            <tr>
              <td class="border-none" colspan="2"></td>
              <template v-for="(circulation, index) in factoryLayout.circulations">
                <th :key="circulation.circulation" :colspan="circulation.count">
                  循環{{ circulation.circulation }}
                </th>
                <th v-if="index !== (factoryLayout.circulations.length - 1)" class="circulation-border" :key="[circulation.circulation, index].join('-')"></th>
              </template>
            </tr>
            <tr>
              <th colspan="2">ライン</th>
              <template v-for="(column, index) in factoryLayout.columns">
                <th :key="column.column">{{ column.column_name }}</th>
                <th
                  v-if="index !== 0 && index !== (factoryLayout.columns.length - 1) && factoryLayout.columns[index + 1].circulation !== column.circulation"
                  :key="[column.column, index].join('-')"
                  class="circulation-border">
                </th>
              </template>
            </tr>
            <template v-for="floor in factoryLayout.beds">
              <tr v-for="(row, index) in floor.rows" :key="row.row">
                <th v-if="index === 0" :rowspan="floor.rows.length">
                  <a :href="currentUrl + '/' + floor.floor + '?display_kubun=' + displayKubun" @click="confirmLeave($event)">
                    {{ floor.floor }}<br>階
                  </a>
                </th>
                <th>{{ row.row }}段</th>
                <template v-for="(bed, bedIndex) in row.beds">
                  <td v-if="! bed.other_species" :key="[bed.row, bed.column].join('-')">
                    <select-stage-and-pattern
                      v-if="(! hasFixed && ! bed.is_fixed) || (hasFixed && bed.stage)"
                      :bed-coordination="bed"
                      :factory-growing-stages="factoryGrowingStages"
                      :bed-status-options="bedStatusOptions"
                      :label-of-bed="labelOfBed"
                      :has-fixed="hasFixed"
                      v-on:update-panel-allocation="updatePanelAllocation"
                      v-on:has-changed="hasChanged">
                    </select-stage-and-pattern>
                    <replace-bed
                      v-else-if="! hasFixed && bed.is_fixed"
                      :bed-coordination="bed"
                      :bed-status-options="bedStatusOptions"
                      :label-of-bed="labelOfBed"
                      v-on:update-panel-allocation="updatePanelAllocation"
                      v-on:has-changed="hasChanged">
                    </replace-bed>
                  </td>
                  <td v-else :class="{'other-species': bed.other_species}" :key="[bed.row, bed.column].join('-')">&nbsp;</td>
                  <td
                    v-if="bedIndex !== 0 && bedIndex !== (row.beds.length - 1) && row.beds[bedIndex + 1].circulation !== bed.circulation"
                    :key="[bed.row, bed.column, bedIndex].join('-')"
                    class="circulation-border">
                  </td>
                </template>
              </tr>
              <tr :key="[floor.floor, 'border'].join('-')">
                <td
                  v-for="space in Array.from(Array(2 + factoryLayout.columns.length + (factoryLayout.circulations.length - 1), (v, k) => k))"
                  :key="[floor.floor, space].join('-')"
                  class="floor-border">
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      <div class="col-md-3 col-sm-3">
        <table class="table table-bordered table-more-condensed panel-allocation">
          <tbody>
            <tr>
              <th :colspan="factoryGrowingStages.length">配置パネル合計</th>
            </tr>
            <tr>
              <th v-for="stage in factoryGrowingStages" :key="stage.growing_stage_sequence_number">{{ stage.number_of_holes }}穴</th>
            </tr>
            <template v-for="(row, index) in this.factoryLayout.rows">
              <tr :key="row.row">
                <td
                  v-for="stage in factoryGrowingStages"
                  :key="[row.row, stage.growing_stage_sequence_number].join('-')"
                  class="sum-of-panel text-right">
                  {{ sumOfPanels[row.row][stage.number_of_holes] }}
                </td>
              </tr>
              <tr v-if="index !== (factoryLayout.rows.length - 1) && factoryLayout.rows[index + 1].floor !== row.floor" :key="[row.row, index].join('-')">
                <td v-for="stage in factoryGrowingStages" :key="stage.growing_stage_sequence_number" class="floor-border"></td>
              </tr>
            </template>
            <tr>
              <td v-for="stage in factoryGrowingStages" :key="stage.growing_stage_sequence_number" class="floor-border"></td>
            </tr>
            <tr>
              <td v-for="stage in factoryGrowingStages" :key="stage.growing_stage_sequence_number" class="sum-of-panel-per-stage text-right">
                {{ sumOfPanelsPerStage[stage.number_of_holes] }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <form v-if="! hasFixed" ref="form" :action="currentUrl" method="POST">
        <template v-for="(param, column) in params">
          <template v-for="(value, row) in param">
            <input :key="[column, row, 'stage'].join('-')" type="hidden" :name="'statuses[' + column + '][' + row + '][stage]'" :value="value.stage">
            <input :key="[column, row, 'pattern'].join('-')" type="hidden" :name="'statuses[' + column + '][' + row + '][pattern]'" :value="value.pattern">
          </template>
        </template>
        <input name="_token" type="hidden" :value="csrf">
        <input name="_method" type="hidden" value="PATCH">
      </form>
    </div>
  </div>
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
    displayKubunList: {
      type: Array,
      required: true
    },
    simulationDate: {
      type: Object,
      required: true
    },
    numberOfBeds: {
      type: Object,
      required: true
    },
    bedStatusOptions: {
      type: Array,
      required: true
    },
    factoryGrowingStages: {
      type: Array,
      required: true
    },
    factoryLayout: {
      type: Object,
      required: true
    },
    hrefToPrevious: {
      type: String,
      required: true
    },
    hrefToExportData: {
      type: String,
      required: true
    },
    hrefToFloorCultivationStock: {
      type: String,
      required: true
    },
    hrefToFloorCultivationStockSum: {
      type: String,
      required: true
    },
  },
  data: function () {
    let displayKubun = null
    for (const dk of this.displayKubunList) {
      if (dk.selected) {
        displayKubun = dk.value
      }
    }

    let bedLegendsGrid = {
      'col-md-12': this.bedStatusOptions.length >= 4,
      'col-sm-12': this.bedStatusOptions.length >= 4,
      'col-md-10': this.bedStatusOptions.length === 3,
      'col-sm-10': this.bedStatusOptions.length === 3,
      'col-md-offset-2': this.bedStatusOptions.length === 3,
      'col-sm-offset-2': this.bedStatusOptions.length === 3,
      'col-md-7': this.bedStatusOptions.length === 2,
      'col-sm-7': this.bedStatusOptions.length === 2,
      'col-md-offset-5': this.bedStatusOptions.length === 2,
      'col-sm-offset-5': this.bedStatusOptions.length === 2,
      'col-md-4': this.bedStatusOptions.length <= 1,
      'col-sm-4': this.bedStatusOptions.length <= 1,
      'col-md-offset-8': this.bedStatusOptions.length <= 1,
      'col-sm-offset-8': this.bedStatusOptions.length <= 1,
    }

    let baseUrl = location.href.split('/').slice(0, -1).join('/'),
      switchingDateUrlList = {}
    for (const [key, date] of Object.entries(this.simulationDate.options)) {
      switchingDateUrlList[key] = date ? baseUrl + '/' + date + '?display_kubun=' + displayKubun : '#'
    }

    let sumOfPanels = {}
    for (const row of this.factoryLayout.rows) {
      sumOfPanels[row.row] = {}
      for (const stage of this.factoryGrowingStages) {
        sumOfPanels[row.row][stage.number_of_holes] = 0
      }
    }

    let params = {}
    for (const column of this.factoryLayout.columns) {
      params[column.column] = {}
      for (const row of this.factoryLayout.rows) {
        params[column.column][row.row] = {stage: '', pattern: ''}
      }
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      currentUrl: location.href.split('?')[0],
      baseUrl: baseUrl,
      displayKubun: displayKubun,
      labelOfBed: 'pattern',
      bedLegendsGrid: bedLegendsGrid,
      switchingDateUrlList: switchingDateUrlList,
      remainingBeds: this.numberOfBeds,
      sumOfPanels: sumOfPanels,
      params: params,
      bedHasChanged: false,
      hasFixed: this.growthSimulation.has_fixed,
      displayOnlyFixed: this.bedStatusOptions.length === 0,
      confirmMessage: '保存されていない情報があります。画面を切り替えてよろしいですか？'
    }
  },
  computed: {
    sumOfPanelsPerStage: function () {
      return Object.values(this.sumOfPanels).reduce((sumOfPanelsPerStage, row) => {
        for (const [numberOfHoles, sumOfPanels] of Object.entries(row)) {
          if (! sumOfPanelsPerStage[numberOfHoles]) {
            sumOfPanelsPerStage[numberOfHoles] = 0
          }

          sumOfPanelsPerStage[numberOfHoles] += sumOfPanels
        }

        return sumOfPanelsPerStage
      }, {})
    }
  },
  methods: {
    updatePanelAllocation: function (bedCoordination, selected, prevSelected) {
      this.params[bedCoordination.column][bedCoordination.row]['stage'] = ''
      this.params[bedCoordination.column][bedCoordination.row]['pattern'] = ''

      if (selected) {
        const stage = selected.stage.growing_stage_sequence_number,
          pattern = selected.pattern.pattern

        this.sumOfPanels[bedCoordination.row][selected.stage.number_of_holes] += selected.pattern.number_of_panels
        if (stage in this.remainingBeds) {
          this.remainingBeds[stage][bedCoordination.floor][pattern]--
        }

        this.params[bedCoordination.column][bedCoordination.row]['stage'] = stage
        this.params[bedCoordination.column][bedCoordination.row]['pattern'] = pattern
      }
      if (prevSelected) {
        const prev_stage = prevSelected.stage.growing_stage_sequence_number,
          prev_pattern = prevSelected.pattern.pattern

        this.sumOfPanels[bedCoordination.row][prevSelected.stage.number_of_holes] -= prevSelected.pattern.number_of_panels
        if (prev_stage in this.remainingBeds) {
          this.remainingBeds[prev_stage][bedCoordination.floor][prev_pattern]++
        }
      }
    },
    hasChanged: function () {
      this.bedHasChanged = true
    },
    confirmLeave: function (event) {
      if (this.bedHasChanged && ! confirm(this.confirmMessage)) {
        event.preventDefault()
      }
    },
    exportData: function (event) {
      if (this.bedHasChanged && ! confirm('保存されていない情報があります。変更した情報が反映されない状態で帳票を出力しますが、よろしいですか？')) {
        event.preventDefault()
      }
    },
    switchDate: function () {
      if (this.bedHasChanged && ! confirm(this.confirmMessage)) {
        return
      }

      location.href = this.baseUrl + '/' + this.$refs.targetDate.value.replace(/\//g, '-') + '?display_kubun=' + this.displayKubun
    },
    switchDisplayKubun: function () {
      if (this.bedHasChanged && ! confirm(this.confirmMessage)) {
        return
      }

      location.href = this.currentUrl + '?display_kubun=' + this.displayKubun
    },
    checkRemainingBeds: function () {
      let under_zero = [],
        over_zero = []
      for (const stage in this.remainingBeds) {
        for (const floor in this.remainingBeds[stage]) {
          for (const pattern in this.remainingBeds[stage][floor]) {
            if (this.remainingBeds[stage][floor][pattern] < 0) {
              under_zero.push(this.remainingBeds[stage][floor][pattern])
            }
            if (this.remainingBeds[stage][floor][pattern] > 0) {
              over_zero.push(this.remainingBeds[stage][floor][pattern])
            }
          }
        }
      }

      return [under_zero, over_zero]
    },
    savePanelAllocation: function (event) {
      let message = 'データを登録します。よろしいでしょうか？'

      const [under_zero, over_zero] = this.checkRemainingBeds()
      if (under_zero.length !== 0) {
        message = '残ベッド数が0を下回るステージ/パターンが存在します。\n' + message
      }
      if (over_zero.length !== 0) {
        message = '残ベッド数が0を上回るステージ/パターンが存在します。\n' + message
      }

      if (confirm(message)) {
        event.target.disabled = true
        this.$refs.form.submit()
      }
    }
  }
}
</script>
