<template>
  <tr>
    <td v-if="firstOfSpecies" :rowspan="countOfStocks" class="text-left">{{ speciesName }}</td>
    <td v-if="firstOfPackagingStyle" :rowspan="countOfStocksPerPackagingStyle" class="text-left">
      {{ packagingStyle.number_of_heads }}株
      {{ packagingStyle.weight_per_number_of_heads }}g
      {{ inputGroupList[packagingStyle.input_group] }}
    </td>
    <td v-if="firstOfPackagingStyle" :rowspan="countOfStocksPerPackagingStyle" class="text-right">
      {{ packagingStyle.weight_per_number_of_heads }}g
    </td>
    <td>
      {{ stock.harvesting_date.date }}({{ stock.harvesting_date.day_of_the_week_ja }})
      <input :name="'stocks[' + stock.stock_id + '][harvesting_date]'" type="hidden" :value="stock.harvesting_date.date">
    </td>
    <td>{{ stockStatusList[stock.stock_status] }}</td>
    <td class="text-right">
      {{ stock.stock_quantity | formatNumber }}
      <input :name="'stocks[' + stock.stock_id + '][stock_quantity]'" type="hidden" :value="stock.stock_quantity">
    </td>
    <td class="text-right">{{ subtractedQuantity | formatNumber }}</td>
    <td>
      <input-number-with-formatter
        :value="disposalQuantity | formatNumber"
        :attr-name="'stocks[' + stock.stock_id + '][disposal_quantity]'"
        :max-length="9"
        @update-value="updateDisposalQuantity">
      </input-number-with-formatter>
    </td>
    <td class="text-right">
      {{ disposalWeight | formatNumber }}g
      <input :name="'stocks[' + stock.stock_id + '][disposal_weight]'" type="hidden" :value="disposalWeight">
    </td>
    <td>
      <datepicker-ja
        :attr-name="'stocks[' + stock.stock_id + '][disposal_at]'"
        :date="disposalAt"
        :allow-empty="true">
      </datepicker-ja>
    </td>
    <td>
      <input class="form-control" :name="'stocks[' + stock.stock_id + '][disposal_remark]'" type="text" :value="disposalRemark">
      <input class="form-control" :name="'stocks[' + stock.stock_id + '][updated_at]'" type="hidden" :value="stock.updated_at">
    </td>
  </tr>
</template>

<script>
export default {
  props: {
    speciesName: {
      type: String,
      required: true
    },
    packagingStyle: {
      type: Object,
      required: true
    },
    inputGroupList: {
      type: Object,
      required: true
    },
    firstOfSpecies: {
      type: Boolean,
      required: true
    },
    countOfStocks: {
      type: Number,
      required: true
    },
    firstOfPackagingStyle: {
      type: Boolean,
      required: true,
    },
    countOfStocksPerPackagingStyle: {
      type: Number,
      required: true
    },
    stock: {
      type: Object,
      required: true
    },
    stockStatusList: {
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
      disposalQuantity: this.oldParams.disposal_quantity || this.stock.disposal_quantity,
      disposalAt: this.oldParams.disposal_at || this.stock.disposal_at,
      disposalRemark: this.oldParams.disposal_remark || this.stock.disposal_remark
    }
  },
  computed: {
    subtractedQuantity: function () {
      return this.stock.stock_quantity - (this.disposalQuantity || 0)
    },
    disposalWeight: function () {
      return (this.disposalQuantity || 0) * this.packagingStyle.weight_per_number_of_heads
    }
  },
  filters: {
    formatNumber: function (number) {
      const number_format = require('locutus/php/strings/number_format')
      return number_format(number)
    }
  },
  methods: {
    updateDisposalQuantity: function (disposalQuantity) {
      this.disposalQuantity = disposalQuantity
    }
  }
}
</script>
