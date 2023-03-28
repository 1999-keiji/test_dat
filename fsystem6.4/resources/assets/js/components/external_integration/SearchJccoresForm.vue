<style scoped>
button.btn.btn-lg,
a.btn.btn-lg {
  margin-top: 5px;
  text-align: left;
  width: 8em;
}
.radio-inline {
  padding-top: 0;
}
li{
  list-style: none;
}
li.factories-list{
  display: inline-block;
}
li.factories-list > label{
  margin: 0 8px 0 0;
}
</style>
<template lang="">
    <div class="col-md-9 col-sm-9 col-xs-10 col-md-offset-1 col-sm-offset-1">
      <table class="table table-color-bordered search-jccores-form">
        <tbody>
          <tr>
            <th class="col-md-2 col-sm-2">各工場<span class="required-mark">*</span></th>
            <td class="col-md-3 col-sm-3">
              <li class="factories-list" v-for="f in factories">
                <input type="checkbox" name="factories[]" :value="f.factory_code" v-model="isChecked">
                <label for="">{{ f.factory_abbreviation }}</label>
              </li>
            </td>
            <th class="col-md-2 col-sm-2">対象年月<span class="required-mark">*</span></th>
            <td class="col-md-3 col-sm-3">
              <input type="month" name="date_month">
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
    searchParams: {
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
  },
  data: function isChecked(){
    return {
      isChecked: checked
    }
    return {
      factories: this.oldParams.factories || this.searchParams.factories || '',
      warehouses: [],
      warehouseCode: this.oldParams.warehouse_code || this.searchParams.warehouse_code || '',
      speciesList: [],
      speciesCode: this.oldParams.species_code || this.searchParams.species_code || '',
      allocationStatus: allocationStatus
    }
  },
  created: function () {
    if (this.factoryCode) {
      this.getSpeciesWithFactoryCode()
      this.speciesCode = this.oldParams.species_code || this.searchParams.species_code
    }
  },
  computed:{
  },
  methods: {
  }
}
</script>