<template>
  <div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1 col-sm-offset-1">
    <table class="table table-color-bordered">
      <tbody>
        <tr>
          <th class="col-md-2 col-sm-2 col-xs-2">
            工場<span class="required-mark">*</span>
          </th>
          <td class="col-md-4 col-sm-4 col-xs-4">
            <select class="form-control" name="factory_code" v-model="factoryCode">
              <option value=""></option>
              <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
            </select>
          </td>
          <th class="col-md-2 col-sm-2 col-xs-2">
            得意先<span class="required-mark">*</span>
          </th>
          <td class="col-md-4 col-sm-4 col-xs-4">
            <select class="form-control" name="customer_code" v-model="customerCode">
              <option value=""></option>
              <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
            </select>
          </td>
        </tr>
        <tr>
          <th>
            出力ファイル<span class="required-mark">*</span>
          </th>
          <td>
            <div v-for="(value, label) in outputFileList" :key="value" class="radio-inline">
              <label>
                <input type="radio" name="output_file" :value="value" v-model="outputFile">{{ label }}
              </label>
            </div>
          </td>
          <th>
            状態<span class="required-mark">*</span>
          </th>
          <td>
            <div v-for="(value, label) in printStateList" :key="value" class="radio-inline">
              <label>
                <input type="radio" name="print_state" :value="value" v-model="printState">{{ label }}
              </label>
            </div>
          </td>
        </tr>
        <tr>
          <th>エンドユーザ</th>
          <td class="text-left">
            <search-master
              target="end_user"
              :code="endUserCode"
              :name="endUserName"
              :factory-code="factoryCode" />
          </td>
          <th>納入先</th>
          <td class="text-left">
            <search-master
              target="delivery_destination"
              :code="deliveryDestinationCode"
              :name="deliveryDestinationName"
              :factory-code="factoryCode"
              @get-selected-delivery-destination="getSelectedDeliveryDestination" />
          </td>
        </tr>
        <tr>
          <th>出荷日</th>
          <td>
            <datepicker-ja attr-name="shipping_date_from" :date="shippingDateFrom" :allow-empty="true">
            </datepicker-ja>&nbsp;～
            <datepicker-ja attr-name="shipping_date_to" :date="shippingDateTo" :allow-empty="true">
            </datepicker-ja>
          </td>
          <th>納入日</th>
          <td>
            <datepicker-ja attr-name="delivery_date_from" :date="deliveryDateFrom" :allow-empty="true">
            </datepicker-ja>&nbsp;～
            <datepicker-ja attr-name="delivery_date_to" :date="deliveryDateTo" :allow-empty="true">
            </datepicker-ja>
          </td>
        </tr>
        <tr>
          <th>注文番号</th>
          <td>
            <input name="order_number" class="form-control ime-active" :value="orderNumber" maxlength="14" type="text">
          </td>
          <th>BASE+注文番号</th>
          <td>
            <input name="base_plus_order_number" class="form-control ime-active base_plus_num" :value="basePlusOrderNumber" maxlength="10" type="text">
            <input name="base_plus_order_chapter_number" class="form-control ime-active base_plus_chap" :value="basePlusOrderChapterNumber" maxlength="3" type="text">
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    factories: {
      type: Array,
      required: true
    },
    customers: {
      type: Array,
      required: true
    },
    outputFileList: {
      type: Object,
      required: true
    },
    printStateList: {
      type: Object,
      required: true
    },
    searchParams: {
      type: Object,
      required: true
    },
    oldParams: {
      type: Object,
      required: true
    }
  },
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code,
      customerCode: this.oldParams.customer_code || this.searchParams.customer_code,
      outputFile: this.oldParams.output_file || this.searchParams.output_file || this.outputFileList['納品書・受領書'],
      printState: this.oldParams.print_state || this.searchParams.print_state || this.printStateList['すべて'],
      endUserCode: this.oldParams.end_user_code || this.searchParams.end_user_code,
      endUserName: this.oldParams.end_user_name || this.searchParams.end_user_name,
      deliveryDestinationCode: this.oldParams.delivery_destination_code || this.searchParams.delivery_destination_code,
      deliveryDestinationName: this.oldParams.delivery_destination_name || this.searchParams.delivery_destination_name,
      shippingDateFrom: this.oldParams.shipping_date_from || this.searchParams.shipping_date_from,
      shippingDateTo: this.oldParams.shipping_date_to || this.searchParams.shipping_date_to,
      deliveryDateFrom: this.oldParams.delivery_date_from || this.searchParams.delivery_date_from,
      deliveryDateTo: this.oldParams.delivery_date_to || this.searchParams.delivery_date_to,
      orderNumber: this.oldParams.order_number || this.searchParams.order_number,
      basePlusOrderNumber: this.oldParams.base_plus_order_number || this.searchParams.base_plus_order_number,
      basePlusOrderChapterNumber: this.oldParams.base_plus_order_chapter_number || this.searchParams.base_plus_order_chapter_number
    }
  },
  methods: {
    getSelectedDeliveryDestination: function (deliveryDestination) {
      this.deliveryDestinationCode = deliveryDestination.code
      this.deliveryDestinationName = deliveryDestination.name
      this.endUserCode = deliveryDestination.end_user.code
      this.endUserName = deliveryDestination.end_user.name
    }
  }
}
</script>
