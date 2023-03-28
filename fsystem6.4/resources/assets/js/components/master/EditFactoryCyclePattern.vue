<style scoped>
  .panel {
    margin-left: 20px;
    margin-right: 20px;
  }
  .pattern {
    width: 50%;
    display: inline-block;
  }
  .add-pattern {
    width: 20%;
    cursor: pointer;
  }
  .number-of-panels {
    width: 100%;
  }
</style>

<template>
  <div>
    <form class="form-horizontal basic-form save-data-form" :action="actionUpdate" method="POST">
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="sequence_number" class="col-md-5 col-sm-5 control-label">サイクルパターン</label>
            <div class="col-md-7 col-sm-7">
              <select
                id="sequence_number"
                class="form-control"
                name="sequence_number"
                v-model="cyclePatternSequenceNumber"
                @change="changeFactoryCyclePattern">
                <option :value="null">新規サイクルパターン</option>
                <option v-for="fcp in factoryCyclePatterns" :key="fcp.sequence_number" :value="fcp.sequence_number">
                  {{ fcp.cycle_pattern_name }}
                </option>
              </select>
            </div>
          </div>
        </div>
        <div v-if="canSaveFactory" class="col-md-5 col-sm-5 col-xs-6">
          <button
            class="btn btn-default btn-lg save-button pull-right delete-data btn-danger"
            type="button"
            @click="deleteFactoryCyclePattern">
            <i class="fa fa-remove"></i> 削除
          </button>
          <button class="btn btn-default btn-lg save-button pull-right save-data" type="button">
            <i class="fa fa-save"></i> 保存
          </button>
        </div>
      </div>
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="row">
            <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
              <div class="row form-group">
                <label for="cycle_pattern_name" class="col-md-5 col-sm-5 control-label required">
                  サイクルパターン名<span class="required-mark">*</span>
                </label>
                <div class="col-md-7 col-sm-7">
                  <input
                    id="cycle_pattern_name"
                    class="form-control ime-active"
                    :class="{'has-error': 'cycle_pattern_name' in errors}"
                    name="cycle_pattern_name"
                    maxlength="50"
                    type="text"
                    :disabled="! canSaveFactory"
                    v-model="cyclePatternName"
                    required>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-7 col-sm-7 col-xs-7 table-responsive">
              <table class="table table-color-bordered">
                <tbody>
                  <tr>
                    <th class="col-md-2 col-sm-2 col-xs-2"></th>
                    <th v-for="(label, value) in inputtableDayOfTheWeeks" :key="value" class="col-md-1 col-sm-1 col-xs-1">
                      {{ label }}
                    </th>
                    <th class="col-md-1 col-sm-1 col-xs-1">計</th>
                  </tr>
                  <tr v-for="(fcpi, index) in factoryCyclePatternItems" :key="index">
                    <th>
                      <input
                        class="form-control text-center ime-inactive pattern"
                        :class="{'has-error': 'pattern.' + index in errors}"
                        type='text'
                        maxlength="1"
                        :name="'pattern[' + index + ']'"
                        :disabled="! canSaveFactory"
                        v-model="fcpi.pattern">
                      <i v-if="canSaveFactory" class="fa fa-plus add-pattern" @click="addFactoryCyclePatternItem(index)"></i>
                      <i v-if="canSaveFactory" class="fa fa-minus add-pattern" @click="deleteFactoryCyclePatternItem(index)"></i>
                    </th>
                    <td v-for="value in Object.keys(inputtableDayOfTheWeeks)" :key="value">
                      <input
                        class="form-control text-right ime-inactive number-of-panels"
                        :class="{'has-error': 'number_of_panels.' + index + '.' + (value % 7) in errors}"
                        type='text'
                        :name="'number_of_panels[' + index + '][' + (value % 7) + ']'"
                        :disabled="! canSaveFactory"
                        v-model.number.lazy="fcpi.number_of_panels[value % 7]"/>
                    </td>
                    <td class="text-right">{{ totalPanels.perPattern(index) }}</td>
                  </tr>
                  <tr>
                    <th>計</th>
                    <td v-for="value in Object.keys(inputtableDayOfTheWeeks)" :key="value" class="text-right">
                      {{ totalPanels.perDayOfTheWeek(value) }}
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="PATCH">
    </form>
    <form id="delete-factory-cycle-pattern-form" method="POST">
      <input name="_token" type="hidden" :value="csrf">
      <input name="_method" type="hidden" value="DELETE">
    </form>
  </div>
</template>

<script>
export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    dayOfTheWeeks: {
      type: Object,
      required: true
    },
    workingDayOfTheWeeks: {
      type: Array,
      required: true
    },
    factoryCyclePatterns: {
      type: Array,
      required: true
    },
    actionUpdate: {
      type: String,
      required: true
    },
    actionDelete: {
      type: String,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    },
    canSaveFactory: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      cyclePatternSequenceNumber: this.oldParams.sequence_number || null,
      cyclePatternName: this.oldParams.cycle_pattern_name || null,
      factoryCyclePatternItems: []
    }
  },
  created: function () {
    if (this.errors.length === 0) {
      const factoryCyclePatternItem = {pattern: null, number_of_panels: {}}
      for (const dayOfTheWeek of Object.values(this.workingDayOfTheWeeks)) {
        factoryCyclePatternItem.number_of_panels[dayOfTheWeek % 7] = 0
      }

      this.factoryCyclePatternItems.push(factoryCyclePatternItem)
    }

    if (this.errors.length !== 0) {
      for (const [index, pattern] of this.oldParams.pattern.entries()) {
        const factoryCyclePatternItem = {pattern, number_of_panels: {}}
        for (const [dayOfTheWeek, numberOfPanels] of Object.entries(this.oldParams.number_of_panels[index])) {
          const casted = parseInt(numberOfPanels)
          factoryCyclePatternItem.number_of_panels[dayOfTheWeek] = Number.isNaN(casted) ? numberOfPanels : casted
        }

        this.factoryCyclePatternItems.push(factoryCyclePatternItem)
      }
    }
  },
  computed: {
    inputtableDayOfTheWeeks: function () {
      const inputtableDayOfTheWeeks = Object.keys(this.factoryCyclePatternItems[0].number_of_panels)

      const dayOfTheWeeks = {}
      for (const [value, label] of Object.entries(this.dayOfTheWeeks)) {
        if (inputtableDayOfTheWeeks.includes(String(value % 7))) {
          dayOfTheWeeks[value] = label
        }
      }

      return dayOfTheWeeks
    },
    totalPanels: function () {
      return {
        perPattern: (index) => {
          return Object.values(this.factoryCyclePatternItems[index].number_of_panels)
            .reduce((total, numberOfPanels) => {
              if (Number.isInteger(numberOfPanels)) {
                total += numberOfPanels
              }

              return total
            })
        },
        perDayOfTheWeek: (dayOfTheWeek) => {
          return this.factoryCyclePatternItems
            .map(fspi => fspi.number_of_panels[dayOfTheWeek % 7])
            .reduce((total, numberOfPanels) => {
              if (Number.isInteger(numberOfPanels)) {
                total += numberOfPanels
              }

              return total
            })
        }
      }
    }
  },
  methods: {
    changeFactoryCyclePattern: function () {
      if (this.cyclePatternSequenceNumber) {
        axios.get('/api/get-factory-cycle-pattern-items', {
          params: {
            factory_code: this.factory.factory_code,
            cycle_pattern_sequence_number: this.cyclePatternSequenceNumber
          }
        })
          .then(response => {
            this.cyclePatternName = response.data.cycle_pattern_name
            this.factoryCyclePatternItems = response.data.factory_cycle_pattern_items
          })
          .catch(() => {
            alert('工場サイクルパターン詳細の取得に失敗しました。しばらくお待ちください。')
          })
      }

      if (! this.cyclePatternSequenceNumber) {
        this.cyclePatternName = null
        if (confirm('現在表示している値を元に新規サイクルパターンを作成しますか？')) {
          return
        }

        const factoryCyclePatternItem = {pattern: null, number_of_panels: {}}
        for (const dayOfTheWeek of Object.values(this.workingDayOfTheWeeks)) {
          factoryCyclePatternItem.number_of_panels[dayOfTheWeek % 7] = 0
        }

        this.factoryCyclePatternItems = [factoryCyclePatternItem]
      }
    },
    addFactoryCyclePatternItem: function (index) {
      const factoryCyclePatternItem = {pattern: null, number_of_panels: {}}
      for (const dayOfTheWeek of Object.keys(this.inputtableDayOfTheWeeks)) {
        factoryCyclePatternItem.number_of_panels[dayOfTheWeek % 7] = 0
      }

      this.factoryCyclePatternItems.splice((index + 1), 0, factoryCyclePatternItem)
    },
    deleteFactoryCyclePatternItem: function (index) {
      if (this.factoryCyclePatternItems.length === 1) {
        alert('パターンを0件にすることはできません。')
        return
      }

      this.factoryCyclePatternItems.splice(index, 1)
    },
    deleteFactoryCyclePattern: function () {
      if (! this.cyclePatternSequenceNumber) {
        alert('サイクルパターンが選択されていません。')
        return
      }

      if (! confirm('選択したサイクルパターンを削除しますか？')) {
        return
      }

      const form = document.getElementById('delete-factory-cycle-pattern-form')
      form.action = this.actionDelete + this.cyclePatternSequenceNumber
      form.submit()
    }
  }
}
</script>
