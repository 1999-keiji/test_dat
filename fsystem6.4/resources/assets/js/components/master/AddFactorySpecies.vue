<style scoped>
.control-row {
  cursor: pointer;
}
</style>

<template>
  <div class="row">
    <div class="col-md-10 col-sm-10 col-xs-12 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered table-more-condensed factory-species">
        <thead>
          <tr>
            <th v-if="canSaveFactory"></th>
            <th>ステージ</th>
            <th>ステージ名</th>
            <th>色</th>
            <th>生育期間</th>
            <th>トレイ/パネル</th>
            <th>歩留率</th>
            <th>サイクルパターン</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(fgs, idx) in factoryGrowingStages" :key="idx">
            <th v-if="canSaveFactory">
              <i v-if="canAppendStage(fgs.growing_stage)" class="glyphicon glyphicon-plus control-row" @click="addStage(idx)"></i>
              <i v-if="canRemoveStage(fgs.growing_stage)" class="glyphicon glyphicon-minus control-row" @click="removeStage(idx)"></i>
            </th>
            <td>
              <label>{{ getLableOfGrowingStage(fgs.growing_stage) }}</label>
              <input :name="'growing_stage[' + (idx + 1) + ']'" type="hidden" :value="fgs.growing_stage">
            </td>
            <td class="text-left">
              <input v-if="isVariableStage(fgs.growing_stage)" :name="'growing_stage_name[' + (idx + 1) + ']'" class="form-control ime-active" type="text" v-model="fgs.growing_stage_name">
              <span v-if="! isVariableStage(fgs.growing_stage)">
                {{ fgs.growing_stage_name }}
              </span>
              <input v-if="! isVariableStage(fgs.growing_stage)" :name="'growing_stage_name[' + (idx + 1) + ']'" type="hidden" v-model="fgs.growing_stage_name">
            </td>
            <td>
              <input v-if="needToSelectLabelColor(fgs.growing_stage)" class="form-control" :name="'label_color['+ (idx + 1) +']'" type="color" v-model="fgs.label_color">
              <span v-else>-</span>
            </td>
            <td>
              <input class="form-control ime-inactive" :name="'growing_term[' + (idx + 1) + ']'" maxlength="4" type="number" required v-model="fgs.growing_term">&nbsp;&nbsp;日
            </td>
            <td>
              <select class="form-control text-right" :name="'number_of_holes[' + (idx + 1) + ']'" v-model="fgs.number_of_holes" required>
                <option value=""></option>
                <option v-for='(fp, index) in factoryPanels' :key="index" :value='fp.number_of_holes'>{{ fp.number_of_holes }}</option>
              </select>
            </td>
            <td>
              <template v-if="needToInputYieldRate(fgs.growing_stage)">
                <input class="form-control ime-inactive text-right" type="number" :name="'yield_rate[' + (idx + 1) + ']'" v-model="fgs.yield_rate" required>&nbsp;&nbsp;％
              </template>
              <span v-else>-</span>
            </td>
            <td>
              <select v-if="needToSelectCyclePattern(fgs.growing_stage)" class="form-control" :name="'cycle_pattern_sequence_number[' + (idx + 1) + ']'" v-model="fgs.cycle_pattern_sequence_number">
                <option value=""></option>
                <option v-for="(fcp, idx) in factoryCyclePatterns" :key="idx" :value="fcp.sequence_number">{{ fcp.cycle_pattern_name }}</option>
              </select>
              <span v-else>-</span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: ['factoryPanels', 'factoryCyclePatterns', 'currentFactoryGrowingStages', 'growingStage', 'canSaveFactory'],
  data: function() {
    const _ = require('lodash')
    return {
      factoryGrowingStages: _.cloneDeep(this.currentFactoryGrowingStages)
    }
  },
  created: function () {
    if (! this.factoryGrowingStages) {
      this.factoryGrowingStages = []
      for (const [label, value] of Object.entries(this.growingStage.growing_stages)) {
        if (this.growingStage.disabled_to_save.includes(value)) {
          continue
        }

        this.factoryGrowingStages.push({
          growing_stage: value,
          growing_stage_name: value === this.growingStage.variable_stage ? '' : label,
          label_color: null,
          growing_term: null,
          number_of_holes: null,
          yield_rate: 0,
          cycle_pattern_sequence_number: null
        })
      }
    }

    for (const [idx, fgs] of this.factoryGrowingStages.entries()) {
      this.factoryGrowingStages[idx].label_color = fgs.label_color ? '#' + fgs.label_color : null
      this.factoryGrowingStages[idx].yield_rate = fgs.yield_rate * 100
    }
  },
  methods: {
    getLableOfGrowingStage: function (stage) {
      let stage_label = ''
      for (const [label, value] of Object.entries(this.growingStage.growing_stages)) {
        if (stage === value) {
          stage_label = label
        }
      }

      return stage_label
    },
    canAppendStage: function (stage) {
      return this.growingStage.can_append_stage.includes(stage)
    },
    canRemoveStage: function (stage) {
      return this.growingStage.can_remove_stage.includes(stage)
    },
    isVariableStage: function (stage) {
      return stage === this.growingStage.variable_stage
    },
    needToSelectLabelColor: function (stage) {
      return this.growingStage.need_label_color.includes(stage)
    },
    needToInputYieldRate: function (stage) {
      return this.growingStage.need_yield_rate.includes(stage)
    },
    needToSelectCyclePattern: function (stage) {
      return this.growingStage.need_cycle_pattern.includes(stage)
    },
    addStage: function (idx) {
      for (const value of Object.values(this.growingStage.growing_stages)) {
        if (value === this.growingStage.variable_stage) {
          this.factoryGrowingStages.splice(idx + 1, 0, {
            growing_stage: value,
            growing_stage_name: '',
            label_color: null,
            growing_term: null,
            number_of_holes: null,
            yield_rate: null,
            cycle_pattern_sequence_number: null
          })
        }
      }
    },
    removeStage: function (idx) {
      this.factoryGrowingStages.splice(idx, 1)
    }
  }
}
</script>
