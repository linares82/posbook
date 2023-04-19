<template>
  <a-row>
    <a-col :span="24">
        <Link
          preserve-scroll
          class="ant-pagination-item"
          :href="datos.first_page_url"
        >Primera</Link>
        <Link
          preserve-scroll
          class="ant-pagination-item"
          :href="datos.prev_page_url"
        >Anterior</Link>
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
          v-show="(link.label<=4) || (link.label>=datos.last_page-3) || (datos.current_page== link.label)"
        ></Link>
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
          v-show="(link.url==datos.prev_page_url || link.url== datos.next_page_url) &&
                (link.label>4 && link.label<datos.last_page-3)"
        >...</Link>

      </template>
      <Link
          preserve-scroll
          class="ant-pagination-item"
          :href="datos.next_page_url"
        >Siguiente</Link>
      <Link
          preserve-scroll
          class="ant-pagination-item"
          :href="datos.last_page_url"
        >Ultima</Link>
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
  setup (props) {

  }
};
</script>
