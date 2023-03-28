<style scoped>
  .panel {
    margin-left: 20px;
    margin-right: 20px;
  }
  .pattern {
    width: 50%;
    display: inline-block;
  }
  .add-pattern {
    width: 20%;
    cursor: pointer;
  }
  .number-of-panels {
    width: 100%;
  }
</style>

<template>
  <div>
    <form id="delete-factory-jccores-form" :action="actionUpdate" method="POST" class="form-horizontal basic-form save-data-form">
      <div class="row">
        <div class="col-md-4 col-sm-4 col-xs-4 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="conversion-code" class="col-md-5 col-sm-5 control-label">
              工場略称
            </label>
            <div class="col-md-7 col-sm-7">
              {{ factory.factory_abbreviation }}
            </div>
          </div>
        </div>
        <div class="col-md-5 col-sm-5 col-xs-6">
          <button v-if="canSaveFactory" class="btn btn-default btn-lg save-button pull-right save-data" type="button">
            <i class="fa fa-save"></i> 保存
          </button>
        </div>
        <div class="col-md-4 col-sm-4 col-xs-4 col-md-offset-1 col-sm-offset-1">
          <div class="row form-group">
            <label for="conversion_code" class="col-md-5 col-sm-5 control-label required">
              原価計算用工場コード
              <span class="required-mark">*</span>
            </label>
            <div class="col-md-7 col-sm-7">
              <input
                id="conversion_code"
                class="form-control ime-inactive"
                :class="{'has-error': 'conversion_code' in errors}"
                name="conversion_code"
                pattern="^[A-Z]+"
                :disabled="! canSaveFactory"
                type="text"
                v-model="conversionName"
                required>
            </div>
          </div>
        </div>
      </div>
    </form>
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
    },
    actionUpdate: {
      type: String,
      required: true
    },
    canSaveFactory: {
      type: Boolean,
      required: true
    }
  },
  data: function () {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    }
  },
  created: function(){
    
  },
  methods: {
    
  }
}
</script>
