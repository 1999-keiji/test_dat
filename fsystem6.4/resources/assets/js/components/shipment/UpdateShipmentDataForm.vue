<template>
  <tr>
    <td>
      <datepicker-ja v-if="! hadBeenShipped" :attr-name="'orders['+index+'][shipping_date]'" :date="order.shipping_date"></datepicker-ja>
      <span v-else>{{ order.shipping_date }}</span>
    </td>
    <td>
      {{ order.printing_delivery_date | formatDate }}
      <input v-if="! hadBeenShipped" type='hidden' :name="'orders['+index+'][delivery_date]'" :value="order.delivery_date">
    </td>
    <td>
      <select class="form-control" :name="'orders['+index+'][transport_company_code]'" v-model="transportCompanyCode" @change="getCollectionTimes" :disabled="hadBeenShipped">
        <option v-for="tc in transportCompanies" :key="tc.transport_company_code" :value="tc.transport_company_code">
          {{ tc.transport_company_abbreviation }}
        </option>
      </select>
    </td>
    <td>
      <select
        class="form-control"
        :name="'orders['+index+'][collection_time_sequence_number]'"
        v-model="collectionTimeSequenceNumber"
        :disabled="hadBeenShipped"
        :readonly="disabledToSelectCollectionTime">
        <option v-for="ct in updatedCollectionTimes" :key="ct.sequence_number" :value="ct.sequence_number">
          {{ ct.collection_time }}
        </option>
      </select>
    </td>
    <td>
      {{ order.order_number }}
      <input v-if="! hadBeenShipped" type='hidden' :name="'orders['+index+'][order_number]'" :value="order.order_number">
    </td>
    <td class="text-left">{{ order.end_user_abbreviation }}</td>
    <td class="text-left">{{ order.delivery_destination_abbreviation }}</td>
    <td class="text-left">{{ order.product_name }}</td>
    <td class="text-right">{{ order.order_quantity }}</td>
    <td class="text-right">{{ numberFormat(order.product_weight_per_case) }}</td>
  </tr>
</template>

<script>
const _ = require('lodash'),
  number_format = require('locutus/php/strings/number_format')

export default {
  props: {
    order: {
      type: Object,
      required: true
    },
    index: {
      type: Number,
      required: true
    },
    transportCompanies: {
      type: Array,
      required: true
    },
    collectionTimes: {
      type: Array,
      required: true
    }
  },
  data: function () {
    return {
      transportCompanyCode: this.order.transport_company_code,
      updatedCollectionTimes: _.cloneDeep(this.collectionTimes),
      collectionTimeSequenceNumber: this.order.collection_time_sequence_number
    }
  },
  watch: {
    collectionTimes: function () {
      this.reset()
    }
  },
  computed: {
    hadBeenShipped: function () {
      return this.order.had_been_shipped
    },
    disabledToSelectCollectionTime: function () {
      return this.updatedCollectionTimes.length === 0
    }
  },
  filters: {
    formatDate: function (date) {
      const moment = require('moment')
      moment.locale('ja')

      return moment(date, 'YYYY/MM/DD').format('YYYY/MM/DD (ddd)')
    }
  },
  methods: {
    getCollectionTimes: function () {
      this.collectionTimeSequenceNumber = null
      this.updatedCollectionTimes = []

      axios.get('/api/get-collection-times-by-trasport-company', {
        params: {
          transport_company_code: this.transportCompanyCode
        }
      })
        .then(response => {
          this.updatedCollectionTimes = response.data
          if (this.disabledToSelectCollectionTime) {
            alert('集荷時間の設定されていない運送会社です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    },
    reset: function () {
      this.transportCompanyCode = this.order.transport_company_code,
      this.updatedCollectionTimes = _.cloneDeep(this.collectionTimes)
      this.collectionTimeSequenceNumber = this.order.collection_time_sequence_number
    },
    numberFormat: function (number) {
      return number_format(number)
    }
  }
}
</script>
