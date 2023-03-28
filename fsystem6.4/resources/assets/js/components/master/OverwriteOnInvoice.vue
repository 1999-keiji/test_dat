<style scoped>
  .overwrite-on-invoice {
    margin-top: 15px;
  }
  #caption {
    background-color: #c3d69b;
    border: solid 1px #4F6228;
    color: #000000;
  }
</style>

<template>
  <div class="overwrite-on-invoice">
    <div class="row">
      <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
        <div class="row form-group">
          <label id="caption" class="col-md-5 col-sm-5 col-md-offset-1 col-sm-offset-1 control-label">
            請求書表示情報
          </label>
          <div class="col-md-6 col-sm-6">
            <label class="checkbox-inline">
              <input type="checkbox" name="overwrite_on_invoice" value="1" v-model="overwriteOnInvoice" @change="toggleForm">請求元を変更
            </label>
          </div>
        </div>
      </div>
    </div>
    <div v-if="overwriteOnInvoice">
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="invoice_corporation_name" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              会社名
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="invoice_corporation_name"
                class="form-control ime-active"
                :class="{'has-error': 'invoice_corporation_name' in errors}"
                name="invoice_corporation_name"
                type="text"
                maxlength="50"
                v-model="corporationName">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="invoice_postal_code" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              郵便番号
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="invoice_postal_code"
                class="form-control ime-inactive"
                :class="{'has-error': 'invoice_postal_code' in errors}"
                name="invoice_postal_code"
                maxlength="10"
                type="text"
                v-model="postalCode">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="invoice_address" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              住所
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="invoice_address"
                class="form-control ime-active"
                :class="{'has-error': 'invoice_address' in errors}"
                maxlength="50"
                name="invoice_address"
                type="text"
                v-model="address">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="invoice_phone_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              電話番号
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="invoice_phone_number"
                class="form-control ime-inactive"
                :class="{'has-error': 'invoice_phone_number' in errors}"
                name="invoice_phone_number"
                maxlength="20"
                type="text"
                v-model="phoneNumber">
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-5 col-sm-5 col-xs-6 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="invoice_fax_number" class="col-md-4 col-sm-4 col-md-offset-1 col-sm-offset-1 control-label">
              FAX番号
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="invoice_fax_number"
                class="form-control ime-inactive"
                :class="{'has-error': 'invoice_fax_number' in errors}"
                maxlength="15"
                name="invoice_fax_number"
                type="text"
                v-model="faxNumber">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    factory: {
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
    }
  },
  data: function () {
    return {
      overwriteOnInvoice: this.oldParams.overwrite_on_invoice || this.factory.overwrite_on_invoice || false,
      corporationName: this.oldParams.invoice_corporation_name || this.factory.invoice_corporation_name,
      postalCode: this.oldParams.invoice_postal_code || this.factory.invoice_postal_code,
      address: this.oldParams.invoice_address || this.factory.invoice_address,
      phoneNumber: this.oldParams.invoice_phone_number || this.factory.invoice_phone_number,
      faxNumber: this.oldParams.invoice_fax_number || this.factory.invoice_fax_number
    }
  },
  methods: {
    toggleForm: function () {
      if (this.overwriteOnInvoice) {
        if (! this.corporationName) {
          this.corporationName = $('#factory_name').val()
        }
        if (! this.postalCode) {
          this.postalCode = $('#postal_code').val()
        }
        if (! this.address) {
          this.address = $('#address').val()
        }
        if (! this.phoneNumber) {
          this.phoneNumber = $('#phone_number').val()
        }
        if (! this.faxNumber) {
          this.faxNumber = $('#fax_number').val()
        }
      }
    }
  }
}
</script>
