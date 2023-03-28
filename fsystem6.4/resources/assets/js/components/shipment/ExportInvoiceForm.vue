<template>
  <form ref="form" :action="actionExport" method="POST" >
    <div class="row">
      <div class="col-md-8 col-sm-8 col-xs-8 col-md-offset-1 col-sm-offset-1">
        <table class="table table-color-bordered">
          <colgroup>
            <col class="col-md-2 col-sm-2 col-xs-2">
            <col class="col-md-4 col-sm-4 col-xs-4">
            <col class="col-md-2 col-sm-2 col-xs-2">
            <col class="col-md-4 col-sm-4 col-xs-4">
          </colgroup>
          <tbody>
            <tr>
              <th>工場<span class="required-mark">*</span></th>
              <td>
                <select name="factory_code" v-model="factoryCode" class="form-control" :class="{'has-error': 'factory_code' in errors}" @change="getEndUsers()">
                  <option value=""></option>
                  <option v-for="f in factories" :key="f.factory_code" :value="f.factory_code">{{ f.factory_abbreviation }}</option>
                </select>
              </td>
              <th>得意先<span class="required-mark">*</span></th>
              <td>
                <select name="customer_code" v-model="customerCode" class="form-control" :class="{'has-error': 'customer_code' in errors}" @change="getEndUsers()">
                  <option value=""></option>
                  <option v-for="c in customers" :key="c.customer_code" :value="c.customer_code">{{ c.customer_abbreviation }}</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>エンドユーザ</th>
              <td>
                <select name="end_user_code" v-model="endUserCode" class="form-control" :class="{'has-error': 'end_user_code' in errors}" :readonly="disabledToSelectEndUser">
                  <option value=""></option>
                  <option v-for="eu in endUsers" :key="eu.code" :value="eu.code">{{ eu.name }}</option>
                </select>
              </td>
              <th>年月<span class="required-mark">*</span></th>
              <td><year-monthpicker-ja name="delivery_month" :value="deliveryMonth" /></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-3 col-sm-3 col-xs-4">
        <button class="btn btn-lg btn-default btn-excel-download pull-left remove-alert" type="submit">
          <i class="fa fa-download"></i> PDFダウンロード
        </button>
      </div>
    </div>
    <input name="_token" type="hidden" :value="csrf">
    <input name="_method" type="hidden" value="POST">
  </form>
</template>

<script>
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
    oldParams: {
      type: Object,
      required: true
    },
    errors: {
      type: [Array, Object],
      required: true
    }
  },
  data: function ()  {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      factoryCode: this.oldParams.factory_code || '',
      customerCode: this.oldParams.customer_code || '',
      endUsers: [],
      endUserCode: this.oldParams.end_user_code || '',
      deliveryMonth: this.oldParams.delivery_month || '',
    }
  },
  created: function () {
    if (this.factoryCode && this.customerCode) {
      this.getEndUsers()
      this.endUserCode = this.oldParams.end_user_code
    }
  },
  computed: {
    disabledToSelectEndUser: function () {
      return this.endUsers.length === 0
    }
  },
  methods: {
    getEndUsers: function () {
      this.endUserCode = ''
      this.endUsers = []

      if (! this.factoryCode || ! this.customerCode) {
        return
      }

      axios.get('/api/search-end-users', {
        params: {
          factory_code: this.factoryCode,
          customer_code: this.customerCode,
        }
      })
        .then(response => {
          this.endUsers = response.data
          if (this.disabledToSelectEndUser) {
            alert('選択可能なエンドユーザが存在しません。')
          }
        })
        .catch(() => {
          alert('通信エラーが発生しました。しばらくお待ちください。')
        })
    }
  }
}
</script>
