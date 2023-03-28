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
        <a class="btn btn-default btn-lg back-button can-transition" :href="hrefToPrevious">
          <i class="fa fa-arrow-left"></i> 戻る
        </a>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-7">
        <a
          class="btn btn-default btn-lg pull-right"
          :href="hrefToExportData + '?label_of_bed=' + labelOfBed"
          @click="exportData($event)">
          <i class="fa fa-edit"></i> 帳票
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8 col-sm-10 col-md-soffset-2 col-sm-offset-1">
        <div class="form-inline">
          <table class="table table-color-bordered">
            <tbody>
              <tr>
                <th>画面切替</th>
                <td class="text-left" colspan="3">
                  <a :href="hrefToCultivationStates">各階栽培株数一覧</a>
                  <a :href="hrefToCultivationStatesSum">各階栽培株数合計表</a>
                </td>
              </tr>
              <tr>
                <th>工場</th>
                <td class="text-left">{{ factory.factory_abbreviation }}</td>
                <th>工場取扱品種</th>
                <td class="text-left">{{ factorySpecies.factory_species_name }}</td>
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
          <div class="col-md-4 col-sm-4 text-right change-simulation-date">
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['prev_day'] === '#'}"
              :href="switchingDateUrlList['prev_day']">
              <i class="fa fa-angle-left" aria-hidden="true"></i> 1日前
            </a>
          </div>
          <div class="col-md-4 col-sm-4 text-center">
            <h3>{{ workingDate.ja }}</h3>
          </div>
          <div class="col-md-4 col-sm-4 change-simulation-date">
            <a class="btn btn-default"
              :class="{disabled: switchingDateUrlList['next_day'] === '#'}"
              :href="switchingDateUrlList['next_day']">
              1日後
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
                  <a :href="currentUrl + '/' + floor.floor">
                    {{ floor.floor }}<br>階
                  </a>
                </th>
                <th>{{ row.row }}段</th>
                <template v-for="(bed, bedIndex) in row.beds">
                  <td v-if="! bed.other_species"
                    :key="[bed.row, bed.column].join('-')"
                    :style="bed.stage && bed.pattern ? {backgroundColor: '#' + bed.label_color} : {}">
                    {{ labelOfBed === 'pattern' ? bed.pattern : (bed.number_of_panels || '') }}
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
    workingDate: {
      type: Object,
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
    hrefToCultivationStates: {
      type: String,
      required: true
    },
    hrefToCultivationStatesSum: {
      type: String,
      required: true
    }
  },
  data: function () {
    let baseUrl = location.href.split('/').slice(0, -1).join('/'),
      switchingDateUrlList = {}
    for (const [key, date] of Object.entries(this.workingDate.options)) {
      switchingDateUrlList[key] = date ? baseUrl + '/' + date : '#'
    }

    let sumOfPanels = {}
    for (const row of this.factoryLayout.rows) {
      sumOfPanels[row.row] = {}
      for (const stage of this.factoryGrowingStages) {
        sumOfPanels[row.row][stage.number_of_holes] = 0
      }
    }

    for (const floor of this.factoryLayout.beds) {
      for (const row of floor.rows) {
        for (const bed of row.beds) {
          if (bed.stage && bed.pattern) {
            sumOfPanels[row.row][bed.number_of_holes] += bed.number_of_panels
          }
        }
      }
    }

    return {
      currentUrl: location.href.split('?')[0],
      baseUrl: baseUrl,
      labelOfBed: 'pattern',
      switchingDateUrlList: switchingDateUrlList,
      sumOfPanels: sumOfPanels,
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
  methods: {}
}
</script>
