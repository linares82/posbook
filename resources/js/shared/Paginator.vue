<template>
  <a-row>
    <a-col :span="24">
      <template v-for="link in datos.links">
        <Link
          preserve-scroll
          class="ant-pagination-item"
          :class="
            datos.current_page == link.label
              ? 'ant-pagination-item-active'
              : 'ant-pagination-item'
          "
          v-if="link.url"
          :href="link.url"
          v-html="link.label"
        ></Link>
        <span v-else v-html="link.label" class="ant-pagination-item"></span>
      </template>
    </a-col>
  </a-row>
</template>
<script>
import { Link } from "@inertiajs/inertia-vue3";
import { object } from "vue-types";
export default {
  components: {
    Link,
  } /*
  props: {
    datos,
  },*/,
  props: ["datos"],
};
</script>