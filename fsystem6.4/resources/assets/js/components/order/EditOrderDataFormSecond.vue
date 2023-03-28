<template>
  <div>
    <div class="row">
      <div class="col-md-2 col-sm-2 col-xs-3 col-md-offset-1 col-sm-offset-1 edit-inner-header">
        <h5>出荷情報</h5>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="transport_company_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            運送会社
          </label>
          <div class="col-md-7 col-sm-7">
            <select class="form-control" :class="{ 'has-error': 'transport_company_code' in errors }" name="transport_company_code" v-model="transportCompanyCode" @change="getCollectionTimes">
                <option value=""></option>
                <option
                  v-for="tc in transportCompanies"
                  :key="tc.transport_company_code"
                  :value="tc.transport_company_code">
                  {{ tc.transport_company_abbreviation }}
                </option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="fixed_shipping_by" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            出荷確定者
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.fixed_shipping_by }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="collection_time_sequence_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            集荷時間
          </label>
          <div class="col-md-7 col-sm-7">
            <select
              class="form-control"
              :class="{ 'has-error': 'collection_time_sequence_number' in errors }"
              name="collection_time_sequence_number"
              v-model="collectionTimeSequenceNumber"
              :readonly="disabledToSelectCollectionTime">
              <option value=""></option>
              <option v-for="ct in collectionTimes" :key="ct.sequence_number" :value="ct.sequence_number">{{ ct.collection_time }}</option>
            </select>
          </div>
        </div>
      </div>
      <div class="col-md-5 col-sm-5 col-xs-6">
        <div class="row form-group">
          <label for="fixed_shipping_at" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            出荷確定日時
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.fixed_shipping_at }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label for="fixed_shipping_sharing_flag" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
            出荷実績連携
          </label>
          <div class="col-md-7 col-sm-7">
            <span class="shown_label">{{ order.fixed_shipping_sharing_flag.label }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    order: {
      type: Object,
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
    transportCompanies: {
      type: Array,
      required: true
    }
  },
  data: function () {
    return {
      transportCompanyCode: this.oldParams.transport_company_code || this.order.transport_company_code || '',
      collectionTimeSequenceNumber: this.oldParams.collection_time_sequence_number || this.order.collection_time_sequence_number || '',
      collectionTimes: []
    }
  },
  created: function () {
    if (this.transportCompanyCode) {
      this.getCollectionTimes()
    }
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
          transport_company_code: this.transportCompanyCode
        }
      })
        .then(response => {
          this.collectionTimes = response.data
          if (this.disabledToSelectCollectionTime) {
            alert('集荷時間が未登録の運送会社です。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
