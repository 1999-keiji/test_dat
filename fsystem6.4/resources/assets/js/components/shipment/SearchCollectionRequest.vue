<template>
  <table class="table table-color-bordered">
    <colgroup>
      <col class="col-md-2 col-sm-2 col-xs-2">
      <col class="col-md-5 col-sm-5 col-xs-5">
    </colgroup>
    <tbody>
      <tr>
        <th>工場<span class="required-mark">*</span></th>
        <td>
          <select class="form-control" name="factory_code" v-model="factoryCode">
            <option value=""></option>
            <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
          </select>
        </td>
        <th>得意先<span class="required-mark">*</span></th>
        <td>
          <select class="form-control" name="customer_code" v-model="customerCode">
            <option value=""></option>
            <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>エンドユーザ<span class="required-mark"></span></th>
        <td>
          <search-master target="end_user" :code="endUserCode" :name="endUserName" is-invalid="0" is-disabled="" />
        </td>
        <th>出荷日<span class="required-mark"></span></th>
        <td>
          <datepicker-ja attr-name="shipping_date" :date="shippingDate" allow-empty="true"></datepicker-ja>
        </td>
      </tr>
      <tr>
        <th>運送会社<span class="required-mark"></span></th>
        <td>
          <select id="transport_companies" class="form-control" name="transport_company_code" v-model="transportCompanyCode" @change="getCollectionTimes">
            <option value=""></option>
            <option v-for="tc in transportCompanies" :key="tc.transport_company_code" :value="tc.transport_company_code">
              {{ tc.transport_company_abbreviation }}
            </option>
          </select>
        </td>
        <th>集荷時間</th>
        <td>
          <select id="collection_time" class="form-control" name="collection_time_sequence_number" v-model="collectionTimeSequenceNumber" :readonly="disabledToSelectCollectionTime">
            <option value=""></option>
            <option v-for="ct in collectionTimes" :key="ct.sequence_number" :value="ct.sequence_number">{{ ct.collection_time }}</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ['factories', 'customers', 'transportCompanies', 'searchParams', 'oldParams'],
  data: function () {
    return {
      factoryCode: this.oldParams.factory_code || this.searchParams.factory_code,
      customerCode: this.oldParams.customer_code || this.searchParams.customer_code,
      endUserCode: this.oldParams.end_user_code || this.searchParams.end_user_code,
      endUserName: this.oldParams.end_user_name || this.searchParams.end_user_name,
      shippingDate: this.oldParams.shipping_date || this.searchParams.shipping_date,
      transportCompanyCode: this.oldParams.transport_company_code || this.searchParams.transport_company_code || '',
      collectionTimes : [],
      collectionTimeSequenceNumber: this.oldParams.collection_time_sequence_number || this.searchParams.collection_time_sequence_number || ''
    }
  },
  created: function () {
    this.getCollectionTimes()
  },
  computed: {
    disabledToSelectCollectionTime: function () {
      return this.collectionTimes.length === 0
    }
  },
  methods: {
    getCollectionTimes: function () {
      this.collectionTimes = []
      if (! this.transportCompanyCode) {
        return
      }

      axios.get('/api/get-collection-times-by-trasport-company', {
        params: {
          transport_company_code: this.transportCompanyCode,
        }
      })
        .then(response => {
          this.collectionTimes = response.data
          if (this.disabledToSelectCollectionTime) {
            alert('集荷時間が未設定の運送会社です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
