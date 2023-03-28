<style scoped>
  .move-form {
    margin-top: 3em;
  }
  .box-with-caption {
    border: thin solid;
  }
  label.caption {
    background-color: #D7E4BD;
    border: solid 1px #4F6228;
    font-size: 1.3em;
    margin-bottom: 1em;
    padding: 3px 30px;
  }
  i.fa.fa-arrow-right {
    font-size: 5em;
    margin-top: 0.5em;
    margin-left: 0.2em;
  }
  input.has-suffix {
    width: 75%;
    display: inline-block;
  }
</style>

<template>
  <div class="row move-form">
    <div class="col-md-3 col-sm-4 col-md-offset-2 col-sm-offset-1">
      <div class="row box-with-caption">
        <label class="caption">移動元</label>
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">保管倉庫</label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ warehouse.warehouse_abbreviation }}</span>
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">数量</label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ (stock.stock_quantity - stock.disposal_quantity) | formatNumber }}</span>
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label required">
            移動開始日<span class="required-mark">*</span>
          </label>
          <div class="col-md-7 col-sm-7">
            <datepicker-ja
              attr-name="moving_start_at"
              :date="movingStartAt"
              @update-date="updateMovingStartAt">
            </datepicker-ja>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-1 col-sm-1">
      <i class="fa fa-arrow-right"></i>
    </div>
    <div class="col-md-5 col-sm-5">
      <div class="row box-with-caption">
        <label class="caption">移動先</label>
        <div class="row form-group">
          <label for="warehouse_code" class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label required">
            保管倉庫<span class="required-mark">*</span>
          </label>
          <div class="col-md-4 col-sm-5">
            <select
              id="warehouse_code"
              class="form-control"
              :class="{'has-error': 'warehouse_code' in errors}"
              name="warehouse_code"
              v-model="warehouseCode">
              <option value=""></option>
              <option v-for="fw in selectableWarehouses" :key="fw.warehouse_code" :value="fw.warehouse_code">
                {{ fw.warehouse_abbreviation }}
              </option>
            </select>
          </div>
        </div>
        <div class="row form-group">
          <label for="stock_quantity" class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label required">
            数量<span class="required-mark">*</span>
          </label>
          <div class="col-md-2 col-sm-3">
            <input-number-with-formatter
              :value="stockQuantity | formatNumber"
              attr-name="stock_quantity"
              :has-error="'stock_quantity' in errors"
              :max-length="9">
            </input-number-with-formatter>
          </div>
          <label for="lead_time" class="col-md-2 col-sm-3 control-label required">
            移動LT<span class="required-mark">*</span>
          </label>
          <div class="col-md-2 col-sm-3">
            <input
              id="lead_time"
              class="form-control text-right has-suffix"
              :class="{'has-error': 'moving_lead_time' in errors}"
              maxlength="1"
              name="moving_lead_time"
              v-model.number="movingLeadTime">日
          </div>
        </div>
        <div class="row form-group">
          <label class="col-md-3 col-sm-3 col-md-offset-1 col-sm-offset-1 control-label">
            移動完了日
          </label>
          <div class="col-md-4 col-sm-5">
            <span class="shown_label">{{ movingCompleteAt }}</span>
            <input name="moving_complete_at" type="hidden" :value="movingCompleteAt">
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const moment = require('moment')
export default {
  props: {
    stock: {
      type: Object,
      required: true
    },
    warehouse: {
      type: Object,
      required: true
    },
    factoryWarehouses: {
      type: Array,
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
    willExportMovingStockFile: {
      type: Boolean,
      required: true
    },
    exportMovingStockAction: {
      type: String,
      required: true
    }
  },
  data: function () {
    let movingStartAt = this.oldParams.moving_start_at
    if (! movingStartAt && this.stock.moving_complete_at) {
      const movingCompleteAt = moment(this.stock.moving_complete_at)
      movingStartAt = moment().utc().isBefore(movingCompleteAt) ?
        movingCompleteAt.format('YYYY/MM/DD') : moment().format('YYYY/MM/DD')
    }

    return {
      movingStartAt,
      warehouseCode: this.oldParams.warehouse_code || '',
      stockQuantity: this.oldParams.stock_quantity || '',
      movingLeadTime: this.oldParams.moving_lead_time || ''
    }
  },
  computed: {
    selectableWarehouses: function () {
      return this.factoryWarehouses.filter((w) => w.warehouse_code !== this.warehouse.warehouse_code)
    },
    movingCompleteAt: function () {
      if (this.movingStartAt === '' || this.movingLeadTime === '') {
        return ''
      }

      return moment(this.movingStartAt, 'YYYY/MM/DD').add(this.movingLeadTime, 'days').format('YYYY/MM/DD')
    }
  },
  filters: {
    formatNumber: function (number) {
      const number_format = require('locutus/php/strings/number_format')
      return number_format(number)
    }
  },
  mounted: function () {
    if (this.willExportMovingStockFile) {
      axios.get(this.exportMovingStockAction, {
        responseType: 'blob'
      })
        .then(res => {
          const link = document.createElement('a')
          link.href = window.URL.createObjectURL(new Blob([res.data]))
          link.setAttribute('download', [
            '倉庫間移動指示書', moment().format('YYYYMMDD_HHmmss')
          ].join('_') + '.xlsx')

          document.body.appendChild(link)
          link.click()
        })
    }
  },
  methods: {
    updateMovingStartAt: function (movingStartAt) {
      this.movingStartAt = movingStartAt
    }
  }
}
</script>
