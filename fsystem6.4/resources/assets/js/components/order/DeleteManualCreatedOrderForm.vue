<template>
  <form ref="form" :action="routeAction" method="POST">
    <input type="hidden" name="updated_at" :value="order.updated_at">
    <button class="btn btn-sm btn-danger" type="button" v-on:click="deleteProduct($event)">削除</button>
    <input name="_token" type="hidden" :value="csrf">
    <input name="_method" type="hidden" value="DELETE">
  </form>
</template>

<script>
export default {
  props: {
    order: {
      type: Object,
      required: true
    },
    routeAction: {
      type: String,
      required: true
    }
  },
  data: () => {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    }
  },
  methods: {
    deleteProduct: function (event) {
      if (! confirm('選択したデータを削除しますか?')) {
        return false
      }

      event.target.disabled = true
      this.$refs.form.submit()
    }
  }
}
</script>
