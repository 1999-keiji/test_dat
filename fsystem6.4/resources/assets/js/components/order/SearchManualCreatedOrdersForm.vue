<template>
  <table class="table table-color-bordered">
    <tbody>
      <tr>
        <th class="col-sm-2 col-md-2 col-xs-2">
          工場
          <span class="required-mark">*</span>
        </th>
        <td class="col-sm-4 col-md-4 col-xs-4">
          <select class="form-control" name="factory_code" v-model="factoryCode">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th class="col-sm-2 col-md-2 col-xs-2">
          注文日
          <span class="required-mark">*</span>
        </th>
        <td class="col-sm-4 col-md-4 col-xs-4">
          <datepicker-ja attr-name="received_date" :date="receivedDate"></datepicker-ja>
        </td>
      </tr>
      <tr>
        <th>
          得意先
          <span class="required-mark">*</span>
        </th>
        <td>
          <select class="form-control" name="customer_code" v-model="customerCode">
            <option value=""></option>
            <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
          </select>
        </td>
        <th>
          納入先
          <span class="required-mark">*</span>
        </th>
        <td v-if="! factoryCode">
          <span>工場を選択してください</span>
        </td>
        <td v-else-if="! customerCode">
          <span>得意先を選択してください</span>
        </td>
        <td v-else>
          <search-master
            target="delivery_destination"
            :code="deliveryDestination.code"
            :name="deliveryDestination.name"
            :factory-code="factoryCode"
            :customer-code="customerCode"
            @get-selected-delivery-destination="getSelectedDeliveryDestination" />
        </td>
      </tr>
      <tr>
        <th>エンドユーザ</th>
        <td>
          <span v-if="deliveryDestination.end_user.code" class="shown_label">
            {{ deliveryDestination.end_user.name }}
          </span>
          <input type="hidden" name="end_user_code" :value="deliveryDestination.end_user.code">
          <input type="hidden" name="end_user_name" :value="deliveryDestination.end_user.name">
        </td>
        <th>
          商品
        </th>
        <td>
          <select class="form-control" name="factory_product_sequence_number" v-model="factoryProductSequenceNumber" :disabled="disabledToSelectProduct">
            <option value=""></option>
            <option v-for="fp in factoryProducts" :key="fp.factory_product_sequence_number" :value="fp.factory_product_sequence_number">{{ fp.product_name }}</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
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
      receivedDate: this.oldParams.received_date || this.searchParams.received_date,
      customerCode: this.oldParams.customer_code || this.searchParams.customer_code,
      deliveryDestination: {
        code: this.oldParams.delivery_destination_code || this.searchParams.delivery_destination_code || '',
        name: this.oldParams.delivery_destination_name || this.searchParams.delivery_destination_name || '',
        end_user : {
          code: this.oldParams.end_user_code || this.searchParams.end_user_code || '',
          name: this.oldParams.end_user_name || this.searchParams.end_user_name || ''
        }
      },
      factoryProductSequenceNumber: this.oldParams.factory_product_sequence_number || this.searchParams.factory_product_sequence_number,
      factoryProducts: []
    }
  },
  watch: {
    customerCode: function () {
      if (! this.customerCode) {
        this.endUserCode = this.endUserAbbreviation = this.deliveryDestinationCode = this.deliveryDestinationAbbreviation = null
      }
    },
    deliveryDestination: function () {
      if (this.deliveryDestination.code) {
        this.getProducts()
      }
    }
  },
  created: function () {
    if (this.factoryCode && this.deliveryDestination.code) {
      this.getProducts()
    }
  },
  computed: {
    disabledToSelectProduct: function () {
      return this.factoryProducts.length === 0
    }
  },
  methods: {
    getSelectedDeliveryDestination: function (deliveryDestination) {
      this.deliveryDestination = deliveryDestination
    },
    getProducts: function () {
      this.factoryProducts = []
      if (! this.factoryCode || ! this.deliveryDestination.code) {
        return
      }

      axios.get('/api/get-delivery-factory-products', {
        params: {
          factory_code: this.factoryCode,
          delivery_destination_code: this.deliveryDestination.code
        }
      })
        .then(response => {
          this.factoryProducts = response.data
          if (this.disabledToSelectProduct) {
            alert('指定された納入先に紐づけされた商品が存在していません。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
