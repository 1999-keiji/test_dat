<template>
  <form ref="form" :action="actionExport" method="GET">
    <div class="row">
      <div class="col-md-9 col-sm-9 col-xs-9">
        <table class="table table-color-bordered table-shipment-data-export">
          <colgroup>
            <col class="col-md-2 col-sm-2 col-xs-2"><col>
            <col class="col-md-2 col-sm-2 col-xs-2"><col>
          </colgroup>
          <tbody>
            <tr>
              <th>工場<span class="required-mark">*</span></th>
              <td>
                <select
                  id="factory_code"
                  class="form-control"
                  :class="{'has-error': 'factory_code' in errors}"
                  name="factory_code"
                  v-model="factoryCode">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
                </select>
              </td>
              <th>出力ファイル<span class="required-mark">*</span></th>
              <td>
                <label v-for="(value, label) in shipmentDataExportFileList" :key="value" class="radio-inline">
                  <input type="radio" name="shipment_data_export_file" v-model="shipmentDataExportFile" :value="value">{{ label }}
                </label>
              </td>
            </tr>
            <tr v-if="shipmentDataExportFile == shipmentDataExportFileList['日別製品化率・出荷重量一覧']">
              <th>収穫日<span class="required-mark">*</span></th>
              <td>
                <datepicker-ja attr-name="harvesting_date[from]" :date="harvestingDate.from"/>&nbsp;～
                <datepicker-ja attr-name="harvesting_date[to]" :date="harvestingDate.to"/>
              </td>
            </tr>
            <tr v-if="shipmentDataExportFile == shipmentDataExportFileList['顧客別出荷実績']">
              <th>出荷日</th>
              <td>
                <datepicker-ja attr-name="shipping_date[from]" :date="shippingDate.from" :allow-empty="true" />&nbsp;～
                <datepicker-ja attr-name="shipping_date[to]" :date="shippingDate.to" :allow-empty="true" />
              </td>
              <th>納入日<span class="required-mark">*</span></th>
              <td>
                <datepicker-ja attr-name="delivery_date[from]" :date="deliveryDate.from"/>&nbsp;～
                <datepicker-ja attr-name="delivery_date[to]" :date="deliveryDate.to"/>
              </td>
            </tr>
            <tr v-if="shipmentDataExportFile == shipmentDataExportFileList['顧客別出荷実績']">
              <th>得意先</th>
              <td>
                <select
                  id="customer_code"
                  class="form-control"
                  :class="{'has-error': 'customer_code' in errors}"
                  name="customer_code"
                  v-model="customerCode">
                  <option value=""></option>
                  <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
                </select>
              </td>
              <th>エンドユーザ</th>
              <td>
                <search-master target="end_user" :code="endUser.code" :name="endUser.name" :is-invalid="'end_user_code' in errors ? '1' : '0'" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-3">
        <button class="btn btn-lg btn-default btn-excel-download pull-left remove-alert" type="submit">
          <i class="fa fa-download"></i> Excelダウンロード
        </button>
      </div>
    </div>
  </form>
</template>

<script>
const moment = require('moment')

export default {
  props: {
    actionExport: {
      type: String,
      required: true
    },
    factories: {
      type: Array,
      required: true
    },
    customers: {
      type: Array,
      required: true
    },
    shipmentDataExportFileList: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || '',
      shipmentDataExportFile: this.oldParams.shipment_data_export_file || this.shipmentDataExportFileList['日別製品化率・出荷重量一覧'],
      harvestingDate: {
        from: this.oldParams.harvesting_date ? this.oldParams.harvesting_date.from : moment().subtract(30, 'days').format('YYYY/MM/DD'),
        to: this.oldParams.harvesting_date ? this.oldParams.harvesting_date.to : moment().format('YYYY/MM/DD')
      },
      customerCode: this.oldParams.customer_code || '',
      shippingDate: {
        from: this.oldParams.shipping_date ? this.oldParams.shipping_date.from : '',
        to: this.oldParams.shipping_date ? this.oldParams.shipping_date.to : ''
      },
      deliveryDate: {
        from: this.oldParams.delivery_date ? this.oldParams.delivery_date.from : moment().subtract(30, 'days').format('YYYY/MM/DD'),
        to: this.oldParams.delivery_date ? this.oldParams.delivery_date.to : moment().format('YYYY/MM/DD')
      },
      endUser: {
        code: this.oldParams.end_user_code || '',
        name: this.oldParams.end_user_name || ''
      }
    }
  }
}
</script>
