<style scoped>
  table#disposed-stocks-table {
    margin-top: 1.5em;
  }

  table#disposed-stocks-table>thead>tr>th.disposal-quantity {
    width: 7.5%;
  }
</style>

<template>
  <table id="disposed-stocks-table" class="table table-color-bordered table-more-condensed">
    <thead>
      <tr>
        <th>品種</th>
        <th>商品規格</th>
        <th>基準重量</th>
        <th>収穫日</th>
        <th>状態</th>
        <th>保管数量</th>
        <th>在庫数量</th>
        <th class="disposal-quantity">廃棄数量</th>
        <th>廃棄重量</th>
        <th>廃棄日</th>
        <th>備考</th>
      </tr>
    </thead>
    <tbody>
      <template v-for="species in speciesList">
        <template v-for="(ps, idx) in species.packaging_styles">
          <input-disposal-quantity
            v-for="(s, stockIdx) in ps.stocks"
            :key="s.stock_id"
            :species-name="species.species_name"
            :packaging-style="ps"
            :input-group-list="inputGroupList"
            :first-of-species="idx === 0 && stockIdx === 0"
            :count-of-stocks="species.count"
            :first-of-packaging-style="stockIdx === 0"
            :count-of-stocks-per-packaging-style="ps.stocks.length"
            :stock="s"
            :stock-status-list="stockStatusList"
            :old-params="Object.keys(oldParams).length !== 0 ? oldParams[s.stock_id] : {}">
          </input-disposal-quantity>
        </template>
      </template>
    </tbody>
  </table>
</template>

<script>
export default {
  props: {
    speciesList: {
      type: Array,
      required: true
    },
    inputGroupList: {
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
  }
}
</script>
