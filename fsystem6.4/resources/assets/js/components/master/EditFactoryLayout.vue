<style scoped>
  .set-panel-table {
    margin-bottom: 20px;
  }
  .set-panel-table th,
  .set-panel-table td {
    padding: 5px;
    white-space: nowrap;
  }
  .set-panel-table td.input {
    border: 1px solid #ccc;
  }

  .coordinate_panel {
    width: 30px;
    display: inline-block;
  }

  .layout-table-col {
    padding-right: 0px;
  }
  .layout-table {
    table-layout: fixed;
  }
  table.layout-table>thead>tr,
  table.layout-table>tbody>tr {
    height: 30px;
  }
  table.layout-table>tbody>tr>th,
  table.layout-table>tbody>tr>td {
    white-space: nowrap;
  }
  table.layout-table .column-name-header {
    border-bottom-color: #d7e4bd;
  }
  table.layout-table .bed-header {
    border-top-color: #d7e4bd;
  }
  .add-row {
    width: 20%;
    cursor: pointer;
  }

  div.bed-table-col {
    overflow-x: auto;
    min-height: 0.01%;
    padding-left: 0px;
  }
  table.bed-table {
    table-layout:fixed;
    width: max-content;
    max-width: none;
    float: left;
    margin-bottom: 0px;
  }
  table.bed-table .coordinate-panel-col {
    width: 90px;
  }
  table.bed-table .irradiation-col {
    width: 60px;
  }
  .irradiation{
    display: inline-block;
  }
</style>

<template>
<form class="form-horizontal basic-form save-data-form" :action="actionUpdate" method="POST">
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="number_of_floors" class="col-md-5 col-sm-5 control-label required">
          階数
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="number_of_floors"
            class="form-control text-right ime-inactive"
            :class="{'has-error': 'number_of_floors' in errors}"
            name="number_of_floors"
            maxlength="1"
            pattern="^[0-9]+$"
            type="text"
            v-model.number="numberOfFloors"
            required>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="number_of_rows" class="col-md-5 col-sm-5 control-label required">
          段数
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="number_of_rows"
            class="form-control text-right ime-inactive"
            :class="{'has-error': 'number_of_rows' in errors}"
            name="number_of_rows"
            maxlength="2"
            type="text"
            pattern="^[0-9]+$"
            v-model.number="numberOfRows"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button v-if="canSaveFactory" class="btn btn-default btn-lg save-button pull-right save-data" type="button">
        <i class="fa fa-save"></i> 保存
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 col-sm-6 col-xs-6">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="number_of_columns" class="col-md-5 col-sm-5 control-label required">
          列数
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="number_of_columns"
            class="form-control text-right ime-inactive"
            :class="{'has-error': 'number_of_columns' in errors}"
            name="number_of_columns"
            maxlength="2"
            type="text"
            pattern="^[0-9]+$"
            v-model.number="numberOfColumns"
            required>
        </div>
      </div>
      <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="number_of_circulation" class="col-md-5 col-sm-5 control-label required">
          循環数
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            id="number_of_circulation"
            class="form-control text-right ime-inactive"
            :class="{'has-error': 'number_of_circulation' in errors}"
            name="number_of_circulation"
            maxlength="2"
            type="text"
            pattern="^[0-9]+$"
            v-model.number="numberOfCirculation"
            required>
        </div>
      </div>
    </div>
    <div class="col-md-5 col-sm-5 col-xs-6">
      <button v-if="canSaveFactory" class="btn btn-default btn-lg pull-left" type="button" @click="reflectLayout()">
        <i class="fa fa-rotate-right"></i> 反映
      </button>
    </div>
  </div>
  <div class="row">
    <div class="col-md-11 col-sm-11 col-xs-12">
      <table class="set-panel-table">
        <tbody v-if="canSaveFactory">
          <tr>
            <td>全て同じパネル数にする</td>
            <td class="input">
              <input
                id="x_coordinate_panel"
                class="form-control coordinate_panel text-right ime-inactive"
                name="x_coordinate_panel"
                type="text"
                maxlength="2"
                pattern="^[0-9]+$"
                v-model.number="xCoordinatePanel">
              ×
              <input
                id="y_coordinate_panel"
                class="form-control coordinate_panel text-right ime-inactive"
                name="y_coordinate_panel"
                type="text"
                maxlength="2"
                pattern="^[0-9]+$"
                v-model.number="yCoordinatePanel">
            </td>
            <td>
              <button class="btn btn-default btn-lg pull-left" type="button" @click="reflectPanelCoordination">
                <i class="fa fa-rotate-right"></i> 反映
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="col-md-2 col-sm-2 col-xs-2 layout-table-col">
        <table class="table table-color-bordered layout-table">
          <colgroup>
            <col class="col-md-4 col-sm-4 col-xs-4" >
            <col class="col-md-8 col-sm-8 col-xs-8" >
          </colgroup>
          <tbody>
            <tr>
              <th colspan="2" class="column-name-header"></th>
            </tr>
            <tr>
              <th colspan="2" class="bed-header"></th>
            </tr>
            <template v-for="(rows, floor_index) in reversedFloorAndRow">
              <template v-for="(factoryBeds, row_index) in rows.rows" >
                <tr :key="[floor_index, row_index].join('-')">
                  <th v-if="row_index === 0" :rowspan="rows.rows.length">
                    {{ rows.floor }}階
                  </th>
                  <th>
                    {{ factoryBeds.row }}段
                    <i v-if="canSaveFactory" class="fa fa-plus add-row" @click="addRow(rows.floor, factoryBeds.row)"></i>
                    <i v-if="canSaveFactory" class="fa fa-minus add-row" @click="removeRow(rows.floor, factoryBeds.row)"></i>
                  </th>
                </tr>
              </template>
            </template>
            <tr>
              <th colspan="2">循環</th>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-10 col-sm-10 col-xs-10 bed-table-col">
        <table class="table table-color-bordered layout-table bed-table">
          <colgroup>
            <template v-for="fc in updatedFactoryColumns">
              <col :key="fc.column + '-bed'" class="coordinate-panel-col">
              <col :key="fc.column + '-irradiation'" class="irradiation-col">
            </template>
          </colgroup>
          <tbody>
            <tr>
              <th colspan="2" v-for="fc in updatedFactoryColumns" :key="fc.column">
                <input
                  class="form-control ime-active"
                  :class="{'has-error': 'factory_columns.' + fc.column + '.column_name' in errors}"
                  :name="'factory_columns[' + fc.column + '][column_name]'"
                  type="text"
                  maxlength="5"
                  required
                  v-model="fc.column_name">
              </th>
            </tr>
            <tr>
              <template v-for="fc in updatedFactoryColumns">
                <th :key="fc.column + '-bed'">ベッド</th>
                <th :key="fc.column + '-irradiation'">照射</th>
              </template>
            </tr>
            <template v-for="(rows, floor_index) in reversedFloorAndRow">
              <template v-for="(factoryBeds, row_index) in rows.rows">
                <tr :key="[floor_index, row_index].join('-')">
                  <template v-for="fb in factoryBeds.factoryBeds">
                    <td :key="[row_index, fb.column, 'coordination'].join('-')">
                      <input
                        class="form-control coordinate_panel x_coordinate_panel text-right ime-inactive"
                        :class="{'has-error': 'factory_beds.' + rows.floor + '.' + factoryBeds.row + '.' + fb.column + '.x_coordinate_panel' in errors}"
                        :name="'factory_beds[' + rows.floor + '][' + factoryBeds.row + '][' + fb.column + '][x_coordinate_panel]'"
                        type="text"
                        maxlength="2"
                        pattern="^[0-9]+$"
                        v-model="fb.x_coordinate_panel">
                      ×
                      <input
                        class="form-control coordinate_panel y_coordinate_panel text-right ime-inactive"
                        :class="{'has-error': 'factory_beds.' + rows.floor + '.' + factoryBeds.row + '.' + fb.column + '.y_coordinate_panel' in errors}"
                        :name="'factory_beds[' + rows.floor + '][' + factoryBeds.row + '][' + fb.column + '][y_coordinate_panel]'"
                        type="text"
                        maxlength="2"
                        pattern="^[0-9]+$"
                        v-model="fb.y_coordinate_panel">
                    </td>
                    <td :key="[row_index, fb.column, 'irradiation'].join('-')">
                      <input
                        class="form-control irradiation text-center ime-inactive"
                        :class="{'has-error': 'factory_beds.' + rows.floor + '.' + factoryBeds.row + '.' + fb.column + '.irradiation' in errors}"
                        :name="'factory_beds[' + rows.floor + '][' + factoryBeds.row + '][' + fb.column + '][irradiation]'"
                        type="text"
                        maxlength="5"
                        pattern="^[0-9]+$"
                        v-model="fb.irradiation">
                    </td>
                  </template>
                </tr>
              </template>
            </template>
            <tr>
              <template v-for="c in updatedCirculations">
                <th :key="c.circulation" v-if="c.count !== 0" :colspan="(c.count * 2)">
                  循環{{ c.circulation }}
                </th>
              </template>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row" v-for="c in updatedCirculations" :key="c.circulation">
    <div class="col-md-6 col-sm-6 col-xs-6">
      <div class="col-md-6 col-sm-6 col-xs-6">
        <label for="number_of_rows" class="col-md-5 col-sm-5 control-label required">
          循環{{ c.circulation }}
          <span class="required-mark">*</span>
        </label>
        <div class="col-md-7 col-sm-7">
          <input
            class="form-control text-right ime-inactive"
            :class="{'has-error': 'circulations[' + c.circulation + ']' in errors}"
            :name="'circulations[' + c.circulation + ']'"
            type="text"
            maxlength="2"
            pattern="^[0-9]+$"
            v-model.number.lazy="c.count">
        </div>
      </div>
    </div>
  </div>
  <input name="number_of_columns_confirmation" type="hidden" :value="sumOfCountOfCirculations">
  <input name="updated_at" type="hidden" :value="this.factory.updated_at">
  <input name="_token" type="hidden" :value="csrf">
  <input name="_method" type="hidden" value="PATCH">
  <input v-if="! canSaveFactory" id="can-save-data" type="hidden" value="0">
</form>
</template>

<script>
const _ = require('lodash')

export default {
  props: {
    factory: {
      type: Object,
      required: true
    },
    factoryBeds: {
      type: [Array, Object],
      required: true
    },
    factoryColumns: {
      type: Array,
      required: true
    },
    circulations: {
      type: Array,
      required: true
    },
    defaultXCoordinatePanel: {
      type: Number,
      required: true
    },
    defaultYCoordinatePanel: {
      type: Number,
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
    actionUpdate: {
      type: String,
      required: true
    },
    canSaveFactory: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    let numberOfRows = this.oldParams.number_of_rows
    if (! numberOfRows) {
      if (this.factory.number_of_floors !== 0) {
        numberOfRows = Math.floor(this.factory.number_of_rows / this.factory.number_of_floors)
      }
      if (this.factory.number_of_floors === 0) {
        numberOfRows = 0
      }
    }

    let updatedFactoryBeds = _.clone(this.factoryBeds)
    if (this.oldParams.factory_beds) {
      updatedFactoryBeds = {}
      for (const [floor, rows] of Object.entries(this.oldParams.factory_beds)) {
        updatedFactoryBeds[floor] = {}
        for (const [row, columns] of Object.entries(rows)) {
          updatedFactoryBeds[floor][row] = []
          for (const [column, factoryBed] of Object.entries(columns)) {
            updatedFactoryBeds[floor][row].push({
              floor,
              row,
              column,
              x_coordinate_panel: factoryBed.x_coordinate_panel,
              y_coordinate_panel: factoryBed.y_coordinate_panel,
              irradiation: factoryBed.irradiation
            })
          }
        }
      }
    }

    let updatedFactoryColumns = _.clone(this.factoryColumns)
    if (this.oldParams.factory_columns) {
      updatedFactoryColumns = []
      for (const [column, factoryColumn] of Object.entries(this.oldParams.factory_columns)) {
        updatedFactoryColumns.push({
          column,
          column_name: factoryColumn.column_name
        })
      }
    }

    let updatedCirculations = this.circulations
    if (this.oldParams.circulations) {
      updatedCirculations = []
      for (const [circulation, count] of Object.entries(this.oldParams.circulations)) {
        updatedCirculations.push({circulation, count})
      }
    }

    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      numberOfFloors: parseInt(this.oldParams.number_of_floors || this.factory.number_of_floors),
      numberOfRows: parseInt(numberOfRows),
      numberOfColumns: parseInt(this.oldParams.number_of_columns || this.factory.number_of_columns),
      numberOfCirculation: parseInt(this.oldParams.number_of_circulation || this.factory.number_of_circulation),
      xCoordinatePanel: parseInt(this.oldParams.x_coordinate_panel || this.defaultXCoordinatePanel),
      yCoordinatePanel: parseInt(this.oldParams.y_coordinate_panel || this.defaultYCoordinatePanel),
      updatedFactoryBeds,
      updatedFactoryColumns,
      updatedCirculations
    }
  },
  computed: {
    reversedFloorAndRow: function () {
      let reversed = [],
        floor = Object.values(this.updatedFactoryBeds).length,
        row = Object.values(this.updatedFactoryBeds).reduce((number_of_rows, rows) => {
          return number_of_rows + Object.values(rows).length
        }, 0)

      for (const rows of Object.values(this.updatedFactoryBeds).reverse()) {
        let reversedRows = []
        for (const factoryBeds of Object.values(rows).reverse()) {
          reversedRows.push({row, factoryBeds: _.orderBy(factoryBeds, 'column')})
          row = row - 1
        }

        reversed.push({floor, rows: reversedRows})
        floor = floor - 1
      }

      return reversed
    },
    sumOfCountOfCirculations: function () {
      return this.updatedCirculations.reduce((sum, circulation) => {
        return sum + parseInt(circulation.count)
      }, 0)
    }
  },
  methods: {
    reflectLayout: function () {
      let messages = []
      if (! _.isInteger(this.numberOfFloors)) {
        messages.push('階数には、数字を指定してください。')
      }
      if (! _.isInteger(this.numberOfRows)) {
        messages.push('段数には、数字を指定してください。')
      }
      if (! _.isInteger(this.numberOfColumns)) {
        messages.push('列数には、数字を指定してください。')
      }
      if (! _.isInteger(this.numberOfCirculation)) {
        messages.push('循環数には、数字を指定してください。')
      }
      if (messages.length !== 0) {
        alert(messages.join('\n'))
        return
      }

      this.updatedFactoryColumns = []
      for (let column = 1; column <= this.numberOfColumns; column++) {
        this.updatedFactoryColumns.push({
          column,
          column_name: column + '列',
        })
      }

      this.updatedFactoryBeds = {}
      for (let floor = 1; floor <= this.numberOfFloors; floor++) {
        this.updatedFactoryBeds[floor] = {}
        for (let row = 1; row <= this.numberOfRows; row++) {
          const genuineRow = row + (this.numberOfRows * (floor - 1))

          this.updatedFactoryBeds[floor][genuineRow] = []
          for (let column = 1; column <= this.numberOfColumns; column++) {
            this.updatedFactoryBeds[floor][genuineRow].push({
              floor,
              row: genuineRow,
              column,
              x_coordinate_panel: 0,
              y_coordinate_panel: 0,
              irradiation: ''
            })
          }
        }
      }

      this.updatedCirculations = []
      let remainder = this.numberOfColumns
      for (let circulation = 1; circulation <= this.numberOfCirculation; circulation++) {
        const count = Math.floor(this.numberOfColumns / this.numberOfCirculation)
        remainder = remainder - count
        this.updatedCirculations.push({
          circulation,
          count: count + (circulation === this.numberOfCirculation ? remainder : 0)
        })
      }
    },
    reflectPanelCoordination: function () {
      if (! (_.isInteger(this.xCoordinatePanel) && _.isInteger(this.yCoordinatePanel))) {
        alert('パネル数には、正数を指定してください。')
        return
      }

      for (let floor = 1; floor <= this.numberOfFloors; floor++) {
        for (let row = 1; row <= this.numberOfRows; row++) {
          const genuineRow = row + (this.numberOfRows * (floor - 1))
          for (let idx = 0; idx < this.numberOfColumns; idx++) {
            Vue.set(this.updatedFactoryBeds[floor][genuineRow][idx], 'x_coordinate_panel', this.xCoordinatePanel)
            Vue.set(this.updatedFactoryBeds[floor][genuineRow][idx], 'y_coordinate_panel', this.yCoordinatePanel)
          }
        }
      }

      this.$forceUpdate()
    },
    addRow: function (floor, targetRow) {
      const factoryBeds = this.updatedFactoryBeds[floor][targetRow]
      for (const floor of Object.keys(this.updatedFactoryBeds).reverse()) {
        for (const row of Object.keys(this.updatedFactoryBeds[floor]).reverse()) {
          if (parseInt(row) < targetRow) {
            continue
          }

          Vue.set(this.updatedFactoryBeds[floor], parseInt(row) + 1, this.updatedFactoryBeds[floor][row])
        }
      }

      Vue.set(this.updatedFactoryBeds[floor], targetRow, factoryBeds)
    },
    removeRow: function (floor, row) {
      if (Object.values(this.updatedFactoryBeds[floor]).length === 1) {
        alert('各階の段数を0段にすることはできません。')
        return
      }

      Vue.delete(this.updatedFactoryBeds[floor], row)
    }
  }
}
</script>
